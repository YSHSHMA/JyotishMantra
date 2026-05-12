<?php

namespace App\Http\Controllers\Admin\Pooja;

use App\Events\OrderStatusEvent;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmailPoojaJob;
use App\Models\Astrologer\Astrologer;
use App\Models\Chadhava;
use App\Models\Followsup;
use App\Models\Leads;
use App\Traits\PdfGenerator;
use App\Models\Product;
use App\Models\Vippooja;
use App\Models\Chadhava_orders;
use App\Models\ServiceTax;
use App\Models\ServiceTransaction;
use App\Models\User;
use App\Models\PanditPriceSlab;
use App\Models\INPanditWallet;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use App\Jobs\SendWhatsappMessage;
use App\Models\PanditTransectionPooja;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\View as PdfView;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\View;
use function App\Utils\payment_gateways; 
use Illuminate\Support\Facades\File;

class ChadhavaOrderController extends Controller
{
    use PdfGenerator;

    public function orders_list($status, Request $request)
    {
        $ordersQuery = Chadhava_orders::where('type', 'chadhava')->where('is_block', '!=', 9)->with(['leads', 'chadhava', 'customers', 'pandit']);

        if ($status !== 'all') {
            $ordersQuery->where('status', $status);
        }
        if ($request->filled('payment_status')) {
            $ordersQuery->where('payment_status', $request->payment_status);
        }
        if ($request->filled('service_id')) {
            $ordersQuery->where('service_id', $request->service_id);
        }
        if ($request->filled('order_status')) {
            $ordersQuery->where('order_status', $request->order_status);
        }

        $orders = $ordersQuery->orderBy('created_at', 'DESC')->get();

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $cutoff = $today->copy()->addDays(7); // 👈 For getNextAvailableDate()

        // Transform each order
        $orders->transform(function ($order) use ($today, $tomorrow, $cutoff) {
            $createdDate = Carbon::parse($order->created_at);
            $order->is_new = $createdDate->isToday() || $createdDate->isTomorrow();

            // ✅ Add next_available_date from chadhava relationship
            if ($order->chadhava) {
                $order->next_available_date = $order->chadhava->getNextAvailableDate($cutoff);
            } else {
                $order->next_available_date = null;
            }

            return $order;
        });

        $users = User::all();

        $todayDate = $today->toDateString();       // e.g., 2025-08-02
        $yesterdayDate = $today->copy()->subDay()->toDateString(); // e.g., 2025-08-01

        $todayCount = Chadhava_orders::where('type', 'chadhava')
            ->whereDate('created_at', $todayDate)
            ->where('status', 0)
            ->where('is_block', '!=', 9)
            ->where('order_status', 0)
            ->count();

        $tomorrowCount = Chadhava_orders::where('type', 'chadhava')
            ->whereDate('created_at', $yesterdayDate)
            ->where('status', 0)
            ->where('is_block', '!=', 9)
            ->where('order_status', 0)
            ->count();
        $paymentPublishedStatus = config('get_payment_publish_status');
        $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
        $payment_gateways_list = payment_gateways();
        $digital_payment = getWebConfig(name: 'digital_payment');
        return view('admin-views.pooja.chadhavaorder.list', compact('orders', 'users', 'todayCount', 'tomorrowCount','payment_gateways_list', 'paymentGatewayPublishedStatus', 'digital_payment'));
    }

    public function checked_order()
    {
        $chadhava = Chadhava_orders::where('checked', 0)->update(['checked' => 1]);
        if ($chadhava) {
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 400]);
        // return redirect()->route('admin.chadhava.order.list', ['status' => 'all']);
    }

    public function orders_details($id)
    {
        $serviceId = Chadhava_orders::select('service_id')->where('is_block', '!=', 9)->where('id', $id)->first()->service_id;
        $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day')
            ->where('primary_skills', 3)
            ->where('type', 'in house')
            ->where('status', 1)
            ->whereRaw("JSON_CONTAINS_PATH(is_pandit_chadhava, 'one', '$.\"$serviceId\"')")
            ->get();
        $details = Chadhava_orders::where('id', $id)->with('customers')->with('chadhava')->with('leads')->with('packages')->with('payments')->with('pandit')->with('product_leads.productsData')->first();
        $details['pooja_pandit'] = Astrologer::where('primary_skills', '3')->where('is_pandit_chadhava', 'like', '%' . $details['service_id'] . '%')->where('status', 1)->get();
        return view('admin-views.pooja.chadhavaorder.details', compact('details', 'inHouseAstrologers'));
    }


    public function orders_generate_invoice($id)
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $companyAddress = getWebConfig(name: 'shop_address');
        $companygst = getWebConfig(name: 'company_gst');
        $companypan = getWebConfig(name: 'company_pan');
        $details = Chadhava_orders::where('id', $id)->with('customers')->with('chadhava')->with('leads')->with('payments')->with('product_leads.productsData')->first();
        // dd($details);
        $mpdf_view = PdfView::make('admin-views.pooja.chadhavaorder.invoice', compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo', 'companyAddress', 'companygst', 'companypan'));
        $this->generatePdf($mpdf_view, 'order_invoice_', $details['order_id']);
    }

    public function orders_assign_pandit($id, Request $request)
    {
        $pandit = Chadhava_orders::where('id', $id)->where('type', 'chadhava')->where('is_block', '!=', 9)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->update(['pandit_assign' => $request->pandit_id]);
        if ($pandit) {
            Toastr::success(translate('pandit_assigned'));
            return back();
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    // Single certificated
    public function orders_status($id, Request $request)
    {
        $pooja = Chadhava_orders::where('id', $id)->where('is_block', '!=', 9)->with(['chadhava', 'customers', 'payments'])->first();
        if (!$pooja) {
            Toastr::error(translate('order_not_found'));
            return back();
        }
        $tax = ServiceTax::first();
        $commission = 0;
        if ($request->order_status == 1) {
            $certificate = Image::make(public_path('assets/back-end/img/certificate/chadhava\format\certificate-format.png'));
            $certificate->text(@ucwords($pooja['customers']['f_name']) . ' ' . @ucwords($pooja['customers']['l_name']), 950, 630, function ($font) {
                $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });
            $serviceName = wordwrap($pooja['chadhava']['name'], 65, "\n", false);
            $certificate->text($serviceName, 500, 815, function ($font) {
                $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                $font->size(40);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            $certificate->text(date('d/m/Y', strtotime($pooja['booking_date'])), 830, 994, function ($font) {
                $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                $font->size(40);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });
            $certificatePath = 'assets/back-end/img/certificate/chadhava/' . $pooja['order_id'] . '.jpg';
            $certificate->save(public_path($certificatePath));
            Chadhava_orders::where('id', $id)->update(['pooja_certificate' => $pooja['order_id'] . '.jpg']);
            $astrologer = Astrologer::where('id', $pooja['pandit_assign'])->first();
            if ($astrologer) {
                foreach (json_decode($astrologer['is_pandit_chadhava_commission']) as $key => $value) {
                    if ($key == $pooja['chadhava']['id']) {
                        $commission = $value;
                    }
                }
            }
            // dd($astrologer);
            $transaction = new ServiceTransaction();
            $transaction->astro_id = $pooja['pandit_assign'];
            $transaction->type = 'chadahva';
            $transaction->service_id = $request->service_id;
            $transaction->booking_date = $request->booking_date;
            $transaction->order_id = $pooja['order_id'];
            $transaction->txn_id = !empty($pooja['wallet_translation_id']) ? $pooja['wallet_translation_id'] : $pooja['payment_id'];

            $transaction->amount = $pooja['pay_amount'];
            $transaction->commission = $commission;
            $transaction->tax = $tax['online_pooja'] ?? 0;
            $transaction->save();
            Chadhava_orders::where('id', $id)->update([
                'status' => $request->order_status,
                'is_completed' => $request->order_status,
                'is_edited' => $request->order_status,
                'order_status' => $request->order_status,
                'pooja_certificate' => $certificatePath,
                'order_completed' => now(),
            ]);
            // dd($transaction);

        } elseif ($request->order_status == 2) {
            $status = Chadhava_orders::where('id', $id)->update([
                'order_status' => $request->order_status,
            ]);
            Toastr::success(translate('status_changed_successfully'));
            return back();
        } else {
            $status = Chadhava_orders::where('id', $id)->update([
                'order_status' => $request->order_status,
            ]);
            Toastr::success(translate('status_changed_successfully'));
            return back();
        }
        Toastr::success(translate('status_changed_successfully'));
        return redirect()->route('admin.chadhava.order.list', ['status' => $request->order_status]);
    }

    // Single  All Order Status Time,Live,Pooja
    public function status_times($id, Request $request)
    {
        $orders = Chadhava_orders::where('type', 'chadhava')->where('is_block', '!=', 9)->where('status', 0)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->with(['chadhava', 'customers'])->get();
        foreach ($orders as $order) {
            $order->schedule_time = $request->schedule_time;
            $order->schedule_created = now();
            $order->order_status = $request->order_status;
            $order->save();
        }
        // dd($orders);
        Toastr::success(translate('schedule_time_changed_successfully'));
        return back();
    }

    public function live_streams($id, Request $request)
    {
        // dd($request->all());
        $orders = Chadhava_orders::where('type', 'chadhava')->where('is_block', '!=', 9)->where('status', 0)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->with(['chadhava', 'customers'])->get();
        foreach ($orders as $order) {
            $order->live_stream = $request->live_stream;
            $order->live_created_stream = now();
            $order->order_status = $request->order_status;
            $order->save();
        }
        // dd($orders);
        Toastr::success(translate('live_stream_changed_successfully'));
        return back();
    }

    public function pooja_videos($id, Request $request)
    {
        // dd($request->all());
        $orders = Chadhava_orders::where('type', 'chadhava')->where('is_block', '!=', 9)->where('status', 0)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->with(['chadhava', 'customers'])->get();
        foreach ($orders as $order) {
            $order->pooja_video = $request->pooja_video;
            $order->video_created_sharing = now();
            $order->order_status = $request->order_status;
            $order->save();
        }
        Toastr::success(translate('Chadhava_video_changed_successfully'));

        return back();
    }
    public function cancel_poojas($id, Request $request)
    {
        // dd($request->all());
        $orders = Chadhava_orders::where('type', 'chadhava')->where('is_block', '!=', 9)->where('status', 0)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->with(['chadhava', 'customers'])->get();
        foreach ($orders as $pooja) {
            $pooja->order_canceled_reason = $request->order_canceled_reason;
            $pooja->order_canceled = now();
            $pooja->status = 2;
            $pooja->order_status = $request->order_status;
            $pooja->is_completed = 2;

            $pooja->save();
        }
        // dd($orders);
        Toastr::success(translate('pooja_Cancel_successfully'));
        return redirect()->route('admin.chadhava.order.list', ['2']);
    }
    //----------------------------------------Order by Pooja Change The option ------------------------------
    //All ORDER PENDIT ASSING
    // public function all_orders_assign_pandit(Request $request)
    // {
    //     $pandit = Chadhava_orders::where('type', 'chadhava')->where('service_id', $request->service_id)->where('booking_date', $request->booking_date)->where('payment_status', 1)->update(['pandit_assign' => $request->pandit_id]);
    //     if ($pandit) {
    //         Toastr::success(translate('pandit_assigned'));
    //         return back();
    //     }
    //     Toastr::error(translate('an_error_occured'));
    //     return back();
    // }

    public function all_orders_assign_pandit(Request $request)
    {
        $pandit = Chadhava_orders::where('type', 'chadhava')->where('is_block', '!=', 9)->where('service_id', $request->service_id)->where('booking_date', $request->booking_date)->where('payment_status', 1)
        ->get();
        if (!$pandit) {
            Toastr::error(translate('No matching order found or already assigned'));
            return back();
        }

        $panditId    = $request->pandit_id;
        $serviceId   = $request->service_id;
        $bookingDate = $request->booking_date;
        $totalOrders = $pandit->count();

        $tax = ServiceTax::value('online_pooja') ?? 0;
         // ================= PRICE FROM SLAB =================
         $slab = PanditPriceSlab::where('pandit_id', $panditId)
         ->where('service_id', $serviceId)
         ->where('type', 'chadhava')
         ->where('status', 1)
         ->where('min_qty', '<=', $totalOrders)
         ->where('max_qty', '>=', $totalOrders)
         ->first();

     if (!$slab) {
         Toastr::error(translate('Pandit price slab not found'));
         return back();
     }

     // Final pandit price
     if ($totalOrders == 1) {
         $panditPrice = $slab->single_price ?? 0;
     } else {
         $panditPrice = $slab->price ?? 0;
     }

        Toastr::error(translate('an_error_occured'));
        return back();
    }

    //ALL ORDER DETAILS
    public function all_orders_status(Request $request)
    {
        $orders = Chadhava_orders::where('payment_status', 1)->where('is_block', '!=', 9)->where('service_id', $request->service_id)->where('is_completed', 0)->where('booking_date', $request->booking_date)->with(['chadhava', 'customers', 'payments', 'customer'])->get();
        if (!$orders) {
            Toastr::error(translate('order_not_found'));
            return back();
        }
        $commission = 0;
        $tax = ServiceTax::first();
        $service = Chadhava::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
        }
        foreach ($orders as $pooja) {
            if ($request->order_status == 1) {
                 $certificate = Image::make(public_path('assets/back-end/img/certificate/chadhava/format/certificate-format.png'));
                $certificate->text(ucwords($pooja->customers->f_name . ' ' . $pooja->customers->l_name), 950, 630, function ($font) {
                    $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                    $font->size(100);
                    $font->color('#ffffff');
                    $font->align('center');
                    $font->valign('top');
                });

                $serviceName = wordwrap($pooja->chadhava->name, 65, "\n", false);
                $certificate->text($serviceName, 500, 815, function ($font) {
                    $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                    $font->size(40);
                    $font->color('#ffffff');
                    $font->align('left');
                    $font->valign('top');
                });

                $certificate->text(date('d/m/Y', strtotime($pooja->booking_date)), 830, 994, function ($font) {
                    $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                    $font->size(40);
                    $font->color('#ffffff');
                    $font->align('center');
                    $font->valign('top');
                });
                $certificatePath = 'assets/back-end/img/certificate/chadhava/' . $pooja['order_id'] . '.jpg';
                $certificate->save(public_path($certificatePath));
                // Service_order::where('service_id', $service_id)->update(['pooja_certificate' => $pooja['order_id'] . '.jpg']);
                // $astrologer = Astrologer::where('id', $pooja['pandit_assign'])->first();

                // if ($astrologer) {
                //     foreach (json_decode($astrologer['is_pandit_chadhava_commission']) as $key => $value) {
                //         if ($key == $pooja['chadhava']['id']) {
                //             $commission = $value;
                //         }
                //     }
                // }
                if (!$pooja['pandit_assign']) {
                    Toastr::error(translate('Please_selected_the_Pandit_ji'));
                    return back();
                }
                $packagePrice = $pooja->package_price;

                $amount = $pooja->pay_amount;

                $productAmount = $amount - $packagePrice;

                $transaction = new ServiceTransaction();
                $transaction->astro_id = $pooja['pandit_assign'];
                $transaction->type = 'chadhava';
                $transaction->order_id = $pooja['order_id'];
                $transaction->txn_id = !empty($pooja['wallet_translation_id']) ? $pooja['wallet_translation_id'] : $pooja['payment_id'];
                $transaction->service_id = $request->service_id;
                $transaction->booking_date = $request->booking_date;
                $transaction->amount = $pooja['pay_amount'];
                $transaction->package_price = $packagePrice;
                $transaction->product_amount = $productAmount;
                $transaction->tax = $tax['online_pooja'] ?? 0;
                $transaction->save();
                Chadhava_orders::where('order_id', $pooja['order_id'])->update([
                    'status' => $request->order_status,
                    'is_completed' => $request->order_status,
                    'is_edited' => $request->order_status,
                    'order_status' => $request->order_status,
                    'pooja_certificate' => $certificatePath,
                    'order_completed' => now(),
                ]);
            } elseif ($request->order_status == 2) {
                $status = Chadhava_orders::where('order_id', $pooja['order_id'])->update([
                    'order_status' => $request->order_status,
                ]);
            } else {
                $status = Chadhava_orders::where('order_id', $pooja['order_id'])->update([
                    'order_status' => $request->order_status,
                ]);
            }

            // =====================================================
            // 🔥 PANDIT WALLET ENTRY (SINGLE + BULK SLAB)
            // =====================================================
            if ($request->order_status == 1 && $orders->count() > 0) {

                $panditId    = $orders->first()->pandit_assign;
                $serviceId   = (int) $request->service_id;
                $bookingDate = $request->booking_date;

                if ($panditId) {

                    $totalOrders = $orders->count();

                    // Fetch applicable slab
                    $slab = PanditPriceSlab::where('pandit_id', $panditId)
                        ->where('service_id', $serviceId)
                        ->where('type', 'chadhava')
                        ->where('status', 1)
                        ->where('min_qty', '<=', $totalOrders)
                        ->where('max_qty', '>=', $totalOrders)
                        ->first();

                    if ($slab) {

                        $creditAmount = 0;
                        $entryType    = null;

                        if ($totalOrders == 1) {

                            // Single order
                            $creditAmount = $slab->single_price ?? 0;
                            $entryType    = 'single';

                        } elseif ($totalOrders > 1 && $totalOrders <= 1000) {

                            // Bulk fixed
                            $creditAmount = $slab->price ?? 0;
                            $entryType    = 'bulk_fixed';

                        } else {

                            // More than 1000
                            $creditAmount = $slab->price * $totalOrders;
                            $entryType    = 'bulk_per_order';
                        }

                        if ($creditAmount >= 0) {

                                $lastBalance = INPanditWallet::where('pandit_id', $panditId)
                                    ->orderBy('id', 'desc')
                                    ->value('balance') ?? 0;

                                INPanditWallet::create([
                                    'pandit_id'    => $panditId,
                                    'service_id'   => $serviceId,
                                    'booking_date' => $bookingDate,
                                    'total_orders' => $totalOrders,

                                    'single_price' => $slab->single_price,
                                    'slab_price'   => $slab->price,

                                    'amount'       => $creditAmount,
                                    'credit'       => $creditAmount,
                                    'debit'        => 0,
                                    'balance'      => $lastBalance + $creditAmount,

                                    'entry_type'   => $entryType, // single | bulk_fixed | bulk_per_order
                                    'type'         => 'chadhava',
                                ]);
                        }
                    }
                }
            }

            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'share_video' => $request->pooja_video ?? 'mahakal.com',
                    'customer_id' => $pooja->customer_id,
                    'attachment' => asset('public/' . $certificatePath),
                    'type' => 'text-with-media',
                    'orderId' => $pooja->order_id,
                    'chadhava_venue' => $service->chadhava_venue,
                    'amount' => webCurrencyConverter((float) ($pooja->pay_amount ?? 0)),
                    'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                ];

                SendWhatsappMessage::dispatch('chadhava', 'Completed', $messageData);

                // send email
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Chadhava::where('id', $pooja->service_id)->first();
                    $bookingDetails = \App\Models\Chadhava_orders::where('service_id', ($pooja->service_id ?? ""))
                        ->where('type', 'chadhava')->where('booking_date', ($pooja->booking_date ?? ""))->where('customer_id', ($pooja->customer_id ?? ""))->where('order_id', ($pooja->order_id ?? ""))->first();

                    $data = [
                        'type' => 'chadhava',
                        'email' => $userInfo->email,
                        'subject' => 'Chadhava Completed.',
                        'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-complete', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];
                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }

        foreach ($orders as $order) {
            if ($order->order_status == 1 && $order->customer) {
                event(new OrderStatusEvent(key: '1', type: 'puja', order: $order));
            }
        }
        Toastr::success(translate('status_changed_successfully'));
        return redirect()->route('admin.chadhava.order.orderbycompleted',['status' => 1]);
    }

    // Multiple All Order Status Time,Live,Pooja
    public function status_time(Request $request)
    {
        $orders = Chadhava_orders::where('status', 0)->where('is_block', '!=', 9)->where('type', 'chadhava')->where('payment_status', 1)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->with(['chadhava', 'customers', 'customer'])->get();

        foreach ($orders as $pooja) {
            $pooja->schedule_time = $request->schedule_time;
            $pooja->schedule_created = now();
            $pooja->order_status = $request->order_status;
            $pooja->save();
            $service = Chadhava::find($request->service_id);
            if (!$service) {
                Toastr::error(translate('Service not found.'));
                return back();
            }
        }

        //whatsap
        foreach ($orders as $pooja) {
            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'scheduled_time' => date('h:i A', strtotime($request->schedule_time)) ?? 'N/A',
                    'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                    'customer_id' => $pooja->customer_id,
                    'orderId' => $pooja->order_id,
                    'chadhava_venue' => $service->chadhava_venue,
                ];

                SendWhatsappMessage::dispatch('chadhava', 'Schedule', $messageData);

                //email
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Chadhava::where('id', $pooja->service_id)
                        ->first();
                    $bookingDetails = \App\Models\Chadhava_orders::where('service_id', ($pooja->service_id ?? ""))
                        ->where('type', 'chadhava')->where('booking_date', ($pooja->booking_date ?? ""))
                        ->where('customer_id', ($pooja->customer_id ?? ""))->where('order_id', ($pooja->order_id ?? ""))
                        ->first();
                    $data = [
                        'type' => 'chadhava',
                        'email' => $userInfo->email,
                        'subject' => 'Chadhava Time Scheduled',
                        'htmlContent' =>
                        \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-schedule-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];

                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }

        $orders->load('customer');

        foreach ($orders as $order) {
            if ($order->order_status == 3 && $order->customer) {
                event(new OrderStatusEvent(key: '3', type: 'puja', order: $order));
            }
        }
        Toastr::success(translate('schedule_time_changed_successfully'));

        return back();
        // Loop through the orders again to send WhatsApp messages
    }

    public function live_stream(Request $request)
    {

        $orders = Chadhava_orders::where('status', 0)->where('is_block', '!=', 9)->where('type', 'chadhava')->where('payment_status', 1)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->with(['chadhava', 'customers', 'customer'])->get();

        foreach ($orders as $pooja) {
            $pooja->live_stream = $request->live_stream;
            $pooja->live_created_stream = now();
            $pooja->order_status = $request->order_status;
            $pooja->save();
            $service = Chadhava::find($request->service_id);
            if (!$service) {
                Toastr::error(translate('Service not found.'));
                return back();
            }

            // Loop through the orders again to send WhatsApp messages
            foreach ($orders as $pooja) {
                if ($pooja->customers) {
                    $messageData = [
                        'service_name' => $service->name,
                        'live_stream' => $pooja->live_stream ?? 'mahakal.com',
                        'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                        'customer_id' => $pooja->customer_id,
                        'orderId' => $pooja->order_id,
                        'chadhava_venue' => $service->chadhava_venue ?? 'N/A',
                    ];

                    SendWhatsappMessage::dispatch('chadhava', 'Live Stream', $messageData);
                    // send email

                    $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                    $service_name = \App\Models\Chadhava::where('id', $pooja->service_id)
                        ->first();
                    $bookingDetails = \App\Models\Chadhava_orders::where('service_id', ($pooja->service_id ?? ""))
                        ->where('type', 'chadhava')
                        ->where('booking_date', ($pooja->booking_date ?? ""))
                        ->where('customer_id', ($pooja->customer_id ?? ""))
                        ->where('order_id', ($pooja->order_id ?? ""))
                        ->first();
                    if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                        $data = [
                            'type' => 'chadhava',
                            'email' => $userInfo->email,
                            'subject' => 'Chadhava Live Now',
                            'htmlContent' =>
                            \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-live-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                        ];

                        SendEmailPoojaJob::dispatch($data);
                    }
                }
            }
        }

        $orders->load('customer');

        foreach ($orders as $order) {
            if ($order->order_status == 4 && $order->customer) {
                event(new OrderStatusEvent(key: '4', type: 'puja', order: $order));
            }
        }
        Toastr::success(translate('live_stream_changed_successfully'));

        return back();
    }

    public function pooja_video(Request $request)
    {

        $orders = Chadhava_orders::where('status', 0)->where('is_block', '!=', 9)->where('type', 'chadhava')->where('payment_status', 1)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->with(['chadhava', 'customers', 'customer'])->get();

        foreach ($orders as $pooja) {
            $pooja->pooja_video = $request->pooja_video;
            $pooja->video_created_sharing = now();
            $pooja->order_status = $request->order_status;
            $pooja->save();
            $service = Chadhava::find($request->service_id);
            if (!$service) {
                Toastr::error(translate('Service not found.'));
                return back();
            }

            //whatsap
            foreach ($orders as $pooja) {
                if ($pooja->customers) {
                    $messageData = [
                        'service_name' => $service->name,
                        'share_video' =>  $request->pooja_video ?? 'mahakal.com',
                        'chadhava_venue' => $service->chadhava_venue,
                        'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                        'customer_id' => $pooja->customer_id,
                        'orderId' => $pooja->order_id,
                    ];

                    SendWhatsappMessage::dispatch('chadhava', 'Shared Video', $messageData);
                    //email
                    $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                    $service_name = \App\Models\Chadhava::where('id', $pooja->service_id)
                        ->first();

                    if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                        $bookingDetails = Chadhava_orders::where('service_id', $pooja->service_id)
                            ->where('type', 'chadhava')
                            ->where('booking_date', $pooja->booking_date)
                            ->where('customer_id', $pooja->customer_id)
                            ->where('order_id', $pooja->order_id)
                            ->first();

                        $data = [
                            'type' => 'chadhava',
                            'email' => $userInfo->email,
                            'subject' => 'Chadhava Video Link Shared',
                            'htmlContent' =>
                            \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-share-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                        ];
                        SendEmailPoojaJob::dispatch($data);
                    }
                }
            }
        }
        $orders->load('customer');

        foreach ($orders as $order) {
            if ($order->order_status == 5 && $order->customer) {
                event(new OrderStatusEvent(key: '5', type: 'puja', order: $order));
            }
        }
        Toastr::success(translate('Chadhava_video_changed_successfully'));

        return back();
    }

    public function cancel_pooja(Request $request)
    {
        // dd($request->all());
        $orders = Chadhava_orders::where('status', 0)->where('is_block', '!=', 9)->where('type', 'chadhava')->where('payment_status', 1)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->with(['chadhava', 'customers'])->get();

        foreach ($orders as $pooja) {
            $pooja->order_canceled_reason = $request->cancel_reason;
            $pooja->order_canceled = now();
            $pooja->status = 2;
            $pooja->order_status = $request->order_status;
            $pooja->is_completed = 2;

            $pooja->save();
            $service = Chadhava::find($request->service_id);
            if (!$service) {
                Toastr::error(translate('Service not found.'));
                return back();
            }

            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'customer_id' => $pooja->customer_id,
                    'order_canceled_reason' => $pooja->order_canceled_reason,
                    'order_id' => $pooja->order_id,
                ];
                SendWhatsappMessage::dispatch('chadhava', 'Chadhava Cancelled', $messageData);
            }
        }
        // dd($orders);
        Toastr::success(translate('chadhava_Cancel_successfully'));
        return redirect()->route('admin.chadhava.order.list', ['2']);
    }
    //----------------------------------------Order by Pooja Change The option ------------------------------

    public function lead_list(Request $request)
    {
        if ($request->has('searchValue')) {
            $leads = Leads::where('person_name', 'like', '%' . $request->searchValue . '%')->where('status', 1)->where('type', 'chadhava')->with('chadhava')->orderBy('created_at', 'DESC')->paginate(10);
        } else {
            $leads = Leads::where('status', 1)->where('type', 'chadhava')->with('chadhava', 'followby')->orderBy('created_at', 'DESC')->paginate(10);
        }
        // dd($leads);
        return view('admin-views.pooja.chadhavalead.list', compact('leads'));
    }
    public function lead_delete($id, Request $request)
    {
        $lead = Leads::where('id', $id)->first();
        if ($lead) {
            $lead->delete();
            Toastr::success(translate('lead_Delete_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }

    public function followup_store(Request $request)
    {
        $follows = [
            'customer_id' => $request->input('customer_id'),
            'pooja_id' => $request->input('pooja_id'),
            'lead_id' => $request->input('lead_id'),
            'type' => $request->input('type'),
            'follow_by' => $request->input('follow_by'),
            'follow_by_id' => $request->input('follow_by_id'),
            'last_date' => $request->input('last_date'),
            'message' => $request->input('message'),
            'next_date' => $request->input('next_date'),
        ];
        Followsup::create($follows);
        //  dd($followStore);
        Toastr::success(translate('lead_follow_up_successfully'));
        return back();
    }
    public function getFollowList($id)
    {
        $followlist = Followsup::where('lead_id', $id)->get();
        return response()->json($followlist);
    }
    // Order Pop Model To Show
    public function getOrderDetails(Request $request)
    {
        $orderId = $request->id;
        $order = Chadhava_orders::where('id', $orderId)->where('type', 'chadhava')->where('is_block', '!=', 9)
            ->with(['chadhava', 'astrologer', 'customers', 'product_leads'])
            ->first();
        return response()->json($order);
    }
    // Order By Pooja
    public function orders_by_chadhava(Request $request)
    {
        $chadhavaOrder = Chadhava_orders::where('type', 'chadhava')->where('is_block', '!=', 9)->where('status', 0)->where('is_completed', 0)->where('payment_status', 1)
            ->with(['leads', 'chadhava', 'customers', 'pandit'])->selectRaw('service_id, COUNT(*) as total_orders,pandit_assign,booking_date, COUNT(created_at) as booking_count, SUM(pay_amount) as total_amount, GROUP_CONCAT(members SEPARATOR "|") as members,order_status,created_at,customer_id,id')
            ->groupBy('service_id', 'booking_date', 'pandit_assign')->orderBy('total_orders', 'DESC')->get();
        $users = User::all();
        return view('admin-views.pooja.chadhavaorder.chadhavaByorder', compact('chadhavaOrder', 'users'));
    }

    public function downloadMemberList($service_id, $booking_date, $status)
    {
        $orders = Chadhava_orders::where('type', 'chadhava')->where('is_block', '!=', 9)->where('service_id', $service_id)->where('booking_date', $booking_date)->where('order_status', $status)->get();
        $chadhava = Chadhava::where('id', $service_id)->value('name');
        $chadhava_venue = Chadhava::where('id', $service_id)->value('chadhava_venue');
        $bookingDate = optional($orders->first())->booking_date;
        $mpdf_view = PdfView::make('admin-views.pooja.chadhavaorder.member-list', compact('orders', 'chadhava', 'bookingDate', 'chadhava_venue'));
        $this->generatePdf($mpdf_view, 'member-list', $bookingDate);
    }

    // All Single Order
    function all_single_order($service_id, $booking_date, $status)
    {
        if (!$service_id) {
            return redirect()->back()->with('error', 'No matching service found.');
        }
        $chadhava = Chadhava::find($service_id);
        if (!$chadhava) {
            return redirect()->back()->with('error', 'Service not found.');
        }
        $chadhavaOrders = Chadhava_orders::where('type', 'chadhava')->where('is_block', '!=', 9)->where('service_id', $service_id)->where('booking_date', $booking_date)
            ->where('order_status', $status)->with(['chadhava'])->get();
        $pending = Chadhava_orders::where('type', 'chadhava')->where('service_id', $service_id)->where('booking_date', $booking_date)->where('order_status', $status)->first();
        if (!$pending) {
            $pending = null;
        }

        // dd($chadhavaOrders);
        return view('admin-views.pooja.chadhavaorder.SingleOrder', compact('chadhavaOrders', 'chadhava', 'pending'));
    }

    public function single_orders_details($booking_date, $service_id, $status)
    {
        if (!$service_id) {
            return redirect()->back()->with('error', 'No matching service found for the given service ID.');
        }

        $service = Chadhava::find($service_id);

        if (!$service) {
            return redirect()->back()->with('error', 'Service not found.');
        }

        // Fetch service order safely
        $serviceOrder = Chadhava_orders::select('service_id')
            ->where('booking_date', $booking_date)
            ->where('is_block', '!=', 9)
            ->where('service_id', $service_id)
            ->first();

        if (!$serviceOrder) {
            return redirect()->back()->with('error', 'No matching service order found for the given booking date and service ID.');
        }
        $serviceId = $serviceOrder->service_id;

        //  Pandit IDs from PanditPriceSlab (MODEL based)
        $panditIds = PanditPriceSlab::where('service_id', $serviceId)
            ->where('type', 'chadhava')
            ->where('status', 1)
            ->pluck('pandit_id')
            ->unique()
            ->toArray();



        // In-house astrologers
        $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day')
            ->where('primary_skills', 3)
            ->where('type', 'in house')
            ->where('status', 1)
            ->whereIn('id', $panditIds)
            ->get();

        // Freelancer astrologers with price mapping
        $freelancerAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'is_pandit_vippooja')
            ->where('primary_skills', 3)
            ->where('type', 'freelancer')
            ->where('status', 1)
            ->whereIn('id', $panditIds)
            ->get()
            ->map(function ($astrologer) use ($serviceId) {

                // ✅ Price from PanditPriceSlab MODEL
                $slab = PanditPriceSlab::where('pandit_id', $astrologer->id)
                    ->where('service_id', $serviceId)
                    ->where('type', 'chadhava')
                    ->where('status', 1)
                    ->orderBy('min_qty')
                    ->first();

                return [
                    'id'                     => $astrologer->id,
                    'name'                   => $astrologer->name,
                    'is_pandit_pooja_per_day' => $astrologer->is_pandit_pooja_per_day,
                    'service_id'             => $serviceId,
                    'price'                  => $slab->single_price ?? $slab->price ?? null,
                ];
            });

        // Service order aggregation
        $details = Chadhava_orders::where('service_id', $service_id)
            ->where('booking_date', $booking_date)
            ->where('status', $status)
            ->where('is_block', '!=', 9)
            ->with(['chadhava', 'pandit', 'customers', 'product_leads'])
            ->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date, COUNT(booking_date) as booking_count, SUM(pay_amount) as total_amount, 
                GROUP_CONCAT(COALESCE(members, "") SEPARATOR "|") as members, 
                GROUP_CONCAT(COALESCE(gotra, "") SEPARATOR "|") as gotra, 
                GROUP_CONCAT(COALESCE(leads_id, "") SEPARATOR ",") as leads, 
                GROUP_CONCAT(COALESCE(order_id, "") SEPARATOR "|") as order_id, 
                order_status, created_at, schedule_time, schedule_created, live_stream, live_created_stream, 
                pooja_video, video_created_sharing, reject_reason, pooja_certificate, 
                order_completed, order_canceled, order_canceled_reason')
            ->groupBy('service_id', 'booking_date')
            ->orderBy('total_orders', 'DESC')
            ->first();

        if (!$details) {
            return redirect()->back()->with('error', 'No order details found for the provided inputs.');
        }

        return view('admin-views.pooja.chadhavaorder.SingleOrderdetails', compact('details', 'inHouseAstrologers', 'freelancerAstrologers'));
    }

    public function order_rejected_update(Request $request)
    {
        $orderId = $request->order_id;
        $serviceId = $request->service_id;
        $cust_details = [
            'booking_date' => $request->input('booking_date'),
            'reject_reason' => $request->input('reject_reason'),
            'status' => 0,
            'is_rejected' => 1,
            'is_completed' => 0,
            'order_status' => 0,
            'created_at' => now(),
        ];
        $updated = Chadhava_orders::where('order_id', $orderId)->update($cust_details);

        if ($updated) {
            Toastr::success(translate('chadhava_Reschedule_Successfully'));
        } else {
            Toastr::error(translate('chadhava_Reschedule_Unsuccessfully'));
        }
        return redirect()->route('admin.chadhava.order.list', ['0']);
    }

    public function orders_getpandit($serviceId, $bookingDate)
    {
        // ✅ Pandits list from PanditPriceSlab (Model-based)
        $panditIds = PanditPriceSlab::where('service_id', $serviceId)
            ->where('type', 'chadhava')
            ->where('status', 1)
            ->pluck('pandit_id')
            ->unique()
            ->toArray();

        // ================= INHOUSE PANDITS =================
        $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'type', 'is_pandit_chadhava')
            ->where('primary_skills', 3)->where('type', 'in house')->where('status', 1)
            ->whereIn('id', $panditIds)
            ->get()
            ->map(function ($astrologer) use ($bookingDate) {

                $checkastro = Chadhava_orders::where('pandit_assign', $astrologer->id)
                    ->where('booking_date', $bookingDate)
                    ->where('is_block', '!=', 9)
                    ->count();

                $completeastro = Chadhava_orders::where('pandit_assign', $astrologer->id)
                    ->where('status', 1)
                    ->where('booking_date', $bookingDate)
                    ->where('is_block', '!=', 9)
                    ->count();

                $astrologer->checkastro    = $checkastro;
                $astrologer->completeastro = $completeastro;

                return $astrologer;
            });
        // ================= FREELANCER PANDITS =================
        $freelancerAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'type', 'is_pandit_chadhava')
            ->where('primary_skills', 3)
            ->where('type', 'freelancer')
            ->whereIn('id', $panditIds)->get()
            ->map(function ($astrologer) use ($bookingDate, $serviceId) {

                $checkastro = Chadhava_orders::where('pandit_assign', $astrologer->id)
                    ->where('booking_date', $bookingDate)
                    ->where('is_block', '!=', 9)
                    ->count();

                $completeastro = Chadhava_orders::where('pandit_assign', $astrologer->id)
                    ->where('status', 1)
                    ->where('booking_date', $bookingDate)
                    ->where('is_block', '!=', 9)
                    ->count();

                // ✅ Price from PanditPriceSlab MODEL
                $slab = PanditPriceSlab::where('pandit_id', $astrologer->id)
                    ->where('service_id', $serviceId)
                    ->where('type', 'chadhava')
                    ->where('status', 1)
                    ->orderBy('min_qty')
                    ->first();

                $astrologer->checkastro    = $checkastro;
                $astrologer->completeastro = $completeastro;
                $astrologer->price         = $slab->single_price ?? $slab->price ?? null;

                return $astrologer;
            });
        return response()->json([
            'status' => 200,
            'inhouse' => $inHouseAstrologers,
            'freelancer' => $freelancerAstrologers
        ]);
    }

    public function send_whatsapp_leads($id)
    {
        $lead = Leads::where('id', $id)->first();
        $chadhavaName = Chadhava::where('status', 1)->where('id', $lead->service_id)->first();
        $customer = User::where('is_active', 1)->where('phone', $lead->person_phone)->first();

        if ($lead) {
            $message_data = [
                'service_name' => $chadhavaName->name,
                'chadhava_venue' => $chadhavaName->chadhava_venue,
                'type' => 'text-with-media',
                'attachment' => asset('/storage/app/public/chadhava/thumbnail/' . $chadhavaName->thumbnail),
                'link' => 'mahakal.com/chadhava/details/' . $chadhavaName->slug,
                'customer_id' => ($customer->id ?? ""),
            ];

            $messages =  Helpers::whatsappMessage('chadhava', 'Lead Message', $message_data);
            Leads::where('id', $id)->increment('whatsapp_hit');
            Toastr::success(translate('message_sent_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }
    // --------------Order----By---Completed------------19/06/2025----------------------------By Renuka Rudrawal And Er.Rahul Bathri
    public function orders_by_completed(Request $request)
    {
        $orders = Chadhava_orders::where('type', 'chadhava')->where('is_block', '!=', 9)->where('status', 1)->where('is_completed', 1)->with(['chadhava', 'customers', 'pandit'])->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date,order_completed, COUNT(created_at) as booking_count, SUM(pay_amount)  AS total_amount, COALESCE(GROUP_CONCAT(DISTINCT members SEPARATOR "|"), "") as members, order_status,  created_at, id')
            ->groupBy('service_id', 'booking_date', 'pandit_assign')->orderBy('total_orders', 'DESC')->get();
        $users = User::all();
        // dd($orders);
        return view('admin-views.pooja.chadhavaorder.orderbycompleted', compact('orders', 'users'));
    }
    public function completed_chadhava($booking_date, $service_id, $status)
    {
        $service = Chadhava::findOrFail($service_id);
        // 2. Get service_id from first matched service order
        $serviceOrder = Chadhava_orders::where('booking_date', $booking_date)->where('is_block', '!=', 9)->where('service_id', $service_id)->first();

        if (!$serviceOrder) {
            return back()->with('error', 'No service orders found for the given date and service.');
        }
        // 3. Get In-house astrologers (pandits)
        $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'type')->where('primary_skills', 3)
            ->where('type', 'in house')->where('status', 1)
            ->whereRaw("JSON_CONTAINS_PATH(is_pandit_chadhava, 'one', '$.\"$service_id\"')")
            ->get();

        // 4. Get Freelancer astrologers with dynamic price info
        $freelancerAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'is_pandit_chadhava')
            ->where('primary_skills', 3)->where('type', 'freelancer')
            ->where('status', 1)->whereRaw("JSON_CONTAINS_PATH(is_pandit_chadhava, 'one', '$.\"$service_id\"')")
            ->get()
            ->map(function ($astrologer) use ($service_id) {
                $isPanditPooja = json_decode($astrologer->is_pandit_chadhava, true);
                $price = $isPanditPooja[$service_id] ?? null;

                return [
                    'id' => $astrologer->id,
                    'name' => $astrologer->name,
                    'is_pandit_pooja_per_day' => $astrologer->is_pandit_pooja_per_day,
                    'service_id' => $service_id,
                    'price' => $price,
                ];
            });

        // 5. Get grouped order summary if status == 1 (completed)
        if ($status == '1') {
            $details = Chadhava_orders::where('service_id', $service_id)->where('is_block', '!=', 9)
                ->where('status', $status)->where('booking_date', $booking_date)->with(['chadhava', 'pandit'])
                ->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date, COUNT(booking_date) as booking_count,SUM(pay_amount) AS total_amount, 
                GROUP_CONCAT(COALESCE(order_id, "") SEPARATOR "|") as order_id, 
                GROUP_CONCAT(COALESCE(members, "") SEPARATOR "|") as members, 
                GROUP_CONCAT(COALESCE(gotra, "") SEPARATOR "|") as gotra, 
                GROUP_CONCAT(COALESCE(leads_id, "") SEPARATOR ",") as leads, 
                order_status, created_at, schedule_time, schedule_created, live_stream, live_created_stream, pooja_video,video_created_sharing,pooja_certificate, order_completed')
                ->groupBy('service_id', 'booking_date')
                ->orderBy('total_orders', 'DESC')
                ->first();
        } else {
            $details = null;
        }

        // 6. Get all service orders with relations
        $orders = Chadhava_orders::whereDate('booking_date', $booking_date)
            ->where('service_id', $service_id)
            ->where('is_block', '!=', 9)
            ->where('status', $status)
            ->with(['chadhava', 'pandit']) // eager loading
            ->get();
        // dd($service);
        // 7. Pass all data to the view
        return view('admin-views.pooja.chadhavaorder.complete-chadhava', compact(
            'orders',
            'details',
            'inHouseAstrologers',
            'freelancerAstrologers',
            'service',
        ));
    }
    public function update_chadhava_video(Request $request)
    {

        $orders = Chadhava_orders::where('order_status', $request->order_status)->where('is_block', '!=', 9)->where('type', 'chadhava')->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->with(['chadhava', 'customers', 'customer'])->get();

        foreach ($orders as $pooja) {
            $pooja->pooja_video = $request->pooja_video;
            $pooja->video_created_sharing = now();
            $pooja->live_created_stream = now();
            $pooja->order_status = $request->order_status;
            $pooja->save();
        }
        // dd($pooja);
        $service = Chadhava::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
        }

        //whatsap
        foreach ($orders as $pooja) {
            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'share_video' =>  $request->pooja_video ?? 'mahakal.com',
                    'chadhava_venue' => $service->chadhava_venue,
                    'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                    'customer_id' => $pooja->customer_id,
                    'orderId' => $pooja->order_id,
                ];

                SendWhatsappMessage::dispatch('chadhava', 'Shared Video', $messageData);
                //email
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                $service_name = \App\Models\Chadhava::where('id', $pooja->service_id)
                    ->first();

                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $bookingDetails = Chadhava_orders::where('service_id', $pooja->service_id)
                        ->where('type', 'chadhava')
                        ->where('is_block', '!=', 9)
                        ->where('booking_date', $pooja->booking_date)
                        ->where('customer_id', $pooja->customer_id)
                        ->where('order_id', $pooja->order_id)
                        ->first();

                    $data = [
                        'type' => 'chadhava',
                        'email' => $userInfo->email,
                        'subject' => 'Chadhava Video Link Shared',
                        'htmlContent' =>
                        \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-share-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];
                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }
        $orders->load('customer');

        foreach ($orders as $order) {
            if ($order->order_status == 5 && $order->customer) {
                event(new OrderStatusEvent(key: '5', type: 'puja', order: $order));
            }
        }
        Toastr::success(translate('pooja_video_updated_link_successfully'));

        return back();
    }

    public function getCustomerOrders(Request $request)
    {
        $orders = Chadhava_orders::where('customer_id', $request->customer_id)->where('type','chadhava')->where('is_block', '!=', 9)
            ->select('order_id')
            ->get();
        return response()->json([
            'orders' => $orders
        ]);
    }

    public function blockSelectedOrders(Request $request)
    {
     
        $orderIds = is_array($request->order_ids) 
        ? $request->order_ids 
        : [$request->order_ids];

        Chadhava_orders::whereIn('order_id', $orderIds)
            ->where('type', 'chadhava')
            ->update(['is_block' => 9]);

        return response()->json([
            'message' => 'Selected orders have been blocked successfully.'
        ]);
    }

    public function update_certificate(Request $request)
    {
        $orders = Chadhava_orders::where('is_block', '!=', 9)
            ->whereDate('booking_date', $request->booking_date)
            ->where('service_id', $request->service_id)
            ->get();

        if ($orders->isEmpty()) {
            Toastr::error('Order not found');
            return back();
        }

        $oldPath = $orders->first()->pooja_certificate;

        if ($request->hasFile('pooja_certificate')) {

            $file = $request->file('pooja_certificate');
            $fileName = basename($oldPath);

            $directory = public_path('assets/back-end/img/certificate/chadhava');

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true);
            }

            $file->move($directory, $fileName);

            $oldPath = 'assets/back-end/img/certificate/chadhava/' . $fileName;
        }

        foreach ($orders as $order) {
            $order->pooja_certificate = $oldPath;
            $order->order_status = $request->order_status;
            $order->save();
        }

        Toastr::success('Chadhava certificate updated successfully');
        return back();
    }

}
