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
use App\Models\Vippooja;
use App\Models\Devotee;
use App\Models\PoojaRecords;
use App\Models\PanditTransectionPooja;
use App\Models\Service_order;
use App\Models\ServiceTax;
use App\Models\ServiceTransaction;
use App\Jobs\SendWhatsappMessage;
use App\Models\Prashad_deliverys;
use App\Models\User;
use App\Models\PanditPriceSlab;
use App\Models\INPanditWallet;
use App\Utils\Helpers;
use App\Traits\Whatsapp;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\View as PdfView;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\View;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use PDF;
use function App\Utils\payment_gateways;
use Illuminate\Support\Facades\File;

class VipPoojaOrderController extends Controller
{
    use PdfGenerator;


    
    public function orders_list($status, Request $request)
    {
        if ($status == 'all') {
            $orders = Service_order::where('type', 'vip')->where('is_block', '!=', 9)->with('leads')->with('packages')->with('vippoojas')->with('customers')->with('pandit')->orderBy('created_at', 'DESC')->paginate(10);
        } else {
            $orders = Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('status', $status)->with('leads')->with('customers')->with('vippoojas')->with('pandit')->orderBy('created_at', 'DESC')->paginate(10);
        }
        $users = User::all();

        $paymentPublishedStatus = config('get_payment_publish_status');
        $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
        $payment_gateways_list = payment_gateways();
        $digital_payment = getWebConfig(name: 'digital_payment');
        return view('admin-views.pooja.viporder.list', compact('orders', 'users', 'payment_gateways_list', 'paymentGatewayPublishedStatus', 'digital_payment'));
    }

    public function VippoojaData(Request $request): JsonResponse
    {
        $query = Service_order::select([
                'id',
                'order_id',
                'pay_amount',
                'coupon_amount',
                'is_prashad',
                'status',
                'order_status',
                'customer_id',
                'service_id',
                'booking_date',
                'package_id',
                'pandit_assign',
                'payment_status',
                'created_at',
            ])
            ->where('type', 'vip')
            ->with([
                'customers:id,f_name,l_name,phone',
                'vippoojas:id,name,is_anushthan',
                'pandit:id,name'
            ])
            ->orderByDesc('created_at');
    
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
    
        $orders = $query->get()->map(function ($order) {
          
            $statusMap = [
                0 => 'Pending',
                1 => 'Completed',
                2 => 'Canceled',
                3 => 'Schedule',
                4 => 'Live',
                5 => 'Video Share',
                6 => 'Rejected'
            ];
    
            $statusLabel = $statusMap[$order->status] ?? 'All';
    
            return [
                'id' => $order->id,
                'order_id' => $order->order_id,
                'order_date' => $order->created_at,
                'customer_name' => $order->customers?->f_name . ' ' . $order->customers?->l_name,
                'customer_phone' => $order->customers?->phone,
                'pooja_name' => $order->vippoojas?->name,
                'pooja_type' => $order->vippoojas?->is_anushthan,
                'pandit_name' => $order->pandit?->name,
                'pay_amount' => $order->pay_amount,
                'booking_date' => $order->booking_date,
                'coupon_amount' => $order->coupon_amount,
                'is_prashad' => $order->is_prashad,
                'status' => $order->status,
                'order_status' => $order->order_status,
                'service_id' => $order->service_id,
                'booking_date' => $order->booking_date,
                'package_id' => $order->package_id,
                'payment_status' => $order->payment_status,
            ];
        });
    
        return response()->json(['data' => $orders]);
    }
    // -----------------------------------------Instance Order Details-----------------------------------
    public function instance_orders(Request $request)
    {
        $orders = Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('status', 0)->where('is_completed', 0)->where('package_id', 6)->where('payment_status', 1)
            ->with(['packages', 'vippoojas', 'customers', 'pandit'])->orderBy('created_at', 'DESC')->paginate(10);
        return view('admin-views.pooja.viporder.listinstanceorder', compact('orders'));
    }
    public function checked_order()
    {
        $vip = Service_order::where('checked', 0)->update(['checked' => 1]);
        if ($vip) {
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 400]);
        // return redirect()->route('admin.vippooja.order.list', ['status' => 'all']);
    }

    public function orders_details($id)
    {
        $serviceId = Service_order::select('service_id')->where('is_block', '!=', 9)->where('id', $id)->first()->service_id;

        // Pandit IDs from PanditPriceSlab (MODEL based)
        $panditIds = PanditPriceSlab::where('service_id', $serviceId)
            ->where('type', 'vip')
            ->where('status', 1)
            ->pluck('pandit_id')
            ->unique()
            ->toArray();


        $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day')
            ->where('primary_skills', 3)->where('type', 'in house')->where('status', 1)->whereIn('id', $panditIds)
            ->get();


        // Get freelancer astrologers
        $freelancerAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day')
            ->where('primary_skills', 3)
            ->where('type', 'freelancer')
            ->where('status', 1)
            ->whereIn('id', $panditIds)
            ->get()
            ->map(function ($astrologer) use ($serviceId) {

                // Price from PanditPriceSlab MODEL
                $slab = PanditPriceSlab::where('pandit_id', $astrologer->id)
                    ->where('service_id', $serviceId)
                    ->where('type', 'vip')
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

        $details = Service_order::where('id', $id)->with('customers')->with('vippoojas')->with('leads')->with('packages')->with('payments')->with('pandit')->with('product_leads.productsData')->first();
        $details['pooja_pandit'] = Astrologer::where('primary_skills', '3')->where('is_pandit_vippooja', 'like', '%' . $details['service_id'] . '%')->where('status', 1)->get();
        return view('admin-views.pooja.viporder.details', compact('details', 'inHouseAstrologers', 'freelancerAstrologers'));
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
        $details = Service_order::where('id', $id)->with('customers')->with('vippoojas')->with('leads')->with('packages')->with('payments')->with('product_leads.productsData')->first();
        $mpdf_view = PdfView::make('admin-views.pooja.viporder.invoice', compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo', 'companyAddress', 'companygst', 'companypan'));
        $this->generatePdf($mpdf_view, 'order_invoice_', $details['order_id']);
    }

    // public function orders_assign_pandit(Request $request)
    // {
    //     $orders = Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('payment_status', 1)->where('service_id', $request->service_id)->where('booking_date', $request->booking_date)->get();
    //     if ($orders->isEmpty()) {
    //         Toastr::error(translate('No matching orders found'));
    //         return back();
    //     }
    //     $getPanditData = Astrologer::where('id', $request->pandit_id)->first();
    //     if (!$getPanditData || !$getPanditData->is_pandit_vippooja) {
    //         Toastr::error(translate('Pandit or price data not found'));
    //         return back();
    //     }
    //     $priceList = json_decode($getPanditData->is_pandit_vippooja, true);
    //     $price = isset($priceList[$request->service_id]) ? $priceList[$request->service_id] : 0;
    //     $commissionList = json_decode($getPanditData->is_pandit_vippooja_commission, true);
    //     $commission = $commissionList[$request->service_id] ?? 0;
    //     $tax = ServiceTax::value('online_pooja');

    //     foreach ($orders as $order) {
    //         $order->pandit_assign = $request->pandit_id;
    //         $order->save();
    //         $existingTransaction = PanditTransectionPooja::where('service_id', $order->service_id)->where('booking_date', $order->booking_date)->where('type','vip')
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
    //             // Insert new transaction with latest pandit_id
    //             PanditTransectionPooja::create([
    //                 'pandit_id'         => $request->pandit_id,
    //                 'service_id'        => $order->service_id,
    //                 'service_order_id'  => $order->order_id,
    //                 'type'              => 'vip',
    //                 'pandit_amount'     => $price,
    //                 'booking_date'      => $order->booking_date,
    //                 'order_amount'    => $order->pay_amount,
    //                 'admin_commission' => $commission,
    //                 'govt_tax' => $tax,
    //             ]);
    //         }
    //     }

    //     Toastr::success(translate('Pandit reassigned and transactions updated/created successfully'));
    //     return back();
    // }

    public function orders_assign_pandit(Request $request)
    {
        $orders = Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('payment_status', 1)->where('service_id', $request->service_id)->where('booking_date', $request->booking_date)->get();
        if ($orders->isEmpty()) {
            Toastr::error(translate('No matching orders found'));
            return back();
        }
        // $getPanditData = Astrologer::where('id', $request->pandit_id)->first();
        // if (!$getPanditData || !$getPanditData->is_pandit_vippooja) {
        //     Toastr::error(translate('Pandit or price data not found'));
        //     return back();
        // }

        $panditId    = $request->pandit_id;
        $serviceId   = $request->service_id;
        $bookingDate = $request->booking_date;
        $totalOrders = $orders->count();

        $tax = ServiceTax::value('online_pooja');
         // ================= PRICE FROM SLAB =================
        $slab = PanditPriceSlab::where('pandit_id', $panditId)
            ->where('service_id', $serviceId)
            ->where('type', 'vip')
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
                    'admin_commission' => 0,   // commission removed
                    'govt_tax'         => $tax,
                ]);
            } else {
                PanditTransectionPooja::create([
                    'pandit_id'        => $panditId,
                    'service_id'       => $order->service_id,
                    'service_order_id' => $order->order_id,
                    'type'             => 'vip',
                    'pandit_amount'    => $panditPrice,
                    'booking_date'     => $bookingDate,
                    'order_amount'     => $order->pay_amount,
                    'admin_commission' => 0,   // commission removed
                    'govt_tax'         => $tax,
                ]);
            }
        }


        Toastr::success(translate('Pandit reassigned and transactions updated/created successfully'));
        return back();
    }
    // Single certificated
    public function orders_status($id, Request $request)
    {
        $orders = Service_order::where('id', $id)->where('payment_status', 1)->where('is_completed', 0)->where('is_block', '!=', 9)->where('type', 'vip')->where('service_id', $request->service_id)->where('booking_date', $request->booking_date)->where('package_id', $request->package_id)->with(['vippoojas', 'customers', 'payments', 'pandit'])->first();

        if (!$orders) {
            Toastr::error(translate('order_not_found'));
            return back();
        }
        $commission = 0;
        $tax = ServiceTax::first();
        if ($request->order_status == 1) {
            $certificate = Image::make(public_path('assets/back-end/img/certificate/vippuja/format/certificate-format.png'));
            $certificate->text(@ucwords($orders['customers']['f_name']) . ' ' . @ucwords($orders['customers']['l_name']), 950, 630, function ($font) {
                $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });
            $serviceName = wordwrap($orders['vippoojas']['name'], 65, "\n", false);
            $certificate->text($serviceName, 500, 815, function ($font) {
                $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                $font->size(40);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            $certificate->text(date('d/m/Y', strtotime($orders['created_at'])), 830, 994, function ($font) {
                $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                $font->size(40);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });
            $certificatePath = 'assets/back-end/img/certificate/vippuja/' . $orders['order_id'] . '.jpg';
            $certificate->save(public_path($certificatePath));
            Service_order::where('id', $id)->update(['pooja_certificate' => $orders['order_id'] . '.jpg']);
            $astrologer = Astrologer::where('id', $orders['pandit_assign'])->first();
            // if ($astrologer) {
            //     foreach (json_decode($astrologer['is_pandit_vippooja_commission']) as $key => $value) {
            //         if ($key == $orders['vippoojas']['id']) {
            //             $commission = $value;
            //         }
            //     }
            // }
            if (!$orders['pandit_assign']) {
                Toastr::error(translate('Please_selected_the_Pandit_ji'));
                return back();
            }

            $packagePrice = $orders->package_price;

            $amount = $orders->pay_amount;

            $productAmount = $amount - $packagePrice;

            $transaction = new ServiceTransaction();
            $transaction->astro_id = $orders['pandit_assign'];
            $transaction->type = 'vip';
            $transaction->order_id = $orders['order_id'];
            $transaction->txn_id = !empty($orders['wallet_translation_id']) ? $orders['wallet_translation_id'] : $orders['payment_id'];
            $transaction->service_id =  $request->service_id;
            $transaction->booking_date = $request->booking_date;
            $transaction->amount = $orders['pay_amount'];
            $transaction->package_price = $packagePrice;
            $transaction->product_amount = $productAmount;
            $transaction->tax = $tax['online_pooja'] ?? 0;
            $transaction->save();
            Devotee::where('service_order_id', $orders->order_id)->update([
                'status' => 1,
            ]);
            PanditTransectionPooja::where('service_order_id', $orders->order_id)->update([
                'status' => 1,
            ]);
            PoojaRecords::where('service_order_id', $orders->order_id)->update([
                'status' => 1,
            ]);
            Service_order::where('order_id', $orders['order_id'])->update([
                'status' => $request->order_status,
                'is_completed' => $request->order_status,
                'is_edited' => $request->order_status,
                'order_status' => $request->order_status,
                'pooja_certificate' => $certificatePath,
                'order_completed' => now(),
            ]);
            if ($orders->is_prashad == 1) {
                Prashad_deliverys::where('order_id', $orders['order_id'])->where('service_id', $request->service_id)->update([
                    'pooja_status' => $request->order_status,
                    'status' => $request->order_status,
                    'order_completed' => now(),
                ]);
            }
        } elseif ($request->order_status == 2) {
            $status = Service_order::where('order_id', $orders['order_id'])->update([
                'order_status' => $request->order_status,
                'status' => $request->order_status,
            ]);
        } else {
            $status = Service_order::where('order_id', $orders['order_id'])->update([
                'order_status' => $request->order_status,
                'status' => $request->order_status,
            ]);
        }
        $service = Vippooja::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
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
                        ->where('type', 'vip')
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
                                    'type'         => 'vip',
                                ]);
                            
                        }
                    }
                }
            }
        if ($orders->customers) {
            $messageData = [
                'service_name' => $service->name,
                'share_video' => $request->pooja_video ?? 'mahakal.com',
                'customer_id' => $orders->customer_id,
                'puja' => 'VIP Puja',
                'puja_venue' => $orders->pandit->is_pandit_primary_mandir_location ?? 'N/A',
                'attachment' => asset('public/' . $certificatePath),
                'type' => 'text-with-media',
                'orderId' => $orders->order_id,
                'amount' => webCurrencyConverter((float) ($orders->pay_amount - ($orders->coupon_amount ?? 0))),
                'booking_date' => date('d-m-Y', strtotime($orders->booking_date)),
            ];

            SendWhatsappMessage::dispatch('vipanushthan', 'Completed', $messageData);
            $userInfo = \App\Models\User::where('id', $orders->customer_id)->first();
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $service_name = \App\Models\Vippooja::where('id', $orders->service_id)
                    ->where('is_anushthan', 0)->first();
                $bookingDetails = \App\Models\Service_order::where('service_id', ($orders->service_id ?? ""))
                    ->where('type', 'vip')->where('booking_date', ($orders->booking_date ?? ""))->where('customer_id', ($orders->customer_id ?? ""))->where('order_id', ($orders->order_id ?? ""))->first();
                $data = [
                    'type' => 'pooja',
                    'email' => $userInfo->email,
                    'subject' => 'Puja Completed',
                    'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-complete', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                ];
                SendEmailPoojaJob::dispatch($data);
            }
        }
        $order = Service_order::where('order_id', $orders->order_id)->where('order_status', '1')->with(['customer'])->first();
        event(new OrderStatusEvent(key: '1', type: 'puja', order: $order));
        Toastr::success(translate('status_changed_successfully'));
        return redirect()->route('admin.vippooja.order.list', ['status' => $request->order_status]);
    }

    // ------------------------------------Single  Order Status Time,Live,Share,Cancel----------------------------
    public function status_times($id, Request $request)
    {
        $orders = Service_order::where('id', $id)
            ->where('type', 'vip')
            ->where('status', 0)
            ->where('payment_status', 1)
            ->where('is_block', '!=', 9)
            ->where('package_id', $request->package_id)
            ->where('booking_date', $request->booking_date)
            ->where('service_id', $request->service_id)->with('pandit')
            ->first();
        // dd($orders); // Use first() instead of get()
        if ($orders) {
            $orders->schedule_time = $request->schedule_time;
            $orders->order_status = $request->order_status;
            $orders->schedule_created = now();
            $orders->save();
        } else {
            return response()->json(['message' => 'Order not found.'], 404);
        }
        $service = Vippooja::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
        }
        $scheduledTime = !empty($orders->schedule_time) && strtotime($orders->schedule_time)
            ? Carbon::parse($orders->schedule_time)->format('h:i A')
            : 'N/A';
        if ($orders->customer_id) {
            $messageData = [
                'service_name' => $service->name,
                'puja' => 'VIP Puja',
                'puja_venue' => $orders->pandit->is_pandit_primary_mandir_location ?? 'N/A',
                'scheduled_time' => $scheduledTime,
                'booking_date' => date('d-m-Y', strtotime($orders->booking_date)),
                'customer_id' => $orders->customer_id,
                'orderId' => $orders->order_id,
            ];
            // dd($messageData);

            SendWhatsappMessage::dispatch('vipanushthan', 'Schedule', $messageData);
            $userInfo = \App\Models\User::where('id', $orders->customer_id)->first();
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $service_name = \App\Models\Vippooja::where('id', $orders->service_id)->where('is_anushthan', 0)->first();
                $bookingDetails = \App\Models\Service_order::where('service_id', ($orders->service_id ?? ""))
                    ->where('type', 'vip')->where('booking_date', ($orders->booking_date ?? ""))->where('customer_id', ($orders->customer_id ?? ""))->where('order_id', ($orders->order_id ?? ""))->first();
                // dd($bookingDetails);
                $data = [
                    'type' => 'pooja',
                    'email' => $userInfo->email,
                    'subject' => 'Vip Pooja Time Scheduled',
                    'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-schedule-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                ];
                // dd($data);
                SendEmailPoojaJob::dispatch($data);
            }
        }

        $order = Service_order::where('order_id', $orders->order_id)->where('order_status', '3')->with(['customer'])->first();
        event(new OrderStatusEvent(key: '3', type: 'puja', order: $order));

        Toastr::success(translate('schedule_time_changed_successfully'));
        return back();
    }

    public function live_streams($id, Request $request)
    {
        // dd($request->all());
        $orders = Service_order::where('id', $id)
            ->where('type', 'vip')
            ->where('status', 0)
            ->where('payment_status', 1)
            ->where('is_block', '!=', 9)
            ->where('package_id', $request->package_id)
            ->where('booking_date', $request->booking_date)
            ->where('service_id', $request->service_id)->with('pandit')
            ->first();
        if ($orders) {
            $orders->live_stream = $request->live_stream;
            $orders->order_status = $request->order_status;
            $orders->live_created_stream = now();
            $orders->save();
        } else {
            return response()->json(['message' => 'Order not found.'], 404);
        }


        $service = Vippooja::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
        }
        $scheduledTime = !empty($orders->schedule_time) && strtotime($orders->schedule_time)
            ? Carbon::parse($orders->schedule_time)->format('h:i A')
            : 'N/A';
        if ($orders->customer_id) {
            $messageData = [
                'service_name' => $service->name,
                'live_stream' => $orders->live_stream ?? 'mahakal.com',
                'puja_venue' => $orders->pandit->is_pandit_primary_mandir_location ?? 'N/A',
                'scheduled_time' =>  $scheduledTime,
                'puja' => 'VIP Puja',
                'booking_date' => date('d-m-Y', strtotime($orders->booking_date)),
                'customer_id' =>  $orders->customer_id,
                'orderId' =>  $orders->order_id,
            ];
            // Send WhatsApp message using the helper function
            SendWhatsappMessage::dispatch('vipanushthan', 'Live Stream', $messageData);
            $userInfo = \App\Models\User::where('id',  $orders->customer_id)->first();
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $service_name = Vippooja::where('id',  $orders->service_id)
                    ->where('is_anushthan', 0)
                    ->first();

                $bookingDetails = Service_order::where('service_id',  $orders->service_id)
                    ->where('type', 'vip')
                    ->where('booking_date',  $orders->booking_date)
                    ->where('customer_id',  $orders->customer_id)
                    ->where('order_id',  $orders->order_id)
                    ->first();
                $data = [
                    'type' => 'pooja',
                    'email' => $userInfo->email,
                    'subject' => 'Vip Pooja Live Video',
                    'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-live-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                ];

                SendEmailPoojaJob::dispatch($data);
            }
        }
        $order = Service_order::where('order_id', $orders->order_id)->where('order_status', '4')->with(['customer'])->first();
        event(new OrderStatusEvent(key: '4', type: 'puja', order: $order));
        Toastr::success(translate('live_stream_changed_successfully'));
        return back();
    }

    public function pooja_videos($id, Request $request)
    {
        // dd($request->all());
        $orders = Service_order::where('id', $id)
            ->where('type', 'vip')
            ->where('status', 0)
            ->where('payment_status', 1)
            ->where('is_block', '!=', 9)
            ->where('package_id', $request->package_id)
            ->where('booking_date', $request->booking_date)
            ->where('service_id', $request->service_id)->with('pandit')
            ->first();
        if ($orders) {
            $orders->pooja_video = $request->pooja_video;
            $orders->video_created_sharing = now();
            $orders->order_status = $request->order_status;
            $orders->save();
        } else {
            return response()->json(['message' => 'Order not found.'], 404);
        }
        $service = Vippooja::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
        }
        if ($orders->customer_id) {
            $messageData = [
                'service_name' => $service->name,
                'puja' => 'VIP Puja',
                'puja_venue' => $orders->pandit->is_pandit_primary_mandir_location ?? 'N/A',
                'share_video' =>  $request->pooja_video ?? 'mahakal.com',
                'booking_date' => date('d-m-Y', strtotime($orders->booking_date)),
                'customer_id' => $orders->customer_id,
                'orderId' => $orders->order_id,
            ];
            // dd($messageData);

            SendWhatsappMessage::dispatch('vipanushthan', 'Shared Video', $messageData);
            $userInfo = \App\Models\User::where('id', $orders->customer_id)->first();
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $service_name = \App\Models\Vippooja::where('id', $orders->service_id)->where('is_anushthan', 0)->first();
                $bookingDetails = \App\Models\Service_order::where('service_id', ($orders->service_id ?? ""))
                    ->where('type', 'vip')->where('booking_date', ($orders->booking_date ?? ""))->where('customer_id', ($orders->customer_id ?? ""))->where('order_id', ($orders->order_id ?? ""))->first();
                // dd($bookingDetails);
                $data = [
                    'type' => 'pooja',
                    'email' => $userInfo->email,
                    'subject' => 'Vip Pooja Time Scheduled',
                    'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-schedule-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                ];
                // dd($data);
                SendEmailPoojaJob::dispatch($data);
            }
        }
        $order = Service_order::where('order_id', $orders->order_id)->where('order_status', '5')->with(['customer'])->first();
        event(new OrderStatusEvent(key: '5', type: 'puja', order: $order));
        Toastr::success(translate('pooja_video_changed_successfully'));

        return back();
    }
    public function cancel_poojas($id, Request $request)
    {
        $orders = Service_order::where('id', $id)
            ->where('type', 'vip')
            ->where('status', 0)
            ->where('payment_status', 1)
            ->where('is_block', '!=', 9)
            ->where('package_id', $request->package_id)
            ->where('booking_date', $request->booking_date)
            ->where('service_id', $request->service_id)->with('pandit')
            ->first();
        if ($orders) {
            $orders->order_canceled_reason = $request->cancel_reason;
            $orders->order_canceled = now();
            $orders->status = 2;
            $orders->is_completed = 2;
            $orders->save();
        } else {
            return response()->json(['message' => 'Order not found.'], 404);
        }
        $service = Vippooja::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
        }
        if ($orders->customers) {
            $messageData = [
                'service_name' => $service->name,
                'customer_id' => $orders->customer_id,
                'pooja' => 'VIP Pooja',
                'order_canceled_reason' => $orders->order_canceled_reason,
                'order_id' => $orders->order_id,
            ];

            SendWhatsappMessage::dispatch('vipanushthan', 'Canceled', $messageData);
            $userInfo = \App\Models\User::where('id', $orders->customer_id)->first();
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $service_name = \App\Models\Vippooja::where('id', $orders->service_id)
                    ->where('is_anushthan', 0)->first();
                $bookingDetails = \App\Models\Service_order::where('service_id', ($order->service_id ?? ""))
                    ->where('type', 'vip')->where('booking_date', ($order->booking_date ?? ""))->where('customer_id', ($order->customer_id ?? ""))->where('order_id', ($order->order_id ?? ""))->first();
                $data = [
                    'type' => 'pooja',
                    'email' => $userInfo->email,
                    'subject' => 'Cancel the Puja Service',
                    'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-schedule-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                ];
                SendEmailPoojaJob::dispatch($data);
            }
        }
        Toastr::success(translate('pooja_Cancel_successfully'));
        return redirect()->route('admin.vippooja.order.list', ['2']);
    }
    // ------------------------------------Single  Order Status Time,Live,Pooja----------------------------
    // --------------------------------------Leads Order For VIP Pooja--------------------------------------

    public function lead_list(Request $request)
    {
        if ($request->has('searchValue')) {
            $leads = Leads::where('person_name', 'like', '%' . $request->searchValue . '%')->where('status', 1)->where('type', 'vip')->with('vippooja')->orderBy('created_at', 'DESC')->paginate(10);
        } else {
            $leads = Leads::where('status', 1)->where('type', 'vip')->with('vippooja', 'followby')->orderBy('created_at', 'DESC')->paginate(10);
        }
        // dd($leads);
        return view('admin-views.pooja.viplead.list', compact('leads'));
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
        $order = Service_order::where('id', $orderId)->where('type', 'vip')->where('is_block', '!=', 9)
            ->with(['vippoojas', 'packages', 'astrologer', 'customers', 'product_leads'])
            ->first();
        return response()->json($order);
    }
    //----------------------------------------Order by Pooja Change The option ------------------------------
    public function orders_by_vippooja(Request $request)
    {

        // Fetch orders with package_id 5
        $viporders = Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('payment_status', 1)->where('status', 0)->where('is_completed', 0)->where('package_id', 5)
            ->with(['packages', 'vippoojas', 'customers', 'pandit'])
            ->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date, COUNT(created_at) as booking_count, SUM(pay_amount) as total_amount, COALESCE(GROUP_CONCAT(DISTINCT members SEPARATOR "|"), "") as members, order_status, package_id, created_at, id')
            ->groupBy('service_id', 'booking_date', 'pandit_assign')
            ->orderBy('total_orders', 'DESC')
            ->get();

        // Fetch orders with package_id 6
        $instanceorders = Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('status', 0)->where('is_completed', 0)->where('package_id', 6)->where('payment_status', 1)
            ->with(['packages', 'vippoojas', 'customers', 'pandit'])
            ->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date, COUNT(created_at) as booking_count, SUM(pay_amount) as total_amount, GROUP_CONCAT(members SEPARATOR "|") as members, order_status,package_id,created_at')
            ->groupBy('service_id', 'booking_date', 'pandit_assign')
            ->orderBy('total_orders', 'DESC')
            ->get();

        // Fetch all users
        $users = User::all();

        // Return the view with the orders and users
        return view('admin-views.pooja.viporder.orderbyvippooja', compact('viporders', 'instanceorders', 'users'));
    }

    // All Single Order
    public function all_single_order($service_id, $booking_date, $status)
    {
        if (!$service_id) {
            return redirect()->back()->with('error', 'No matching service found.');
        }
        $service = Vippooja::find($service_id);
        if (!$service) {
            return redirect()->back()->with('error', 'Service not found.');
        }
        $orders = Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('service_id', $service_id)->where('booking_date', $booking_date)
            ->where('order_status', $status)->with(['vippoojas'])->get();
        $pending = Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('service_id', $service_id)->where('booking_date', $booking_date)->where('order_status', $status)->first();
        if (!$pending) {
            $pending = null;
        }

        return view('admin-views.pooja.viporder.SingleOrder', compact('orders', 'service', 'pending'));
    }


    // public function single_orders_details($booking_date, $service_id, $status)
    // {
    //     if (!$service_id) {
    //         return redirect()->back()->with('error', 'No matching service found for the given booking date and service ID.');
    //     }

    //     $service = Vippooja::findOrFail($service_id);

    //     // Fetch service order, check if it exists before accessing service_id
    //     $serviceOrder = Service_order::select('service_id')
    //         ->where('package_id', 5)
    //         ->where('booking_date', $booking_date)
    //         ->where('is_block', '!=', 9)
    //         ->where('service_id', $service_id)
    //         ->first();

    //     if (!$serviceOrder) {
    //         return redirect()->back()->with('error', 'No matching service order found for the given booking date and service ID.');
    //     }

    //     $serviceId = $serviceOrder->service_id;

    //     $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day')
    //         ->where('primary_skills', 3)
    //         ->where('type', 'in house')
    //         ->where('status', 1)
    //         ->whereRaw("JSON_CONTAINS_PATH(is_pandit_vippooja, 'one', '$.\"$serviceId\"')")
    //         ->get();

    //     $query = Service_order::where('service_id', $service_id)
    //         ->where('booking_date', $booking_date)
    //         ->where('status', $status)
    //         ->where('package_id', 5)
    //         ->where('is_block', '!=', 9)
    //         ->with(['vippoojas', 'packages', 'pandit'])
    //         ->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date, COUNT(booking_date) as booking_count, SUM(pay_amount) as total_amount, 
    //             GROUP_CONCAT(COALESCE(members, "") SEPARATOR "|") as members, 
    //             GROUP_CONCAT(COALESCE(gotra, "") SEPARATOR "|") as gotra, 
    //             GROUP_CONCAT(COALESCE(leads_id, "") SEPARATOR ",") as leads, 
    //             GROUP_CONCAT(COALESCE(order_id, "") SEPARATOR "|") as order_id, 
    //             order_status, package_id, created_at, schedule_time, schedule_created, live_stream, live_created_stream, pooja_video, 
    //             video_created_sharing, reject_reason, pooja_certificate, order_completed, order_canceled, order_canceled_reason')
    //         ->groupBy('service_id', 'booking_date')
    //         ->orderBy('total_orders', 'DESC');

    //     // Check status condition
    //     $details = ($status == '1') ? $query->first() : $query->first();

    //     return view('admin-views.pooja.viporder.SingleOrderdetails', compact('details', 'service', 'inHouseAstrologers'));
    // }

    public function single_orders_details($booking_date, $service_id, $status)
    {
        if (!$service_id) {
            return redirect()->back()->with('error', 'No matching service found for the given booking date and service ID.');
        }

        $service = Vippooja::findOrFail($service_id);

        // Fetch service order, check if it exists before accessing service_id
        $serviceOrder = Service_order::select('service_id')
            ->where('package_id', 5)
            ->where('booking_date', $booking_date)
            ->where('is_block', '!=', 9)
            ->where('service_id', $service_id)
            ->first();

        if (!$serviceOrder) {
            return redirect()->back()->with('error', 'No matching service order found for the given booking date and service ID.');
        }
        

        $serviceId = $serviceOrder->service_id;
        $panditIds = PanditPriceSlab::where('service_id', $serviceId)
        ->where('type', 'vip')
        ->where('status', 1)
        ->pluck('pandit_id')
        ->unique()
        ->toArray();

        $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day')
            ->where('primary_skills', 3)
            ->where('type', 'in house')
            ->where('status', 1)
            ->whereIn('id', $panditIds)
            ->get()
            ->map(function ($astrologer) use ($serviceId) {

                // ✅ Price from PanditPriceSlab MODEL
                $slab = PanditPriceSlab::where('pandit_id', $astrologer->id)
                    ->where('service_id', $serviceId)
                    ->where('type', 'vip')
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

        $query = Service_order::where('service_id', $service_id)
            ->where('booking_date', $booking_date)
            ->where('status', $status)
            ->where('package_id', 5)
            ->where('is_block', '!=', 9)
            ->with(['vippoojas', 'packages', 'pandit'])
            ->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date, COUNT(booking_date) as booking_count, SUM(pay_amount) as total_amount, 
                GROUP_CONCAT(COALESCE(members, "") SEPARATOR "|") as members, 
                GROUP_CONCAT(COALESCE(gotra, "") SEPARATOR "|") as gotra, 
                GROUP_CONCAT(COALESCE(leads_id, "") SEPARATOR ",") as leads, 
                GROUP_CONCAT(COALESCE(order_id, "") SEPARATOR "|") as order_id, 
                order_status, package_id, created_at, schedule_time, schedule_created, live_stream, live_created_stream, pooja_video, 
                video_created_sharing, reject_reason, pooja_certificate, order_completed, order_canceled, order_canceled_reason')
            ->groupBy('service_id', 'booking_date')
            ->orderBy('total_orders', 'DESC');

        // Check status condition
        $details = ($status == '1') ? $query->first() : $query->first();

        return view('admin-views.pooja.viporder.SingleOrderdetails', compact('details', 'service', 'inHouseAstrologers'));
    }


    //----------------------------------------Order by Pooja Change The option ------------------------------
    public function all_orders_assign_pandit(Request $request)
    {
        $pandit = Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('payment_status', 1)->where('service_id', $request->service_id)->where('booking_date', $request->booking_date)->update(['pandit_assign' => $request->pandit_id]);
        if ($pandit) {
            Toastr::success(translate('pandit_assigned'));
            return back();
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }


    //ALL ORDER DETAILS
    public function all_orders_status(Request $request)
    {
        $orders = Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('payment_status', 1)->where('service_id', $request->service_id)->where('booking_date', $request->booking_date)->where('package_id', 5)->with(['vippoojas', 'customers', 'payments'])->get();
        if (!$orders) {
            Toastr::error(translate('order_not_found'));
            return back();
        }
        $commission = 0;
        $tax = ServiceTax::first();
        $service = Vippooja::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
        }

        foreach ($orders as $pooja) {
            if ($request->order_status == 1) {
                if ($pooja->customers) {
                    $customerName = ucwords($pooja->customers->f_name . ' ' . $pooja->customers->l_name);
                    $certificate = Image::make(public_path('assets/back-end/img/certificate/vippuja/format/certificate-format.png'));
                    $certificate->text(ucwords($customerName), 950, 630, function ($font) {
                        $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                        $font->size(100);
                        $font->color('#ffffff');
                        $font->align('center');
                        $font->valign('top');
                    });
                }
                $serviceName = wordwrap($pooja['vippoojas']['name'], 65, "\n", false);
                $certificate->text($serviceName, 500, 815, function ($font) {
                    $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                    $font->size(40);
                    $font->color('#ffffff');
                    $font->align('left');
                    $font->valign('top');
                });

                $certificate->text(date('d/m/Y', strtotime($pooja->created_at)), 830, 994, function ($font) {
                    $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                    $font->size(40);
                    $font->color('#ffffff');
                    $font->align('center');
                    $font->valign('top');
                });
                $certificatePath = 'assets/back-end/img/certificate/vippuja/' . $pooja['order_id'] . '.jpg';
                $certificate->save(public_path($certificatePath));
                // Service_order::where('service_id', $service_id)->update(['pooja_certificate' => $pooja['order_id'] . '.jpg']);
                $astrologer = Astrologer::where('id', $pooja['pandit_assign'])->first();
                if ($astrologer) {
                    foreach (json_decode($astrologer['is_pandit_vippooja_commission']) as $key => $value) {
                        if ($key == $pooja['vippoojas']['id']) {
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
                $transaction->type = 'vip';
                $transaction->order_id = $pooja['order_id'];
                $transaction->txn_id = !empty($pooja['wallet_translation_id']) ? $pooja['wallet_translation_id'] : $pooja['payment_id'];

                $transaction->amount = $pooja['pay_amount'];
                $transaction->commission = $commission;
                $transaction->tax = $tax['online_pooja'] ?? 0;
                // dd($transaction);
                $transaction->save();

                Service_order::where('order_id', $pooja['order_id'])->update([
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
                $status = Service_order::where('order_id', $pooja['order_id'])->update([
                    'order_status' => $request->order_status,
                ]);
            } else {
                $status = Service_order::where('order_id', $pooja['order_id'])->update([
                    'order_status' => $request->order_status,
                ]);
            }
            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'share_video' => $request->pooja_video ?? 'mahakal.com',
                    'customer_id' => $pooja->customer_id,
                    'puja' => 'VIP Puja',
                    'attachment' => asset('public/' . $certificatePath),
                    'type' => 'text-with-media',
                    'orderId' => $pooja->order_id,
                    'amount' => webCurrencyConverter((float) ($pooja->pay_amount ?? 0)),
                    'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                ];

                SendWhatsappMessage::dispatch('vipanushthan', 'Completed', $messageData);
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Vippooja::where('id', $pooja->service_id)
                        ->where('is_anushthan', 0)->first();
                    $bookingDetails = \App\Models\Service_order::where('service_id', ($order->service_id ?? ""))
                        ->where('type', 'vip')->where('booking_date', ($order->booking_date ?? ""))->where('customer_id', ($order->customer_id ?? ""))->where('order_id', ($order->order_id ?? ""))->first();
                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Puja Completed',
                        'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-complete', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];
                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }
        Toastr::success(translate('status_changed_successfully'));
        return redirect()->route('admin.vippooja.order.list', ['status' => 'all']);
    }

    // Multiple All Order Status Time,Live,Pooja
    public function status_time(Request $request)
    {
        $orders = Service_order::where('package_id', $request->package_id)->where('is_block', '!=', 9)->where('type', 'vip')->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->get();
        foreach ($orders as $pooja) {
            $pooja->schedule_time = $request->schedule_time;
            $pooja->schedule_created = now();
            $pooja->order_status = $request->order_status;
            $pooja->save();
            $service = Vippooja::find($request->service_id);
            if (!$service) {
                Toastr::error(translate('Service not found.'));
                return back();
            }
            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'puja' => 'VIP Puja',
                    'scheduled_time' => date('h:i A', strtotime($request->schedule_time)) ?? 'N/A',
                    'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                    'customer_id' => $pooja->customer_id,
                    'orderId' => $pooja->order_id,
                ];

                SendWhatsappMessage::dispatch('vipanushthan', 'Schedule', $messageData);
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Vippooja::where('id', $pooja->service_id)
                        ->where('is_anushthan', 0)->first();
                    $bookingDetails = \App\Models\Service_order::where('service_id', ($pooja->service_id ?? ""))
                        ->where('type', 'vip')->where('booking_date', ($pooja->booking_date ?? ""))->where('customer_id', ($pooja->customer_id ?? ""))->where('order_id', ($pooja->order_id ?? ""))->first();
                    // dd($bookingDetails);
                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Vip Pooja Time Scheduled',
                        'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-schedule-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];
                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }
        // dd($orders);
        Toastr::success(translate('schedule_time_changed_successfully'));

        return back();
    }

    public function live_stream(Request $request)
    {
        $orders = Service_order::where('package_id', $request->package_id)->where('type', 'vip')->where('is_block', '!=', 9)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->get();
        foreach ($orders as $pooja) {
            $pooja->live_stream = $request->live_stream;
            $pooja->live_created_stream = now();
            $pooja->order_status = $request->order_status;
            $pooja->save();
            $service = Vippooja::find($request->service_id);
            if (!$service) {
                Toastr::error(translate('Service not found.'));
                return back();
            }

            // Loop through the orders again to send WhatsApp messages
            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'live_stream' => $pooja->live_stream ?? 'mahakal.com',
                    'scheduled_time' => date('h:i A', strtotime($pooja->schedule_time)) ?? 'N/A',
                    'puja' => 'VIP Puja',
                    'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                    'customer_id' => $pooja->customer_id,
                    'orderId' => $pooja->order_id,
                ];
                // Send WhatsApp message using the helper function
                SendWhatsappMessage::dispatch('vipanushthan', 'Live Stream', $messageData);
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = Vippooja::where('id', $pooja->service_id)
                        ->where('is_anushthan', 0)
                        ->first();

                    $bookingDetails = Service_order::where('service_id', $pooja->service_id)
                        ->where('type', 'vip')
                        ->where('booking_date', $pooja->booking_date)
                        ->where('customer_id', $pooja->customer_id)
                        ->where('order_id', $pooja->order_id)
                        ->first();
                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Vip Pooja Live Video',
                        'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-live-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];

                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }
        // dd($orders);
        Toastr::success(translate('live_stream_changed_successfully'));

        return back();
    }

    public function pooja_video(Request $request)
    {
        $orders = Service_order::where('package_id', $request->package_id)->where('type', 'vip')->where('is_block', '!=', 9)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->get();
        foreach ($orders as $pooja) {
            $pooja->pooja_video = $request->pooja_video;
            $pooja->video_created_sharing = now();
            $pooja->order_status = $request->order_status;
            $pooja->save();
            $service = Vippooja::find($request->service_id);
            if (!$service) {
                Toastr::error(translate('Service not found.'));
                return back();
            }

            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'share_video' =>  $request->pooja_video ?? 'mahakal.com',
                    'puja' => 'VIP Puja',
                    'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                    'customer_id' => $pooja->customer_id,
                    'orderId' => $pooja->order_id,
                ];

                SendWhatsappMessage::dispatch('vipanushthan', 'Shared Video', $messageData);
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Vippooja::where('id', $pooja->service_id)
                        ->where('is_anushthan', 0)->first();
                    $bookingDetails = \App\Models\Service_order::where('service_id', ($pooja->service_id ?? ""))
                        ->where('type', 'vip')->where('booking_date', ($pooja->booking_date ?? ""))->where('customer_id', ($pooja->customer_id ?? ""))->where('order_id', ($pooja->order_id ?? ""))->first();

                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Vip Pooja Share Video',
                        'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-share-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];

                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }
        // dd($orders);
        Toastr::success(translate('pooja_video_changed_successfully'));

        return back();
    }

    public function cancel_pooja(Request $request)
    {
        $orders = Service_order::where('package_id', $request->package_id)->where('type', 'vip')->where('is_block', '!=', 9)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->get();
        foreach ($orders as $pooja) {
            $pooja->order_canceled_reason = $request->cancel_reason;
            $pooja->order_canceled = now();
            $pooja->status = 2;
            $pooja->is_completed = 2;
            $pooja->save();

            $service = Vippooja::find($request->service_id);
            if (!$service) {
                Toastr::error(translate('Service not found.'));
                return back();
            }

            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'customer_id' => $pooja->customer_id,
                    'pooja' => 'VIP Pooja',
                    'order_canceled_reason' => $pooja->order_canceled_reason,
                    'order_id' => $pooja->order_id,
                ];

                SendWhatsappMessage::dispatch('vipanushthan', 'Canceled', $messageData);
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Vippooja::where('id', $pooja->service_id)
                        ->where('is_anushthan', 0)->first();
                    $bookingDetails = \App\Models\Service_order::where('service_id', ($order->service_id ?? ""))
                        ->where('type', 'vip')->where('booking_date', ($order->booking_date ?? ""))->where('customer_id', ($order->customer_id ?? ""))->where('order_id', ($order->order_id ?? ""))->first();
                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Cancel the Puja Service',
                        'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-schedule-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];
                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }

        Toastr::success(translate('Vippooja_Cancel_successfully'));
        return redirect()->route('admin.vippooja.order.list', ['2']);
    }

    // Instance Order Details And Manage The Order Section By Er.Rahul Bathri 02/09/2024
    public function all_orders_instance_assgin_pandit(Request $request)
    {
        $pandit = Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('package_id', 6)->where('service_id', $request->service_id)->where('booking_date', $request->booking_date)->update(['pandit_assign' => $request->pandit_id]);
        if ($pandit) {
            Toastr::success(translate('pandit_assigned'));
            return back();
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }
    public function instance_orders_details($booking_date, $service_id, $status)
    {
        if (!$service_id) {
            return redirect()->back()->with('error', 'No matching service found for the given booking date and service ID.');
        }

        $service = Vippooja::findOrFail($service_id);

        // Fetch service order, check if it exists before accessing service_id
        $serviceOrder = Service_order::select('service_id')
            ->where('package_id', 6)
            ->where('is_block', '!=', 9)
            ->where('booking_date', $booking_date)
            ->where('service_id', $service_id)
            ->first();

        if (!$serviceOrder) {
            return redirect()->back()->with('error', 'No matching service order found for the given booking date and service ID.');
        }

        $serviceId = $serviceOrder->service_id;

        $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day')
            ->where('primary_skills', 3)
            ->where('type', 'in house')
            ->where('status', 1)
            ->whereRaw("JSON_CONTAINS_PATH(is_pandit_vippooja, 'one', '$.\"$serviceId\"')")
            ->get();

        $query = Service_order::where('service_id', $service_id)
            ->where('booking_date', $booking_date)
            ->where('status', $status)
            ->where('package_id', 6)
            ->where('is_block', '!=', 9)
            ->with(['vippoojas', 'packages', 'pandit'])
            ->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date, COUNT(booking_date) as booking_count, SUM(pay_amount) as total_amount, 
                GROUP_CONCAT(COALESCE(members, "") SEPARATOR "|") as members, 
                GROUP_CONCAT(COALESCE(gotra, "") SEPARATOR "|") as gotra, 
                GROUP_CONCAT(COALESCE(leads_id, "") SEPARATOR ",") as leads, 
                GROUP_CONCAT(COALESCE(order_id, "") SEPARATOR "|") as order_id, 
                order_status, package_id, created_at, schedule_time, schedule_created, live_stream, live_created_stream, pooja_video, 
                video_created_sharing, reject_reason, pooja_certificate, order_completed, order_canceled, order_canceled_reason')
            ->groupBy('service_id', 'booking_date')
            ->orderBy('total_orders', 'DESC');

        // Check status condition
        $details = ($status == '1') ? $query->first() : $query->first();

        return view('admin-views.pooja.viporder.instnaceOrderdetails', compact('details', 'service', 'inHouseAstrologers'));
    }
    // Instance Order Status Change function
    //ALL ORDER DETAILS

    public function all_instance_status(Request $request)
    {

        $orders = Service_order::where('type', 'vip')->where('is_block', '!=', 9)->where('service_id', $request->service_id)->where('booking_date', $request->booking_date)->where('package_id', 6)->with(['vippoojas', 'customers', 'payments'])->get();
        $service = Vippooja::find($request->service_id);
        if (!$service) {
            Toastr::error(translate('Service not found.'));
            return back();
        }
        $commission = 0;
        $tax = ServiceTax::first();
        foreach ($orders as $pooja) {
            if ($request->order_status == 1) {
                if ($pooja->customers) {
                    $customerName = ucwords($pooja->customers->f_name . ' ' . $pooja->customers->l_name);
                    $certificate = Image::make(public_path('assets/back-end/img/certificate/format/certificate-format.png'));
                    $certificate->text(ucwords($customerName), 950, 630, function ($font) {
                        $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                        $font->size(100);
                        $font->color('#ffffff');
                        $font->align('center');
                        $font->valign('top');
                    });
                }
                $serviceName = wordwrap($pooja['vippoojas']['name'], 65, "\n", false);
                $certificate->text($serviceName, 500, 815, function ($font) {
                    $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                    $font->size(40);
                    $font->color('#ffffff');
                    $font->align('left');
                    $font->valign('top');
                });

                $certificate->text(date('d/m/Y', strtotime($pooja->created_at)), 830, 994, function ($font) {
                    $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                    $font->size(40);
                    $font->color('#ffffff');
                    $font->align('center');
                    $font->valign('top');
                });
                $certificatePath = 'assets/back-end/img/certificate/pooja/' . $pooja['order_id'] . '.jpg';
                $certificate->save(public_path($certificatePath));
                Service_order::where('service_id', $request->service_id)->update(['pooja_certificate' => $pooja['order_id'] . '.jpg']);
                $astrologer = Astrologer::where('id', $pooja['pandit_assign'])->first();
                if ($astrologer) {
                    foreach (json_decode($astrologer['is_pandit_vippooja_commission']) as $key => $value) {
                        if ($key == $pooja['vippoojas']['id']) {
                            $commission = $value;
                        }
                    }
                }
                $transaction = new ServiceTransaction();
                $transaction->astro_id = $pooja['pandit_assign'];
                $transaction->type = 'vip';
                $transaction->order_id = $pooja['order_id'];
                $transaction->txn_id = !empty($pooja['wallet_translation_id']) ? $pooja['wallet_translation_id'] : $pooja['payment_id'];

                $transaction->amount = $pooja['pay_amount'];
                $transaction->commission = $commission;
                $transaction->tax = $tax['online_pooja'] ?? 0;
                $transaction->save();

                Service_order::where('service_id', $request->service_id)->where('package_id', 6)->update([
                    'status' => $request->order_status,
                    'is_completed' => $request->order_status,
                    'is_edited' => $request->order_status,
                    'order_status' => $request->order_status,
                    'pooja_certificate' => $certificatePath,
                    'order_completed' => now(),
                ]);
                // dd($transaction);
                Prashad_deliverys::where('order_id', $pooja['order_id'])->where('service_id', $request->service_id)->update([
                    'pooja_status' => $request->order_status,
                    'status' => $request->order_status,
                    'order_completed' => now(),
                ]);
            } elseif ($request->order_status == 2) {
                Service_order::where('service_id', $request->service_id)->where('package_id', 6)->update([
                    'order_status' => $request->order_status,
                ]);
                Toastr::success(translate('status_changed_successfully'));
                return back();
            } else {
                Service_order::where('service_id', $request->service_id)->where('package_id', 6)->update([
                    'order_status' => $request->order_status,
                ]);
                Toastr::success(translate('status_changed_successfully'));
                return back();
            }
            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'share_video' => $request->pooja_video ?? 'mahakal.com',
                    'customer_id' => $pooja->customer_id,
                    'pooja' => 'VIP Instance Pooja',
                    'attachment' => asset('public/' . $certificatePath),
                    'type' => 'text-with-media',
                ];

                SendWhatsappMessage::dispatch('vipanushthan', 'Completed', $messageData);
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Vippooja::where('id', $pooja->service_id)
                        ->where('is_anushthan', 0)->first();
                    $bookingDetails = \App\Models\Service_order::where('service_id', ($order->service_id ?? ""))
                        ->where('type', 'vip')->where('booking_date', ($order->booking_date ?? ""))->where('customer_id', ($order->customer_id ?? ""))->where('order_id', ($order->order_id ?? ""))->first();
                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Puja Time Scheduled',
                        'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-complete', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];
                    SendEmailPoojaJob::dispatch($data);
                }
            }
            Toastr::success(translate('status_changed_successfully'));
            return redirect()->route('admin.vippooja.order.list', ['status' => 1]);
        }
    }
    // Instance Order Status For Live,Share,Cancel

    public function time_instance(Request $request)
    {
        $orders = Service_order::where('package_id', $request->package_id)->where('is_block', '!=', 9)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->get();
        foreach ($orders as $pooja) {
            $pooja->schedule_time = $request->schedule_time;
            $pooja->schedule_created = now();
            $pooja->order_status = $request->order_status;
            $pooja->save();
            $service = Vippooja::find($request->service_id);
            if (!$service) {
                Toastr::error(translate('Service not found.'));
                return back();
            }
            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'pooja' => 'VIP Instance Pooja',
                    'scheduled_time' => date('h:i A', strtotime($request->schedule_time)) ?? 'N/A',
                    'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                    'customer_id' => $pooja->customer_id,
                ];
                SendWhatsappMessage::dispatch('vipanushthan', 'Schedule', $messageData);
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Vippooja::where('id', $pooja->service_id)
                        ->where('is_anushthan', 0)->first();
                    $bookingDetails = \App\Models\Service_order::where('service_id', ($pooja->service_id ?? ""))
                        ->where('type', 'vip')->where('booking_date', ($pooja->booking_date ?? ""))->where('customer_id', ($pooja->customer_id ?? ""))->where('order_id', ($pooja->order_id ?? ""))->first();
                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Puja Time Scheduled',
                        'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-schedule-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];
                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }
        // dd($orders);
        Toastr::success(translate('schedule_time_changed_successfully'));

        return back();
    }

    public function stream_instance(Request $request)
    {
        // dd($request->all());
        $orders = Service_order::where('package_id', $request->package_id)->where('is_block', '!=', 9)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->get();
        // dd($orders);
        if ($orders->isEmpty()) {
            Toastr::warning(translate('No orders found for the given criteria'));
            return back();
        }
        foreach ($orders as $pooja) {
            $pooja->live_stream = $request->live_stream;
            $pooja->live_created_stream = now();
            $pooja->order_status = $request->order_status;
            $pooja->save();
            $service = Vippooja::find($request->service_id);
            if (!$service) {
                Toastr::error(translate('Service not found.'));
                return back();
            }

            // Loop through the orders again to send WhatsApp messages
            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'live_stream' => $pooja->live_stream ?? 'mahakal.com',
                    'scheduled_time' => date('h:i A', strtotime($pooja->schedule_time)) ?? 'N/A',
                    'pooja' => 'VIP Instance Pooja',
                    'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                    'customer_id' => $pooja->customer_id,
                ];

                // Send WhatsApp message using the helper function
                SendWhatsappMessage::dispatch('vipanushthan', 'Live Stream', $messageData);
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Vippooja::where('id', $pooja->service_id)->where('is_anushthan', 0)->first();
                    $bookingDetails = \App\Models\Service_order::where('service_id', ($pooja->service_id ?? ""))
                        ->where('type', 'vip')->where('booking_date', ($pooja->booking_date ?? ""))->where('customer_id', ($pooja->customer_id ?? ""))->where('order_id', ($pooja->order_id ?? ""))->first();
                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Instance Puja Live Now',
                        'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-live-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];
                    // dd($data);
                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }
        Toastr::success(translate('live_stream_changed_successfully'));
        return back();
    }


    public function video_instance(Request $request)
    {
        // dd($request->all());
        $orders = Service_order::where('package_id', $request->package_id)->where('is_block', '!=', 9)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->get();
        foreach ($orders as $pooja) {
            $pooja->pooja_video = $request->pooja_video;
            $pooja->video_created_sharing = now();
            $pooja->order_status = $request->order_status;
            $pooja->save();
            $service = Vippooja::find($request->service_id);
            if (!$service) {
                Toastr::error(translate('Service not found.'));
                return back();
            }

            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'share_video' =>  $request->pooja_video ?? 'mahakal.com',
                    'pooja' => 'VIP Instance Pooja',
                    'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                    'customer_id' => $pooja->customer_id,
                ];

                SendWhatsappMessage::dispatch('vipanushthan', 'Shared Video', $messageData);
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Vippooja::where('id', $pooja->service_id)
                        ->where('is_anushthan', 0)->first();
                    $bookingDetails = \App\Models\Service_order::where('service_id', ($pooja->service_id ?? ""))
                        ->where('type', 'vip')->where('booking_date', ($pooja->booking_date ?? ""))->where('customer_id', ($pooja->customer_id ?? ""))->where('order_id', ($pooja->order_id ?? ""))->first();
                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Intance Puja Video Share Link',
                        'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-share-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];
                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }
        // dd($orders);
        Toastr::success(translate('pooja_video_changed_successfully'));

        return back();
    }

    public function cancel_instance(Request $request)
    {
        // dd($request->all());
        $orders = Service_order::where('type', 'anushthan')->where('is_block', '!=', 9)->where('package_id', $request->package_id)->where('booking_date', $request->booking_date)->where('service_id', $request->service_id)->get();
        foreach ($orders as $pooja) {
            $pooja->order_canceled_reason = $request->order_canceled_reason;
            $pooja->order_canceled = now();
            $pooja->status = 2;
            $pooja->is_completed = 2;
            $pooja->save();
            $service = Vippooja::find($request->service_id);
            if (!$service) {
                Toastr::error(translate('Service not found.'));
                return back();
            }

            if ($pooja->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'customer_id' => $pooja->customer_id,
                    'pooja' => 'VIP Instance Pooja',
                    'order_canceled_reason' => $pooja->order_canceled_reason,
                    'order_id' => $pooja->order_id,
                ];

                SendWhatsappMessage::dispatch('vipanushthan', 'Canceled', $messageData);
                $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Vippooja::where('id', $pooja->service_id)
                        ->where('is_anushthan', 0)->first();
                    $bookingDetails = \App\Models\Service_order::where('service_id', ($pooja->service_id ?? ""))
                        ->where('type', 'vip')->where('booking_date', ($pooja->booking_date ?? ""))->where('customer_id', ($pooja->customer_id ?? ""))->where('order_id', ($pooja->order_id ?? ""))->first();
                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Cancel the Puja Service',
                        'htmlContent' => \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-schedule-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];
                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }
        // dd($orders);
        Toastr::success(translate('Vippooja_Cancel_successfully'));
        return redirect()->route('admin.viporders.list', ['2']);
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
        $updated = Service_order::where('order_id', $orderId)->update($cust_details);
       
        if ($updated) {
            Toastr::success(translate('VIPPooja_Reschedule_Successfully'));
        } else {
            Toastr::error(translate('VIPPooja_Reschedule_Unsuccessfully'));
        }
        return redirect()->route('admin.vippooja.order.list', ['6']);
    }

    // public function orders_getpandit($serviceId, $bookingDate)
    // {
    //     // Fetch in-house astrologers
    //     $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'type', 'is_pandit_vippooja')
    //         ->where('primary_skills', 3)->where('type', 'in house')->where('status', 1)
    //         ->whereRaw("JSON_CONTAINS_PATH(is_pandit_vippooja, 'one', '$.\"{$serviceId}\"')")
    //         ->get()
    //         ->map(function ($astrologer) use ($bookingDate) {
    //             $checkastro = Service_Order::where('pandit_assign', $astrologer->id)
    //                 ->where('booking_date', $bookingDate)
    //                 ->count();

    //             $completeastro = Service_Order::where('pandit_assign', $astrologer->id)
    //                 ->where('status', 1)
    //                 ->where('booking_date', $bookingDate)
    //                 ->count();

    //             $astrologer->checkastro = $checkastro;
    //             $astrologer->completeastro = $completeastro;

    //             return $astrologer;
    //         });

    //     // Fetch freelancer astrologers
    //     $freelancerAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'type', 'is_pandit_vippooja')
    //         ->where('primary_skills', 3)
    //         ->where('type', 'freelancer')
    //         ->where('status', 1)->whereRaw("JSON_CONTAINS_PATH(is_pandit_vippooja, 'one', '$.\"{$serviceId}\"')")->get()
    //         ->map(function ($astrologer) use ($bookingDate, $serviceId) {
    //             $checkastro = Service_Order::where('pandit_assign', $astrologer->id)
    //                 ->where('booking_date', $bookingDate)
    //                 ->count();

    //             $completeastro = Service_Order::where('pandit_assign', $astrologer->id)
    //                 ->where('status', 1)
    //                 ->where('booking_date', $bookingDate)
    //                 ->count();

    //             $isPanditPooja = json_decode($astrologer->is_pandit_vippooja, true);
    //             $price = $isPanditPooja[$serviceId] ?? null;

    //             $astrologer->checkastro = $checkastro;
    //             $astrologer->completeastro = $completeastro;
    //             $astrologer->price = $price;

    //             return $astrologer;
    //         });
    //     // dd($freelancerAstrologers);
    //     return response()->json([
    //         'status' => 200,
    //         'inhouse' => $inHouseAstrologers,
    //         'freelancer' => $freelancerAstrologers
    //     ]);
    // }

      public function orders_getpandit($serviceId, $bookingDate)
    {
        // ✅ Pandits list from PanditPriceSlab (Model-based)
        $panditIds = PanditPriceSlab::where('service_id', $serviceId)
            ->where('type', 'vip')
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

                //  Price from PanditPriceSlab MODEL
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

    public function downloadMemberList($service_id, $booking_date, $status)
    {
        $orders = Service_order::where('type', 'vip')->where('service_id', $service_id)->where('is_block', '!=', 9)->where('booking_date', $booking_date)
            ->where('order_status', $status)->get();
        // dd($orders);
        $vipooja_name = Vippooja::where('id', $service_id)->value('name');
        $bookingDate = optional($orders->first())->booking_date;
        $mpdf_view = PdfView::make('admin-views.pooja.viporder.member-list', compact('orders', 'vipooja_name', 'bookingDate'));
        $this->generatePdf($mpdf_view, 'member-list', $bookingDate);
    }

    public function send_whatsapp_leads($id)
    {
        $lead = Leads::where('id', $id)->first();
        $poojaName = Vippooja::where('status', 1)->where('id', $lead->service_id)->first();
        $customer = User::where('is_active', 1)->where('phone', $lead->person_phone)->first();

        if ($lead) {
            $message_data = [
                'service_name' => $poojaName->name,
                'type' => 'text-with-media',
                'attachment' => asset('/storage/app/public/pooja/vip/thumbnail/' . $poojaName->thumbnail),
                'puja' => 'VIP Puja',
                'link' => 'mahakal.com/vip/vippooja/' . $poojaName->slug,
                'customer_id' => ($customer->id ?? ""),
            ];

            $messages =  Helpers::whatsappMessage('vipanushthan', 'Lead Message', $message_data);
            Leads::where('id', $id)->increment('whatsapp_hit');
            Toastr::success(translate('message_sent_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }

    public function getCustomerOrders(Request $request)
    {
        $orders = Service_order::where('customer_id', $request->customer_id)->where('type','vip')->where('is_block', '!=', 9)
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
            ->where('type', 'vip')
            ->update(['is_block' => 9]);

        return response()->json([
            'message' => 'Selected orders have been blocked successfully.'
        ]);
    }

    public function update_vip_certificate(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'pooja_certificate' => 'required|image',
        ]);

        $order = Service_order::find($request->order_id);

        if (!$order) {
            Toastr::error('Order not found');
            return back();
        }

        $path = 'assets/back-end/img/certificate/vippuja/';

        if (!File::exists(public_path($path))) {
            File::makeDirectory(public_path($path), 0777, true);
        }

        if ($request->hasFile('pooja_certificate')) {

            if (!empty($order->pooja_certificate) && File::exists(public_path($order->pooja_certificate))) {
                File::delete(public_path($order->pooja_certificate));
            }

            $file = $request->file('pooja_certificate');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($path), $fileName);

            $order->pooja_certificate = $path . $fileName;
            $order->save();
        }

        Toastr::success('VIP Pooja certificate updated successfully');
        return back();
    }

}