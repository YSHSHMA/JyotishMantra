<?php

namespace App\Http\Controllers\Admin\Pooja;

use App\Events\OrderStatusEvent;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmailPoojaJob;
use App\Models\Astrologer\Astrologer;
use App\Models\Followsup;
use App\Models\Leads;
use App\Traits\PdfGenerator;
use App\Models\Product;
use App\Models\Service;
use App\Models\Service_order;
use App\Models\ServiceTax;
use App\Models\Devotee;
use App\Models\PoojaRecords;
use App\Models\PanditTransectionPooja;
use App\Models\ServiceTransaction;
use App\Models\User;
use App\Models\PanditPriceSlab;
use App\Models\INPanditWallet;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\View as PdfView;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\View;
use App\Jobs\SendWhatsappMessage;
use App\Models\Prashad_deliverys;
use Carbon\Carbon;
use function App\Utils\payment_gateways;
use Illuminate\Support\Facades\File;

class PoojaOrderController extends Controller
{
    use PdfGenerator;

    public function orders_list($status, Request $request)
    {
        $ordersQuery = Service_order::where('type', 'pooja')->where('is_block', '!=', 9)->with(['leads', 'packages', 'services', 'customers', 'pandit']);

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
        $orders->transform(function ($order) use ($today, $tomorrow) {
            $createdDate = Carbon::parse($order->created_at);
            $order->is_new = $createdDate->isToday() || $createdDate->isTomorrow();
            return $order;
        });
        $users = User::all();
        $paymentPublishedStatus = config('get_payment_publish_status');
        $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
        $payment_gateways_list = payment_gateways();
        $digital_payment = getWebConfig(name: 'digital_payment');
        return view('admin-views.pooja.order.list', compact('orders', 'users','paymentPublishedStatus','paymentGatewayPublishedStatus','payment_gateways_list','digital_payment'));
    }

    public function checked_order()
    {
        $pooja = Service_order::where('checked', 0)->update(['checked' => 1]);
        if ($pooja) {
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 400]);
        // return redirect()->route('admin.pooja.orders.list', ['status' => 'all']);
    }

    public function orders_details($id)
    {
        // Service_order::where('type', 'pooja')->where('booking_date', '<', date('Y-m-d', strtotime('1 day')))->update(['order_status' => 6, 'status' => 6]);
        $serviceId = Service_order::select('service_id')->where('id', $id)->first()->service_id;
        // Get in-house astrologers
        $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day')
            ->where('primary_skills', 3)
            ->where('type', 'in house')
            ->where('status', 1)
            ->whereRaw("JSON_CONTAINS_PATH(is_pandit_pooja, 'one', '$.\"$serviceId\"')")
            ->get();

        // Get freelancer astrologers
        $freelancerAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'is_pandit_pooja')
            ->where('primary_skills', 3)
            ->where('type', 'freelancer')
            ->where('status', 1)
            ->whereRaw("JSON_CONTAINS_PATH(is_pandit_pooja, 'one', '$.\"$serviceId\"')")
            ->get()
            ->map(function ($astrologer) use ($serviceId) {
                $isPanditPooja = json_decode($astrologer->is_pandit_pooja, true);
                $price = $isPanditPooja[$serviceId] ?? null;

                return [
                    'id' => $astrologer->id,
                    'name' => $astrologer->name,
                    'is_pandit_pooja_per_day' => $astrologer->is_pandit_pooja_per_day,
                    'service_id' => $serviceId,
                    'price' => $price,
                ];
            });

        $details = Service_order::where('id', $id)->with('customers')->with('services')->with('leads')->with('packages')->with('payments')->with('astrologer')->with('product_leads.productsData')->first();
        $details['pooja_pandit'] = Astrologer::where('primary_skills', '3')->where('is_pandit_pooja', 'like', '%' . $details['service_id'] . '%')->where('status', 1)->get();
        return view('admin-views.pooja.order.details', compact('details', 'inHouseAstrologers', 'freelancerAstrologers'));
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
        $details = Service_order::where('id', $id)->with('customers')->with('services')->with('leads')->with('packages')->with('payments')->with('product_leads.productsData')->first();
        // dd($details);
        $mpdf_view = PdfView::make('admin-views.pooja.order.invoice', compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo', 'companyAddress', 'companygst', 'companypan'));
        $this->generatePdf($mpdf_view, 'order_invoice_', $details['order_id']);
    }

    // public function all_orders_assign_pandit(Request $request)
    // {
    //     // Step 1: Fetch Orders Matching the Criteria
    //     $orders = Service_order::where('type', 'pooja')
    //         ->where('service_id', $request->service_id)
    //         ->where('booking_date', $request->booking_date)
    //         ->get();

    //     if ($orders->isEmpty()) {
    //         Toastr::error(translate('No matching orders found'));
    //         return back();
    //     }

    //     // Step 2: Get Pandit Data
    //     $getPanditData = Astrologer::where('id', $request->pandit_id)->first();
    //     if (!$getPanditData || !$getPanditData->is_pandit_pooja) {
    //         Toastr::error(translate('Pandit or price data not found'));
    //         return back();
    //     }

    //     // Step 3: Decode price list
    //     $priceList = json_decode($getPanditData->is_pandit_pooja, true);
    //     $price = isset($priceList[$request->service_id]) ? $priceList[$request->service_id] : 0;

    //     // Step 4: Loop through orders
    //     foreach ($orders as $order) {

    //         // Step 5: Update pandit_assign in service_orders table first
    //         $order->update(['pandit_assign' => $request->pandit_id]);

    //         // Update or Create transaction for each order
    //         $existingTransaction = PanditTransectionPooja::where('service_id', $order->service_id)
    //             ->where('booking_date', $order->booking_date)
    //             ->where('service_order_id', $order->order_id)
    //             ->first();

    //         if ($existingTransaction) {
    //             // Update old transaction with new pandit_id and price
    //             $existingTransaction->update([
    //                 'pandit_id'     => $request->pandit_id,
    //                 'pandit_amount' => $price,
    //             ]);
    //         } else {
    //             // Insert new transaction with latest pandit_id
    //             PanditTransectionPooja::create([
    //                 'pandit_id'         => $request->pandit_id,
    //                 'service_id'        => $order->service_id,
    //                 'service_order_id'  => $order->order_id,
    //                 'type'              => 'pooja',
    //                 'pandit_amount'     => $price,
    //                 'booking_date'      => $order->booking_date,
    //             ]);
    //         }
    //     }

    //     Toastr::success(translate('Pandit reassigned and transactions updated/created successfully'));
    //     return back();
    // }

    // Single certificated
    public function orders_status($id, Request $request)
    {
        $pooja = Service_order::where('id', $id)->where('payment_status', 1)->with(['services', 'customers', 'payments'])->first();
        if (!$pooja) {
            Toastr::error(translate('order_not_found'));
            return back();
        }
        $commission = 0;
        $tax = ServiceTax::first();
        // dd($pooja);
        if ($request->order_status == 1) {
            $certificate = Image::make(public_path('assets/back-end/img/certificate/pooja/format/certificate-format.png'));
            $certificate->text(@ucwords($pooja['customers']['f_name']) . ' ' . @ucwords($pooja['customers']['l_name']), 950, 630, function ($font) {
                $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });
            $serviceName = wordwrap($pooja['services']['name'], 65, "\n", false);
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
            $certificatePath = 'assets/back-end/img/certificate/pooja/' . $pooja['order_id'] . '.jpg';
            $certificate->save(public_path($certificatePath));
            Service_order::where('id', $id)->update(['pooja_certificate' => $pooja['order_id'] . '.jpg']);
            $astrologer = Astrologer::where('id', $pooja['pandit_assign'])->first();
            if ($astrologer) {
                foreach (json_decode($astrologer['is_pandit_pooja_commission']) as $key => $value) {
                    if ($key == $pooja['services']['id']) {
                        $commission = $value;
                    }
                }
            }
            if (!$pooja['pandit_assign']) {
                Toastr::error(translate('Please_selected_the_Pandit_ji'));
                return back();
            }
            $transaction = new ServiceTransaction();
            // dd($pooja);
            $transaction->astro_id = $pooja['pandit_assign'];
            $transaction->type = $pooja['services']['product_type'];
            $transaction->order_id = $pooja['order_id'];
            $transaction->txn_id = !empty($pooja['wallet_translation_id']) ? $pooja['wallet_translation_id'] : $pooja['payment_id'];

            $transaction->amount = $pooja['pay_amount'];
            $transaction->commission = $commission;
            $transaction->tax = $tax['online_pooja'] ?? 0;
            $transaction->save();
            // dd($transaction);
            Service_order::where('id', $id)->update([
                'status' => $request->order_status,
                'is_completed' => $request->order_status,
                'order_status' => $request->order_status,
                'is_edited' => $request->order_status,
                'pooja_certificate' => $certificatePath,
                'order_completed' => now(),
            ]);
            Toastr::success(translate('status_changed_successfully'));
            return redirect()->route('admin.pooja.orders.list', ['status' => 1]);
        } elseif ($request->order_status == 2) {
            Service_order::where('id', $id)->update([
                'order_status' => $request->order_status,
            ]);
            Toastr::success(translate('status_changed_successfully'));
            return back();
        } else {
            Service_order::where('id', $id)->update([
                'order_status' => $request->order_status,
            ]);
            if (!$pooja['pandit_assign']) {
                Toastr::error(translate('Please_selected_the_Pandit_ji'));
                return back();
            }
            Toastr::success(translate('status_changed_successfully'));
            return back();
        }
    }
    // Prashad Order Status
    public function prashad_orders_status($id, Request $request)
    {
        $pooja = Service_order::where('id', $id)->with(['services', 'customers', 'payments'])->first();
        if (!$pooja) {
            Toastr::error(translate('order_not_found'));
            return back();
        }
        if ($request->prashad_status == 1) {
            Service_order::where('id', $id)->update([
                'prashad_status' => $request->prashad_status,
            ]);
            Toastr::success(translate('status_changed_successfully'));
        } elseif ($request->prashad_status == 2) {
            Service_order::where('id', $id)->update([
                'prashad_status' => $request->prashad_status
            ]);
            Toastr::success(translate('status_changed_successfully'));
            return back();
        } elseif ($request->prashad_status == 3) {
            Service_order::where('id', $id)->update([
                'prashad_status' => $request->prashad_status
            ]);
            Toastr::success(translate('status_changed_successfully'));
            return back();
        } elseif ($request->prashad_status == 4) {
            Service_order::where('id', $id)->update([
                'prashad_status' => $request->prashad_status
            ]);
            Toastr::success(translate('status_changed_successfully'));
            return redirect()->route('admin.pooja.orders.list', ['status' => 1]);
        } elseif ($request->prashad_status == 5) {
            Service_order::where('id', $id)->update([
                'prashad_status' => $request->prashad_status
            ]);
            Toastr::success(translate('status_changed_successfully'));
            return back();
        } else {
            Service_order::where('id', $id)->update([
                'prashad_status' => $request->prashad_status
            ]);
            Toastr::success(translate('status_changed_successfully'));
            return back();
        }
    }

    // Single Order Status Time,Live,Pooja
    public function status_times($id, Request $request)
    {
        // dd($request->all());
        $orders = Service_order::where('id', $id)->where('payment_status', 1)
            ->where('status', 0)->with(['services', 'customers'])->get();

        foreach ($orders as $order) {
            $order->schedule_time = $request->schedule_time;
            $order->order_status = $request->order_status;
            $order->schedule_created = now();
            $order->save();
        }
        // dd($orders);
        Toastr::success(translate('schedule_time_changed_successfully'));

        return back();
    }

    public function live_streams($id, Request $request)
    {
        // dd($request->all());
        $orders = Service_order::where('id', $id)->where('payment_status', 1)
            ->where('status', 0)->with(['services', 'customers'])->get();
        foreach ($orders as $order) {
            $order->live_stream = $request->live_stream;
            $order->order_status = $request->order_status;
            $order->live_created_stream = now();
            $order->save();
        }
        // dd($orders);
        Toastr::success(translate('live_stream_changed_successfully'));
        return back();
    }

    public function pooja_videos($id, Request $request)
    {
        // dd($request->all());
        $orders = Service_order::where('id', $id)->where('payment_status', 1)
            ->where('status', 0)->with(['services', 'customers'])->get();
        foreach ($orders as $order) {
            $order->pooja_video = $request->pooja_video;
            $order->order_status = $request->order_status;
            $order->video_created_sharing = now();
            $order->save();
        }
        Toastr::success(translate('pooja_video_changed_successfully'));

        return back();
    }
    public function cancel_poojas($id, Request $request)
    {
        // dd($request->all());
        $orders = Service_order::where('id', $id)->where('payment_status', 1)
            ->where('status', 0)->with(['services', 'customers'])->get();
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
        return redirect()->route('admin.pooja.orders.list', ['2']);
    }
    //----------------------------------------Order by Pooja Change The option ------------------------------
    //All ORDER PENDIT ASSING
    // public function all_orders_assign_pandit(Request $request)
    // {
    //     $orders = Service_order::where('type', 'pooja')
    //         ->where('service_id', $request->service_id)
    //         ->where('booking_date', $request->booking_date)
    //         ->where('payment_status', 1)
    //         ->where('is_block', '!=', 9)
    //         ->get();

    //     if ($orders->isEmpty()) {
    //         Toastr::error(translate('No matching orders found'));
    //         return back();
    //     }

    //     $getPanditData = Astrologer::where('id', $request->pandit_id)->first();
    //     if (!$getPanditData || !$getPanditData->is_pandit_pooja) {
    //         Toastr::error(translate('Pandit or price data not found'));
    //         return back();
    //     }

    //     $priceList = json_decode($getPanditData->is_pandit_pooja, true);
    //     $price = $priceList[$request->service_id] ?? 0;
    //     $commissionList = json_decode($getPanditData->is_pandit_pooja_commission, true);
    //     $commission = $commissionList[$request->service_id] ?? 0;
    //     $tax = ServiceTax::value('online_pooja');

    //     foreach ($orders as $order) {
    //         // Ensure Pandit is assigned correctly
    //         $order->pandit_assign = $request->pandit_id;
    //         $order->save();

    //         // Handle Pandit Transactions
    //         $existingTransaction = PanditTransectionPooja::where('service_id', $order->service_id)
    //             ->where('booking_date', $order->booking_date)
    //             ->where('service_order_id', $order->order_id)
    //             ->first();

    //         if ($existingTransaction) {
    //             $existingTransaction->update([
    //                 'pandit_id'     => $request->pandit_id,
    //                 'pandit_amount' => $price,
    //                 'admin_commission' => $commission,
    //                 'govt_tax' => $tax,
    //             ]);
    //         } else {
    //             PanditTransectionPooja::create([
    //                 'pandit_id'        => $request->pandit_id,
    //                 'service_id'       => $order->service_id,
    //                 'service_order_id' => $order->order_id,
    //                 'type'             => 'pooja',
    //                 'pandit_amount'    => $price,
    //                 'booking_date'     => $order->booking_date,
    //                 'order_amount'    => $order->pay_amount,
    //                 'admin_commission' => $commission,
    //                 'govt_tax' => $tax,
    //             ]);
    //         }
    //     }

    //     Toastr::success(translate('Pandit reassigned and transactions updated/created successfully'));
    //     return back();
    // }


    public function all_orders_assign_pandit(Request $request)
    {
        $orders = Service_order::where('type', 'pooja')
            ->where('service_id', $request->service_id)
            ->where('booking_date', $request->booking_date)
            ->where('payment_status', 1)
            ->where('is_block', '!=', 9)
            ->get();

        if ($orders->isEmpty()) {
            Toastr::error(translate('No matching orders found'));
            return back();
        }

        $panditId    = $request->pandit_id;
        $serviceId   = $request->service_id;
        $bookingDate = $request->booking_date;
        $totalOrders = $orders->count();

        $tax = ServiceTax::value('online_pooja') ?? 0;

        // ================= PRICE FROM SLAB =================
        $slab = PanditPriceSlab::where('pandit_id', $panditId)
            ->where('service_id', $serviceId)
            ->where('type', 'pooja')
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

        foreach ($orders as $order) {

            // Assign pandit
            $order->pandit_assign = $panditId;
            $order->save();

            // ================= TRANSACTION =================
            $existingTransaction = PanditTransectionPooja::where('service_order_id', $order->order_id)
                ->first();

            if ($existingTransaction) {
                $existingTransaction->update([
                    'pandit_id'        => $panditId,
                    'pandit_amount'    => $panditPrice,
                    'admin_commission' => 0,   // ❌ commission removed
                    'govt_tax'         => $tax,
                ]);
            } else {
                PanditTransectionPooja::create([
                    'pandit_id'        => $panditId,
                    'service_id'       => $order->service_id,
                    'service_order_id' => $order->order_id,
                    'type'             => 'pooja',
                    'pandit_amount'    => $panditPrice,
                    'booking_date'     => $bookingDate,
                    'order_amount'     => $order->pay_amount,
                    'admin_commission' => 0,   // ❌ commission removed
                    'govt_tax'         => $tax,
                ]);
            }
        }

        Toastr::success(translate('Pandit assigned using slab pricing successfully'));
        return back();
    }


    //ALL ORDER DETAILS
    // public function all_orders_status(Request $request)
    // {
    //     $orders = Service_order::where('type', 'pooja')->where('is_block', '!=', 9)->where('service_id', $request->service_id)->where('booking_date', $request->booking_date)->where('payment_status', 1)->with(['services', 'customers', 'payments', 'customer'])->get();
    //     if (!$orders) {
    //         Toastr::error(translate('order_not_found'));
    //         return back();
    //     }
    //     $commission = 0;
    //     $tax = ServiceTax::first();
    //     $service = Service::find($request->service_id);
    //     if (!$service) {
    //         Toastr::error(translate('Service not found.'));
    //         return back();
    //     }


    //     // dd($orders);
    //     foreach ($orders as $pooja) {
    //         if ($request->order_status == 1) {
    //             $certificate = Image::make(public_path('assets/back-end/img/certificate/format/certificate-format.png'));
    //             $certificate->text(ucwords($pooja->customers->f_name . ' ' . $pooja->customers->l_name), 950, 630, function ($font) {
    //                 $font->file(public_path('fonts/NotoSans-Regular.ttf'));
    //                 $font->size(100);
    //                 $font->color('#ffffff');
    //                 $font->align('center');
    //                 $font->valign('top');
    //             });
    //             $serviceName = wordwrap($pooja->services->name, 65, "\n", false);
    //             $certificate->text($serviceName, 500, 815, function ($font) {
    //                 $font->file(public_path('fonts/NotoSans-Regular.ttf'));
    //                 $font->size(40);
    //                 $font->color('#ffffff');
    //                 $font->align('left');
    //                 $font->valign('top');
    //             });

    //             $certificate->text(date('d/m/Y', strtotime($pooja->booking_date)), 830, 994, function ($font) {
    //                 $font->file(public_path('fonts/NotoSans-Regular.ttf'));
    //                 $font->size(40);
    //                 $font->color('#ffffff');
    //                 $font->align('center');
    //                 $font->valign('top');
    //             });
                
    //             $certificateDir = public_path('assets/back-end/img/certificate/pooja/');
    //             if (!file_exists($certificateDir)) {
    //                 mkdir($certificateDir, 0777, true);
    //             }

    //             $certificate = Image::make(public_path('assets/back-end/img/certificate/pooja/format/certificate-format.png'));
    //             $certificate->save(public_path($certificatePath));
    //             // Service_order::where('order_id', $pooja['order_id'])->update(['pooja_certificate' => $pooja['order_id'] . '.jpg']);
    //             $astrologer = Astrologer::where('id', $pooja['pandit_assign'])->first();
    //             // dd($astrologer);
    //             if ($astrologer) {
    //                 foreach (json_decode($astrologer['is_pandit_pooja_commission']) as $key => $value) {
    //                     if ($key == $pooja['services']['id']) {
    //                         $commission = $value;
    //                     }
    //                 }
    //             }
    //             if (!$pooja['pandit_assign']) {
    //                 Toastr::error(translate('Please_selected_the_Pandit_ji'));
    //                 return back();
    //             }
    //             $transaction = new ServiceTransaction();
    //             $transaction->astro_id = $pooja['pandit_assign'];
    //             $transaction->type = $pooja['services']['product_type'];
    //             $transaction->order_id = $pooja['order_id'];
    //             $transaction->txn_id = !empty($pooja['wallet_translation_id']) ? $pooja['wallet_translation_id'] : $pooja['payment_id'];
    //             $transaction->service_id =  $request->service_id;
    //             $transaction->booking_date = $request->booking_date;
    //             // dd($transaction->txn_id);
    //             $transaction->amount = $pooja['pay_amount'];
    //             $transaction->commission = $commission;
    //             $transaction->tax = $tax['online_pooja'];
    //             $transaction->save();
    //             Devotee::where('service_order_id', $pooja->order_id)->update([
    //                 'status' => 1,
    //             ]);

    //             PanditTransectionPooja::where('service_order_id', $pooja->order_id)->update([
    //                 'status' => 1,
    //             ]);
    //             PoojaRecords::where('service_order_id', $pooja->order_id)->update([
    //                 'status' => 1,
    //             ]);
    //             Service_order::where('order_id', $pooja['order_id'])->where('is_block', '!=', 9)->update([
    //                 'status' => $request->order_status,
    //                 'is_completed' => $request->order_status,
    //                 'is_edited' => $request->order_status,
    //                 'order_status' => $request->order_status,
    //                 'pooja_certificate' => $certificatePath,
    //                 'order_completed' => now(),
    //             ]);
    //             Prashad_deliverys::where('order_id', $pooja['order_id'])->where('service_id', $request->service_id)->update([
    //                 'pooja_status' => $request->order_status,
    //                 'status' => $request->order_status,
    //                 'order_completed' => now(),
    //             ]);
    //         } elseif ($request->order_status == 2) {
    //             Service_order::where('order_id', $pooja['order_id'])->where('is_block', '!=', 9)->update([
    //                 'order_status' => $request->order_status,
    //             ]);
    //         } else {
    //             Service_order::where('order_id', $pooja['order_id'])->where('is_block', '!=', 9)->update([
    //                 'order_status' => $request->order_status,
    //             ]);
    //         }
    //         if ($pooja->customers) {
    //             $messageData = [
    //                 'service_name' => $service->name,
    //                 'share_video' => $request->pooja_video ?? 'mahakal.com',
    //                 'customer_id' => $pooja->customer_id,
    //                 'puja_venue' => $service->pooja_venue,
    //                 'attachment' => asset('public/' . $certificatePath),
    //                 'type' => 'text-with-media',
    //                 'orderId' => $pooja->order_id,
    //                 'amount' => webCurrencyConverter((float) ($pooja->pay_amount - ($pooja->coupon_amount ?? 0))),
    //                 'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
    //             ];

    //             SendWhatsappMessage::dispatch('whatsapp', 'Completed', $messageData);

    //             // send email
    //             $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
    //             if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
    //                 $service_name = \App\Models\Service::where('id', $pooja->service_id)
    //                     ->where('product_type', 'pooja')
    //                     ->first();
    //                 $bookingDetails = \App\Models\Service_order::where('service_id', ($pooja->service_id ?? ""))
    //                     ->where('type', 'pooja')->where('booking_date', ($pooja->booking_date ?? ""))
    //                     ->where('customer_id', ($pooja->customer_id ?? ""))->where('order_id', ($pooja->order_id ?? ""))
    //                     ->first();

    //                 $data = [
    //                     'type' => 'pooja',
    //                     'email' => $userInfo->email,INPanditWallet
    //                     'subject' => 'Puja Completed',
    //                     'htmlContent' =>
    //                     \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-complete', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
    //                 ];

    //                 Helpers::emailSendMessage($data);
    //             }
    //         }
    //     }

    //     foreach ($orders as $order) {
    //         if ($order->order_status == 1 && $order->customer) {
    //             event(new OrderStatusEvent(key: '1', type: 'puja', order: $order));
    //         }
    //     }
    //     Toastr::success(translate('status_changed_successfully'));
    //     return redirect()->route('admin.pooja.orders.orderbycompleted', ['status' => 1]);
    // }
    //ALL ORDER DETAILS
    public function all_orders_status(Request $request)
    {
        $orders = Service_order::where('type', 'pooja')->where('is_block', '!=', 9)->where('service_id', $request->service_id)->where('is_completed', 0)->where('booking_date', $request->booking_date)->where('payment_status', 1)->with(['services', 'customers', 'payments', 'customer'])->get();
        if (!$orders) {
            Toastr::error(translate('order_not_found'));
            return back();
        }
        $commission = 0;
        $tax = ServiceTax::first();
        $service = Service::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
        }

        foreach ($orders as $pooja) {
            if ($request->order_status == 1) {
                $certificate = Image::make(public_path('assets/back-end/img/certificate/pooja/format/certificate-format.png'));
                $certificate->text(ucwords($pooja->customers->f_name . ' ' . $pooja->customers->l_name), 950, 630, function ($font) {
                    $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                    $font->size(100);
                    $font->color('#ffffff');
                    $font->align('center');
                    $font->valign('top');
                });
                $serviceName = wordwrap($pooja->services->name, 65, "\n", false);
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
                
                $certificateDir = public_path('assets/back-end/img/certificate/pooja/');
                if (!file_exists($certificateDir)) {
                    mkdir($certificateDir, 0777, true);
                }

                $certificatePath = 'assets/back-end/img/certificate/pooja/' . $pooja['order_id'] . '.jpg';
                $certificate->save(public_path($certificatePath));
                // Service_order::where('order_id', $pooja['order_id'])->update(['pooja_certificate' => $pooja['order_id'] . '.jpg']);
                // $astrologer = Astrologer::where('id', $pooja['pandit_assign'])->first();
                // // dd($astrologer);
                // if ($astrologer) {
                //     foreach (json_decode($astrologer['is_pandit_pooja_commission']) as $key => $value) {
                //         if ($key == $pooja['services']['id']) {
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
                $transaction->type = $pooja['services']['product_type'];
                $transaction->order_id = $pooja['order_id'];
                $transaction->txn_id = !empty($pooja['wallet_translation_id']) ? $pooja['wallet_translation_id'] : $pooja['payment_id'];
                $transaction->service_id =  $request->service_id;
                $transaction->booking_date = $request->booking_date;
                $transaction->amount = $pooja['pay_amount'];
                $transaction->package_price = $packagePrice;
                $transaction->product_amount = $productAmount;
                $transaction->tax = $tax['online_pooja'] ?? 0;
                $transaction->save();
                Devotee::where('service_order_id', $pooja->order_id)->update([
                    'status' => 1,
                ]);

                PanditTransectionPooja::where('service_order_id', $pooja->order_id)->update([
                    'status' => 1,
                ]);
                PoojaRecords::where('service_order_id', $pooja->order_id)->update([
                    'status' => 1,
                ]);
                Service_order::where('order_id', $pooja['order_id'])->where('is_block', '!=', 9)->update([
                    'status' => $request->order_status,
                    'is_completed' => $request->order_status,
                    'is_edited' => $request->order_status,
                    'order_status' => $request->order_status,
                    'pooja_certificate' => $certificatePath,
                    'order_completed' => now(),
                ]);
                Prashad_deliverys::where('order_id', $pooja['order_id'])->where('service_id', $request->service_id)->update([
                    'pooja_status' => $request->order_status,
                    'status' => $request->order_status,
                    'order_completed' => now(),
                ]);
            } elseif ($request->order_status == 2) {
                Service_order::where('order_id', $pooja['order_id'])->where('is_block', '!=', 9)->update([
                    'order_status' => $request->order_status,
                ]);
            } else {
                Service_order::where('order_id', $pooja['order_id'])->where('is_block', '!=', 9)->update([
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
                        ->where('type', 'pooja')
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
                                    'type'         => 'pooja',
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
                    'puja_venue' => $service->pooja_venue,
                    'attachment' => asset('public/' . $certificatePath),
                    'type' => 'text-with-media',
                    'orderId' => $pooja->order_id,
                    'amount' => webCurrencyConverter((float) ($pooja->pay_amount - ($pooja->coupon_amount ?? 0))),
                    'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                ];

                SendWhatsappMessage::dispatch('whatsapp', 'Completed', $messageData);

                // send email
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Service::where('id', $pooja->service_id)
                        ->where('product_type', 'pooja')
                        ->first();
                    $bookingDetails = \App\Models\Service_order::where('service_id', ($pooja->service_id ?? ""))
                        ->where('type', 'pooja')->where('booking_date', ($pooja->booking_date ?? ""))
                        ->where('customer_id', ($pooja->customer_id ?? ""))->where('order_id', ($pooja->order_id ?? ""))
                        ->first();

                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Puja Completed',
                        'htmlContent' =>
                        \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-complete', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];

                    Helpers::emailSendMessage($data);
                }
            }
        }

        foreach ($orders as $order) {
            if ($order->order_status == 1 && $order->customer) {
                event(new OrderStatusEvent(key: '1', type: 'puja', order: $order));
            }
        }
        Toastr::success(translate('status_changed_successfully'));
        return redirect()->route('admin.pooja.orders.orderbycompleted', ['status' => 1]);
    }
    // Multiple All Order Status Time,Live,Pooja
    public function status_time(Request $request)
    {
        $orders = Service_order::where('status', 0)->where('type', 'pooja')->where('is_block', '!=', 9)->where('booking_date', $request->booking_date)->where('payment_status', 1)->where('service_id', $request->service_id)->with(['services', 'customers', 'customer'])->get();
        if ($orders->isEmpty()) {
            Toastr::error(translate('No orders found for the given criteria.'));
            return back();
        }
        foreach ($orders as $order) {
            $order->schedule_time = $request->schedule_time;
            $order->schedule_created = now();
            $order->order_status = $request->order_status;
            $order->save();
        }

        $service = Service::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
        }
        $processedCustomers = [];
        // Loop through the orders again to send WhatsApp messages
        foreach ($orders as $order) {
            if ($order->customers) {
                $hasCompleted = Service_order::where('service_id', $order->service_id)
                    ->where('customer_id', $order->customer_id)
                    ->where('status', 1)
                    ->exists();

                if ($hasCompleted) {
                    continue; 
                }
                if (in_array($order->customer_id, $processedCustomers)) {
                    continue;
                }
                $messageData = [
                    'service_name' => $service->name,
                    'puja_venue' => $service->pooja_venue ?? 'N/A',
                    'scheduled_time' => date('h:i A', strtotime($request->schedule_time)) ?? 'N/A',
                    'booking_date' => date('d-m-Y', strtotime($order->booking_date)),
                    'orderId' => $order->order_id,
                    'customer_id' => $order->customer_id,
                ];

                SendWhatsappMessage::dispatch('whatsapp', 'Schedule', $messageData);
                $processedCustomers[] = $order->customer_id;
                $userInfo = \App\Models\User::where('id', $order->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Service::where('id', $order->service_id)
                        ->where('product_type', 'pooja')
                        ->first();
                    $bookingDetails = \App\Models\Service_order::where('service_id', ($order->service_id ?? ""))
                        ->where('type', 'pooja')->where('booking_date', ($order->booking_date ?? ""))
                        ->where('customer_id', ($order->customer_id ?? ""))->where('order_id', ($order->order_id ?? ""))
                        ->first();
                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Puja Time Scheduled',
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
    }

    public function live_stream(Request $request)
    {
        // dd($request->all());
        $orders = Service_order::where('status', 0)->where('payment_status', 1)->where('is_block', '!=', 9)->where('type', 'pooja')->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->with(['services', 'customers', 'customer'])->get();
        if ($orders->isEmpty()) {
            Toastr::error(translate('No orders found for the given criteria.'));
            return back();
        }
        foreach ($orders as $pooja) {
            $pooja->live_stream = $request->live_stream;
            $pooja->live_created_stream = now();
            $pooja->order_status = $request->order_status;
            $pooja->save();
        }
        $service = Service::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
        }

        // Loop through the orders again to send WhatsApp messages
        foreach ($orders as $order) {
            if ($order->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'live_stream' => $order->live_stream ?? 'mahakal.com',
                    'puja_venue' => $service->pooja_venue ?? 'N/A',
                    'scheduled_time' => date('h:i A', strtotime($order->schedule_time)) ?? 'N/A',
                    'booking_date' => date('d-m-Y', strtotime($order->booking_date)),
                    'customer_id' => $order->customer_id,
                    'orderId' => $order->order_id,
                ];

                // Send WhatsApp message using the helper function
                SendWhatsappMessage::dispatch('whatsapp', 'Live Stream', $messageData);

                // send email

                $userInfo = \App\Models\User::where('id', $order->customer_id)->first();
                $service_name = \App\Models\Service::where('id', $order->service_id)
                    ->where('product_type', 'pooja')
                    ->first();
                $bookingDetails = \App\Models\Service_order::where('service_id', ($order->service_id ?? ""))
                    ->where('type', 'pooja')
                    ->where('booking_date', ($order->booking_date ?? ""))
                    ->where('customer_id', ($order->customer_id ?? ""))
                    ->where('order_id', ($order->order_id ?? ""))
                    ->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Puja Live Now',
                        'htmlContent' =>
                        \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-live-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];

                    SendEmailPoojaJob::dispatch($data);
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
        // dd($request->all());
        $orders = Service_order::where('status', 0)->where('payment_status', 1)->where('is_block', '!=', 9)->where('type', 'pooja')->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->with(['services', 'customers', 'customer'])->get();
        foreach ($orders as $pooja) {
            $pooja->pooja_video = $request->pooja_video;
            $pooja->video_created_sharing = now();
            $pooja->order_status = $request->order_status;
            $pooja->save();
        }
        $service = Service::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
        }

        foreach ($orders as $order) {
            if ($order->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'share_video' =>  $request->pooja_video ?? 'mahakal.com',
                    'puja_venue' => $service->pooja_venue,
                    'scheduled_time' => date('h:i A', strtotime($order->schedule_time)) ?? 'N/A',
                    'booking_date' => date('d-m-Y', strtotime($order->booking_date)),
                    'customer_id' => $order->customer_id,
                    'orderId' => $order->order_id,
                ];

                SendWhatsappMessage::dispatch('whatsapp', 'Shared Video', $messageData);
                // send email
                $userInfo = \App\Models\User::where('id', $order->customer_id)->first();
                $service_name = \App\Models\Service::where('id', $order->service_id)
                    ->where('product_type', 'pooja')
                    ->first();

                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $bookingDetails = $order;

                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Puja Video Link Shared',
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
        Toastr::success(translate('pooja_video_changed_successfully'));

        return back();
    }
    //----------------------------------------Order by Pooja Change The option ------------------------------

    public function lead_list(Request $request)
    {
        if ($request->has('searchValue')) {
            $leads = Leads::where('person_name', 'like', '%' . $request->searchValue . '%')->where('status', 1)->where('type', 'pooja')->with('service')->orderBy('created_at', 'DESC')->paginate(10);
        } else {
            $leads = Leads::where('status', 1)->where('type', 'pooja')->with('service', 'followby')->orderBy('created_at', 'DESC')->paginate(10);
        }
        // dd($leads);
        return view('admin-views.pooja.lead.list', compact('leads'));
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
        // dd($followlist);
        return response()->json($followlist);
    }
    // Order Pop Model To Show
    public function getOrderDetails(Request $request)
    {
        $orderId = $request->id;
        $order = Service_order::where('id', $orderId)->where('type', 'pooja')->where('is_block', '!=', 9)
            ->with(['services', 'packages', 'astrologer', 'customers', 'product_leads'])
            ->first();
        return response()->json($order);
    }
    // Order By Pooja
    public function orders_by_pooja(Request $request)
    {

        $orders = Service_order::where('type', 'pooja')->where('status', 0)->where('is_block', '!=', 9)->where('is_completed', 0)->where('payment_status', 1)
            ->with(['packages', 'services', 'customers', 'pandit'])
            ->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date, COUNT(created_at) as booking_count, SUM(pay_amount) - SUM(COALESCE(coupon_amount, 0)) AS total_amount, COALESCE(GROUP_CONCAT(DISTINCT members SEPARATOR "|"), "") as members, order_status, package_id, created_at, id')
            ->groupBy('service_id', 'booking_date', 'pandit_assign')
            ->orderBy('total_orders', 'DESC')
            ->get();
        $users = User::all();
        return view('admin-views.pooja.order.orderbypooja', compact('orders', 'users'));
    }


    // All Single Order
    function all_single_order($service_id, $booking_date, $status)
    {
        if (!$service_id) {
            return redirect()->back()->with('error', 'No matching service found.');
        }
        $service = Service::find($service_id);
        if (!$service) {
            return redirect()->back()->with('error', 'Service not found.');
        }
        $orders = Service_order::where('type', 'pooja')->where('service_id', $service_id)->where('booking_date', $booking_date)->where('is_block', '!=', 9)
            ->where('order_status', $status)->with(['services','leads'])->get();
        $pending = Service_order::where('type', 'pooja')->where('service_id', $service_id)->where('booking_date', $booking_date)->where('order_status', $status)->where('is_block', '!=', 9)->first();
        if (!$pending) {
            $pending = null;
        }
        // dd($orders);
        return view('admin-views.pooja.order.AllsingleOrder', compact('orders', 'service', 'pending'));
    }

    // public function single_orders_details($booking_date, $service_id, $status)
    // {
    //     $service = Service::findOrFail($service_id);
    //     $serviceId = Service_order::select('service_id')->where('booking_date', $booking_date)->where('is_block', '!=', 9)->where('service_id', $service_id)->first()->service_id;
    //     // dd($serviceId);
    //     $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'type')
    //         ->where('primary_skills', 3)
    //         ->where('type', 'in house')
    //         ->where('status', 1)
    //         ->whereRaw("JSON_CONTAINS_PATH(is_pandit_pooja, 'one', '$.\"$serviceId\"')")
    //         ->get();
    //     $freelancerAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'is_pandit_pooja')
    //         ->where('primary_skills', 3)
    //         ->where('type', 'freelancer')
    //         ->where('status', 1)
    //         ->whereRaw("JSON_CONTAINS_PATH(is_pandit_pooja, 'one', '$.\"$serviceId\"')")
    //         ->get()
    //         ->map(function ($astrologer) use ($serviceId) {
    //             $isPanditPooja = json_decode($astrologer->is_pandit_pooja, true);
    //             $price = $isPanditPooja[$serviceId] ?? null;

    //             return [
    //                 'id' => $astrologer->id,
    //                 'name' => $astrologer->name,
    //                 'is_pandit_pooja_per_day' => $astrologer->is_pandit_pooja_per_day,
    //                 'service_id' => $serviceId,
    //                 'price' => $price,
    //             ];
    //         });
    //     if ($status == '1') {
    //         $details = Service_order::where('service_id', $service_id)->where('booking_date', $booking_date)->where('status', 1)->where('is_block', '!=', 9)->with(['services', 'packages', 'pandit'])
    //             ->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date, COUNT(booking_date) as booking_count,SUM(pay_amount) - SUM(COALESCE(coupon_amount, 0)) AS total_amount,  GROUP_CONCAT(members SEPARATOR "|") as members,GROUP_CONCAT(gotra SEPARATOR "|") as gotra, order_status,package_id,created_at,schedule_time,schedule_created,live_stream,live_created_stream,pooja_video,video_created_sharing,reject_reason,pooja_certificate,order_completed,order_canceled,order_canceled_reason')
    //             ->groupBy('service_id', 'booking_date')
    //             ->orderBy('total_orders', 'DESC')
    //             ->first();
    //     } else {
    //         $details = Service_order::where('service_id', $service_id)->where('payment_status', 1)->where('is_block', '!=', 9)
    //             ->where('status', $status)->where('booking_date', $booking_date)->with(['services', 'packages', 'pandit'])
    //             ->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date, COUNT(booking_date) as booking_count,SUM(pay_amount) - SUM(COALESCE(coupon_amount, 0)) AS total_amount, 
    //             GROUP_CONCAT(COALESCE(order_id, "") SEPARATOR "|") as order_id, 
    //             GROUP_CONCAT(COALESCE(members, "") SEPARATOR "|") as members, 
    //             GROUP_CONCAT(COALESCE(gotra, "") SEPARATOR "|") as gotra, 
    //             GROUP_CONCAT(COALESCE(leads_id, "") SEPARATOR ",") as leads, 
    //             order_status, package_id, created_at, schedule_time, schedule_created, live_stream, live_created_stream, pooja_video,video_created_sharing, reject_reason, pooja_certificate, order_completed, order_canceled, order_canceled_reason')
    //             ->groupBy('service_id', 'booking_date')
    //             ->orderBy('total_orders', 'DESC')
    //             ->first();
    //     }
    //     return view('admin-views.pooja.order.SingleOrderdetails', compact('details', 'service', 'inHouseAstrologers', 'freelancerAstrologers'));
    // }

    public function single_orders_details($booking_date, $service_id, $status)
    {
        $service = Service::findOrFail($service_id);

        $serviceId = Service_order::where('booking_date', $booking_date)
            ->where('service_id', $service_id)
            ->where('is_block', '!=', 9)
            ->value('service_id');

        // ✅ Pandit IDs from PanditPriceSlab (MODEL based)
        $panditIds = PanditPriceSlab::where('service_id', $serviceId)
            ->where('type', 'pooja')
            ->where('status', 1)
            ->pluck('pandit_id')
            ->unique()
            ->toArray();

        // ================= INHOUSE PANDITS =================
        $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'type')
            ->where('primary_skills', 3)
            ->where('type', 'in house')
            ->where('status', 1)
            ->whereIn('id', $panditIds)
            ->get();

        // ================= FREELANCER PANDITS =================
        $freelancerAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'type')
            ->where('primary_skills', 3)
            ->where('type', 'freelancer')
            ->where('status', 1)
            ->whereIn('id', $panditIds)
            ->get()
            ->map(function ($astrologer) use ($serviceId) {

                // ✅ Price from PanditPriceSlab MODEL
                $slab = PanditPriceSlab::where('pandit_id', $astrologer->id)
                    ->where('service_id', $serviceId)
                    ->where('type', 'pooja')
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

        // ================= ORDER DETAILS =================
        if ($status == '1') {

            $details = Service_order::where('service_id', $service_id)
                ->where('booking_date', $booking_date)
                ->where('status', 1)
                ->where('is_block', '!=', 9)
                ->with(['services', 'packages', 'pandit'])
                ->selectRaw('
                    service_id,
                    COUNT(*) as total_orders,
                    pandit_assign,
                    booking_date,
                    COUNT(booking_date) as booking_count,
                    SUM(pay_amount) - SUM(COALESCE(coupon_amount, 0)) AS total_amount,
                    GROUP_CONCAT(members SEPARATOR "|") as members,
                    GROUP_CONCAT(gotra SEPARATOR "|") as gotra,
                    order_status, package_id, created_at,
                    schedule_time, schedule_created,
                    live_stream, live_created_stream,
                    pooja_video, video_created_sharing,
                    reject_reason, pooja_certificate,
                    order_completed, order_canceled, order_canceled_reason
                ')
                ->groupBy('service_id', 'booking_date')
                ->orderBy('total_orders', 'DESC')
                ->first();

        } else {

            $details = Service_order::where('service_id', $service_id)
                ->where('payment_status', 1)
                ->where('is_block', '!=', 9)
                ->where('status', $status)
                ->where('booking_date', $booking_date)
                ->with(['services', 'packages', 'pandit'])
                ->selectRaw('
                    service_id,
                    COUNT(*) as total_orders,
                    pandit_assign,
                    booking_date,
                    COUNT(booking_date) as booking_count,
                    SUM(pay_amount) - SUM(COALESCE(coupon_amount, 0)) AS total_amount,
                    GROUP_CONCAT(COALESCE(order_id, "") SEPARATOR "|") as order_id,
                    GROUP_CONCAT(COALESCE(members, "") SEPARATOR "|") as members,
                    GROUP_CONCAT(COALESCE(gotra, "") SEPARATOR "|") as gotra,
                    GROUP_CONCAT(COALESCE(leads_id, "") SEPARATOR ",") as leads,
                    order_status, package_id, created_at,
                    schedule_time, schedule_created,
                    live_stream, live_created_stream,
                    pooja_video, video_created_sharing,
                    reject_reason, pooja_certificate,
                    order_completed, order_canceled, order_canceled_reason
                ')
                ->groupBy('service_id', 'booking_date')
                ->orderBy('total_orders', 'DESC')
                ->first();
        }

        return view(
            'admin-views.pooja.order.SingleOrderdetails',
            compact('details', 'service', 'inHouseAstrologers', 'freelancerAstrologers')
        );
    }


    public function order_rejected_update(Request $request)
    {
        $orderId = $request->order_id;
        $serviceId = $request->service_id;
        $cust_details = [
            'booking_date' => $request->input('booking_date'),
            'reject_reason' => $request->input('reject_reason'),
            'status' => 0,
            'order_status' => 0,
            'is_rejected' => 1,
            'is_completed' => 0,
            'created_at' => now(),
            'pandit_assign' => null
        ];
        $updated = Service_order::where('order_id', $orderId)->where('is_block', '!=', 9)->update($cust_details);
        $whatsappData = Service_order::where('order_id', $orderId)->first();
        $service_name = \App\Models\Service::where('id', ($whatsappData->service_id ?? ""))->where('product_type', 'pooja')->first();

        if ($updated) {
            Toastr::success(translate('Pooja_Reschedule_Successfully'));
        } else {
            Toastr::error(translate('Pooja_Reschedule_Unsuccessfully'));
        }
        return redirect()->route('admin.pooja.orders.list', ['6']);
    }

    // public function orders_getpandit($serviceId, $bookingDate)
    // {
    //     $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'type')
    //         ->where('primary_skills', 3)
    //         ->where('type', 'in house')
    //         ->where('status', 1)
    //         ->whereRaw("JSON_CONTAINS_PATH(is_pandit_pooja, 'one', '$.\"{$serviceId}\"')")
    //         ->get()
    //         ->map(function ($astrologer) use ($bookingDate) {
    //             // Calculate checkastro for in-house astrologers
    //             $checkastro = Service_Order::where('pandit_assign', $astrologer->id)
    //                 ->where('booking_date', $bookingDate)->where('is_block', '!=', 9)
    //                 ->count();
    //             $completeastro = Service_Order::where('pandit_assign', $astrologer->id)->where('status', 1)
    //                 ->where('booking_date', $bookingDate)->where('is_block', '!=', 9)
    //                 ->count();
    //             $astrologer->checkastro = $checkastro;
    //             $astrologer->completeastro = $completeastro;
    //             return $astrologer;
    //         });

    //     // Fetch freelancer astrologers
    //     $freelancerAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'type', 'is_pandit_pooja')
    //         ->where('primary_skills', 3)
    //         ->where('type', 'freelancer')
    //         ->where('status', 1)
    //         ->whereRaw("JSON_CONTAINS_PATH(is_pandit_pooja, 'one', '$.\"{$serviceId}\"')")
    //         ->get()
    //         ->map(function ($astrologer) use ($bookingDate, $serviceId) {
    //             $checkastro = Service_Order::where('pandit_assign', $astrologer->id)
    //                 ->where('booking_date', $bookingDate)->where('is_block', '!=', 9)
    //                 ->count();

    //             $completeastro = Service_Order::where('pandit_assign', $astrologer->id)
    //                 ->where('status', 1)
    //                 ->where('booking_date', $bookingDate)->where('is_block', '!=', 9)
    //                 ->count();

    //             $isPanditPooja = json_decode($astrologer->is_pandit_pooja, true);
    //             $price = $isPanditPooja[$serviceId] ?? null;

    //             $astrologer->checkastro = $checkastro;
    //             $astrologer->completeastro = $completeastro;
    //             $astrologer->price = $price;

    //             return $astrologer;
    //         });

    //     return response()->json(['status' => 200, 'inhouse' => $inHouseAstrologers, 'freelancer' => $freelancerAstrologers]);
    // }

    public function orders_getpandit($serviceId, $bookingDate)
    {
        // ✅ Pandits list from PanditPriceSlab (Model-based)
        $panditIds = PanditPriceSlab::where('service_id', $serviceId)
            ->where('type', 'pooja')
            ->where('status', 1)
            ->pluck('pandit_id')
            ->unique()
            ->toArray();

        // ================= INHOUSE PANDITS =================
        $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'type')
            ->where('primary_skills', 3)
            ->where('type', 'in house')
            ->where('status', 1)
            ->whereIn('id', $panditIds)
            ->get()
            ->map(function ($astrologer) use ($bookingDate) {

                $checkastro = Service_Order::where('pandit_assign', $astrologer->id)
                    ->where('booking_date', $bookingDate)
                    ->where('is_block', '!=', 9)
                    ->count();

                $completeastro = Service_Order::where('pandit_assign', $astrologer->id)
                    ->where('status', 1)
                    ->where('booking_date', $bookingDate)
                    ->where('is_block', '!=', 9)
                    ->count();

                $astrologer->checkastro    = $checkastro;
                $astrologer->completeastro = $completeastro;

                return $astrologer;
            });

        // ================= FREELANCER PANDITS =================
        $freelancerAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'type')
            ->where('primary_skills', 3)
            ->where('type', 'freelancer')
            ->where('status', 1)
            ->whereIn('id', $panditIds)
            ->get()
            ->map(function ($astrologer) use ($bookingDate, $serviceId) {

                $checkastro = Service_Order::where('pandit_assign', $astrologer->id)
                    ->where('booking_date', $bookingDate)
                    ->where('is_block', '!=', 9)
                    ->count();

                $completeastro = Service_Order::where('pandit_assign', $astrologer->id)
                    ->where('status', 1)
                    ->where('booking_date', $bookingDate)
                    ->where('is_block', '!=', 9)
                    ->count();

                // ✅ Price from PanditPriceSlab MODEL
                $slab = PanditPriceSlab::where('pandit_id', $astrologer->id)
                    ->where('service_id', $serviceId)
                    ->where('type', 'pooja')
                    ->where('status', 1)
                    ->orderBy('min_qty')
                    ->first();

                $astrologer->checkastro    = $checkastro;
                $astrologer->completeastro = $completeastro;
                $astrologer->price         = $slab->single_price ?? $slab->price ?? null;

                return $astrologer;
            });

        return response()->json([
            'status'     => 200,
            'inhouse'    => $inHouseAstrologers,
            'freelancer' => $freelancerAstrologers,
        ]);
    }

    public function orders_getpanditordercount($panditId, $bookdate)
    {
        $orderCount = Service_order::where('pandit_assign', $panditId)->where('booking_date', $bookdate)->groupBy('service_id')->where('is_block', '!=', 9)->count();
        return response()->json(['status' => 200, 'ordercount' => $orderCount]);
    }

    public function downloadMemberList($service_id, $booking_date, $status)
    {
        $orders = Service_order::where('type', 'pooja')->where('service_id', $service_id)->where('booking_date', $booking_date)->where('is_block', '!=', 9)
            ->where('order_status', $status)->get();
        // dd($orders);
        $pooja_name = Service::where('id', $service_id)->value('name');
        $pooja_venue = Service::where('id', $service_id)->value('pooja_venue');
        $bookingDate = optional($orders->first())->booking_date;
        $mpdf_view = PdfView::make('admin-views.pooja.order.member-list', compact('orders', 'pooja_name', 'bookingDate', 'pooja_venue'));
        $this->generatePdf($mpdf_view, 'member-list', $bookingDate);
    }
    public function send_whatsapp_leads($id)
    {
        $lead = Leads::where('id', $id)->first();
        $poojaName = Service::where('status', 1)->where('id', $lead->service_id)->first();
        $customer = User::where('is_active', 1)->where('phone', $lead->person_phone)->first();

        if ($lead) {
            $message_data = [
                'service_name' => $poojaName->name,
                'puja_venue' => $poojaName->pooja_venue,
                'type' => 'text-with-media',
                'attachment' => asset('/storage/app/public/pooja/thumbnail/' . $poojaName->thumbnail),
                'puja' => 'Puja',
                'link' => 'mahakal.com/epooja/' . $poojaName->slug,
                'customer_id' => ($customer->id ?? ""),
            ];

            // dd($message_data);
            $messages =  Helpers::whatsappMessage('whatsapp', 'Lead Message', $message_data);
            Leads::where('id', $id)->increment('whatsapp_hit');
            Toastr::success(translate('message_sent_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }

    // --------------Order----By---Completed------------19/06/2025----------------------------By Renuka Rudrawal And Er.Rahul Bathri
    // public function orders_by_completed(Request $request)
    // {
    //     $orders = Service_order::where('type', 'pooja')->where('status', 1)->where('is_completed', 1)->with(['packages', 'services', 'customers', 'pandit'])->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date,order_completed, COUNT(created_at) as booking_count, SUM(pay_amount) - SUM(COALESCE(coupon_amount, 0)) AS total_amount, COALESCE(GROUP_CONCAT(DISTINCT members SEPARATOR "|"), "") as members, order_status, package_id, created_at, id')
    //         ->groupBy('service_id', 'booking_date', 'pandit_assign')->orderBy('total_orders', 'DESC')->get();
    //     $users = User::all();
    //     // dd($orders);
    //     return view('admin-views.pooja.order.orderbycompleted', compact('orders', 'users'));
    // }

    public function orders_by_completed(Request $request)
    {
        $orders = Service_order::where('type', 'pooja')
            ->where('status', 1)->where('is_block', '!=', 9)
            ->where('is_completed', 1)
            ->with(['packages', 'services', 'customers', 'pandit'])
            ->selectRaw('
                service_id, 
                COUNT(*) as total_orders, 
                pandit_assign, 
                booking_date,
                order_completed, 
                COUNT(created_at) as booking_count, 
                SUM(pay_amount) - SUM(COALESCE(coupon_amount, 0)) AS total_amount, 
                COALESCE(GROUP_CONCAT(DISTINCT members SEPARATOR "|"), "") as members, 
                order_status, 
                package_id, 
                MAX(created_at) as latest_created_at
            ')
            ->groupBy('service_id', 'booking_date', 'pandit_assign', 'order_status', 'package_id', 'order_completed')
            ->orderBy('total_orders', 'DESC')
            ->get();

        $users = User::all();

        return view('admin-views.pooja.order.orderbycompleted', compact('orders', 'users'));
    }
    public function completed_puja($booking_date, $service_id, $status)
    {
        $service = Service::findOrFail($service_id);
        // 2. Get service_id from first matched service order
        $serviceOrder = Service_order::where('booking_date', $booking_date)->where('is_block', '!=', 9)->where('service_id', $service_id)->first();

        if (!$serviceOrder) {
            return back()->with('error', 'No service orders found for the given date and service.');
        }
        // 3. Get In-house astrologers (pandits)
        $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'type')->where('primary_skills', 3)
            ->where('type', 'in house')->where('status', 1)
            ->whereRaw("JSON_CONTAINS_PATH(is_pandit_pooja, 'one', '$.\"$service_id\"')")
            ->get();

        // 4. Get Freelancer astrologers with dynamic price info
        $freelancerAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'is_pandit_pooja')
            ->where('primary_skills', 3)->where('type', 'freelancer')
            ->where('status', 1)->whereRaw("JSON_CONTAINS_PATH(is_pandit_pooja, 'one', '$.\"$service_id\"')")
            ->get()
            ->map(function ($astrologer) use ($service_id) {
                $isPanditPooja = json_decode($astrologer->is_pandit_pooja, true);
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
            $details = Service_order::where('service_id', $service_id)
                ->where('status', $status)->where('booking_date', $booking_date)->with(['services', 'packages', 'pandit'])
                ->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date, COUNT(booking_date) as booking_count,SUM(pay_amount) - SUM(COALESCE(coupon_amount, 0)) AS total_amount, 
                GROUP_CONCAT(COALESCE(order_id, "") SEPARATOR "|") as order_id, 
                GROUP_CONCAT(COALESCE(members, "") SEPARATOR "|") as members, 
                GROUP_CONCAT(COALESCE(gotra, "") SEPARATOR "|") as gotra, 
                GROUP_CONCAT(COALESCE(leads_id, "") SEPARATOR ",") as leads, 
                order_status, package_id, created_at, schedule_time, schedule_created, live_stream, live_created_stream, pooja_video,video_created_sharing,pooja_certificate, order_completed')
                ->groupBy('service_id', 'booking_date')
                ->orderBy('total_orders', 'DESC')
                ->first();
        } else {
            $details = null;
        }

        // 6. Get all service orders with relations
        $orders = Service_order::whereDate('booking_date', $booking_date)
            ->where('service_id', $service_id)
            ->where('status', $status)->where('is_block', '!=', 9)
            ->with(['services', 'packages', 'pandit']) // eager loading
            ->get();
        // dd($service);
        // 7. Pass all data to the view
        return view('admin-views.pooja.order.complete-puja', compact(
            'orders',
            'details',
            'inHouseAstrologers',
            'freelancerAstrologers',
            'service',
        ));
    }
    public function update_pooja_video(Request $request)
    {

        $orders = Service_order::where('order_status', $request->order_status)->where('is_block', '!=', 9)->where('type', 'pooja')->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->with(['services', 'customers', 'customer'])->get();

        foreach ($orders as $pooja) {
            $pooja->pooja_video = $request->pooja_video;
            $pooja->video_created_sharing = now();
            $pooja->live_created_stream = now();
            $pooja->order_status = $request->order_status;
            $pooja->save();
        }
        // dd($pooja);
        $service = Service::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
        }

        foreach ($orders as $order) {
            if ($order->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'share_video' =>  $request->pooja_video ?? 'mahakal.com',
                    'puja_venue' => $service->pooja_venue,
                    'booking_date' => date('d-m-Y', strtotime($order->booking_date)),
                    'customer_id' => $order->customer_id,
                    'orderId' => $order->order_id,
                ];

                SendWhatsappMessage::dispatch('whatsapp', 'Shared Video', $messageData);
                // send email
                $userInfo = \App\Models\User::where('id', $order->customer_id)->first();
                $service_name = \App\Models\Service::where('id', $order->service_id)
                    ->where('product_type', 'pooja')
                    ->first();

                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $bookingDetails = $order;

                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Puja Video Link Shared',
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

    public function search(Request $request)
        {
            $search = trim($request->get('search', ''));

            $customers = User::query()
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('f_name', 'LIKE', "%{$search}%")
                        ->orWhere('l_name', 'LIKE', "%{$search}%")
                        ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ["%{$search}%"])
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                    });
                })
                ->select('id', 'f_name', 'l_name', 'phone')
                ->limit(20)
                ->get();

            return response()->json($customers);
        }

    public function getCustomerOrders(Request $request)
    {
        $orders = Service_order::where('customer_id', $request->customer_id)->where('type','pooja')->where('is_block', '!=', 9)
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

        Service_order::whereIn('order_id', $orderIds)
            ->where('type', 'pooja')
            ->update(['is_block' => 9]);

        return response()->json([
            'message' => 'Selected orders have been blocked successfully.'
        ]);
    }

    public function update_certificate(Request $request)
    {
        $orders = Service_order::where('order_status', $request->order_status)
            ->where('is_block', '!=', 9)
            ->where('type', 'pooja')
            ->where('booking_date', $request->booking_date)
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

            $directory = public_path(dirname($oldPath));

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true);
            }

            $file->move($directory, $fileName);
        }

        foreach ($orders as $order) {
            $order->pooja_certificate = $oldPath;
            $order->order_status = $request->order_status;
            $order->save();
        }

        Toastr::success('Pooja certificate update successfully');
        return back();
    }

}
