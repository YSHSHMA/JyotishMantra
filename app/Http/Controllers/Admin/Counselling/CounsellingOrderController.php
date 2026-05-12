<?php

namespace App\Http\Controllers\Admin\Counselling;

use App\Events\OrderStatusEvent;
use App\Http\Controllers\Controller;
use App\Models\Astrologer\Astrologer;
use App\Models\Followsup;
use App\Models\Leads;
use App\Traits\PdfGenerator;
use App\Models\Product;
use App\Models\Service_order;
use App\Models\ServiceTax;
use App\Models\ServiceTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\View as PdfView;
use Intervention\Image\Facades\Image;
use App\Utils\Helpers;
use App\Utils\ApiHelper;
use App\Models\Service;
use App\Models\CounsellingUser;
use Razorpay\Api\Api;
use App\Traits\Whatsapp;
use App\Models\Admin;
use App\Models\PanditPriceSlab;
use App\Models\INPanditWallet;
use App\Models\WhatsappTemplate;
use App\Models\WConsultancyTemplate;
use App\Models\WChadhavaTemplate;
use App\Models\WEventTemplate;
use App\Models\WDonationTemplate;
use App\Models\WEcomTemplate;
use App\Models\WToursTemplate;
use App\Library\Payment as PaymentInfo;
use App\Library\Payer;
use App\Library\Receiver;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Models\PanditTransectionPooja;
use App\Models\PaymentRequest;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Storage;
use function App\Utils\payment_gateways;
use App\Traits\Payment;
use Carbon\Carbon;


class CounsellingOrderController extends Controller
{
    use Whatsapp;
    use PdfGenerator;

    public function orders_list($status, Request $request)
    {
        $ordersQuery = Service_order::where('type', 'counselling')->where('is_block', '!=', 9)->with(['leads', 'packages', 'services', 'customers', 'pandit']);

        if ($status !== 'all') {
            $ordersQuery->where('status', $status);
        }
        if ($request->filled('payment_status')) {
            $ordersQuery->where('payment_status', $request->payment_status);
        }
        if ($request->filled('service_id')) {
            $ordersQuery->where('service_id', $request->service_id);
        }
        if ($request->filled('status')) {
            $ordersQuery->where('status', $request->status);
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
        return view('admin-views.counselling.order.list', compact('orders', 'users','paymentPublishedStatus','paymentGatewayPublishedStatus','payment_gateways_list','digital_payment'));

        // if ($status == 'all') {
        //     $orders = Service_order::whereNot('payment_status', 2)->where('type', 'counselling')->with('services')->with('customers')->with('astrologer')->orderBy('created_at', 'DESC')->paginate(10);
        // } elseif ($status == 0) {
        //     $orders = Service_order::whereNot('payment_status', 2)->where('type', 'counselling')->where('status', 0)->with('customers')->with('services')->with('astrologer')->orderBy('created_at', 'DESC')->paginate(10);
        // } else {
        //     $orders = Service_order::where('type', 'counselling')->where('status', $status)->with('customers')->with('services')->with('astrologer')->orderBy('created_at', 'DESC')->paginate(10);
        // }
        // $users = User::all();

        // $paymentPublishedStatus = config('get_payment_publish_status');
        // $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
        // $payment_gateways_list = payment_gateways();
        // $digital_payment = getWebConfig(name: 'digital_payment');
        
        // return view('admin-views.counselling.order.list', compact('orders', 'users', 'payment_gateways_list', 'paymentGatewayPublishedStatus', 'digital_payment'));
    }

    public function checked_order()
    {
        $counselling = Service_order::where('checked', 0)->where('type', 'counselling')->update(['checked' => 1]);
        if ($counselling) {
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 400]);
    }


    public function orders_details($id)
    {

        $serviceId = Service_order::select('service_id')->where('id', $id)->first()->service_id;
        $assignedAstrologers = Service_order::whereNotNull('pandit_assign')->whereNotIn('status', [1, 2, 6])->pluck('pandit_assign')->toArray();

         // Pandit IDs from PanditPriceSlab (MODEL based)
        $panditIds = PanditPriceSlab::where('service_id', $serviceId)
            ->where('type', 'counselling')
            ->where('status', 1)
            ->pluck('pandit_id')
            ->unique()
            ->toArray();

        // Get in-house astrologers
        $inHouseAstrologers = Astrologer::select('id', 'name')
            ->where('primary_skills', 4)
            ->where('type', 'in house')
            ->where('status', 1)
            ->whereIn('id', $panditIds)
            ->get()
            ->filter(function ($astrologer) use ($assignedAstrologers) {
                return !in_array($astrologer->id, $assignedAstrologers);
            });

        // Get freelancer astrologers
        $freelancerAstrologers = Astrologer::select('id', 'name', 'consultation_charge')
            ->where('primary_skills', 4)
            ->where('type', 'freelancer')
            ->where('status', 1)
            ->whereIn('id', $panditIds)
            ->get()
            ->filter(function ($astrologer) use ($assignedAstrologers) {
                return !in_array($astrologer->id, $assignedAstrologers);
            });


        // Get in-house pandit
        $panditinhouse = Astrologer::select('id', 'name')
            ->where('primary_skills', 3)
            ->where('type', 'in house')
            ->whereRaw("JSON_CONTAINS(other_skills, '\"4\"')")
            ->where('status', 1)
            ->whereIn('id', $panditIds)
            ->get()
            ->filter(function ($astrologer) use ($assignedAstrologers) {
                return !in_array($astrologer->id, $assignedAstrologers);
            });

        // Get freelancer pandit
        $panditfreelancer = Astrologer::select('id', 'name', 'consultation_charge')
            ->where('primary_skills', 3)
            ->where('type', 'freelancer')
            ->whereRaw("JSON_CONTAINS(other_skills, '\"4\"')")
            ->where('status', 1)
            ->whereIn('id', $panditIds)
            ->get()
            ->filter(function ($astrologer) use ($assignedAstrologers) {
                return !in_array($astrologer->id, $assignedAstrologers);
            });


        $details = Service_order::where('id', $id)->with('customers')->with('services.category')->with('payments')->with('astrologer')->with('counselling_user')->first();
        return view('admin-views.counselling.order.details', compact('details', 'inHouseAstrologers', 'freelancerAstrologers', 'panditfreelancer', 'panditinhouse'));
    }

    public function orders_generate_invoice($id)
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = Service_order::where('id', $id)->with('customers')->with('services')->with('payments')->with('counselling_user')->first();
        // dd($details);
        $mpdf_view = PdfView::make('admin-views.counselling.order.invoice', compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        $this->generatePdf($mpdf_view, 'order_invoice_', $details['order_id']);
    }

    public function orders_assign_astrologer($id, Request $request)
    {
        $pandit = Service_order::where('id', $id)->update(['pandit_assign' => $request->astrologer_id]);
        $order = Service_order::where('id', $id)->first();
        if ($pandit) {
            $getPanditData = Astrologer::where('id', $request->astrologer_id)->first();
            if (!$getPanditData || !$getPanditData->consultation_charge) {
                Toastr::error(translate('Astrologer or price data not found'));
                return back();
            }

            // Step 3: Decode price list
            $priceList = json_decode($getPanditData->consultation_charge, true);
            $price = isset($priceList[$order->service_id]) ? $priceList[$order->service_id] : 0;
            $commissionList = json_decode($getPanditData->consultation_commission, true);
            $commission = $commissionList[$order->service_id] ?? 0;
            $tax = ServiceTax::value('consultation');

            $existingTransaction = PanditTransectionPooja::where('service_order_id', $order->order_id)->first();

            if ($existingTransaction) {
                $existingTransaction->update([
                    'pandit_id'     => $request->astrologer_id,
                    'pandit_amount' => $price,
                    'admin_commission' => $commission,
                    'govt_tax' => $tax,
                ]);
            } else {
                PanditTransectionPooja::create([
                    'pandit_id'         => $request->astrologer_id,
                    'service_id'        => $order->service_id,
                    'service_order_id'  => $order->order_id,
                    'type'              => 'counselling',
                    'pandit_amount'     => $price,
                    'order_amount'    => $order->pay_amount,
                    'admin_commission' => $commission,
                    'govt_tax' => $tax,
                ]);
            }

            Toastr::success(translate('astrologer_assigned'));
            return back();
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function orders_report($id, Request $request)
    {
        // $file = $request->file('report');
        // $report = time() . '-report' . $file->getClientOriginalName();
        // $file->storeAs('public/consultation-order-report', $report);

        $file = $request->file('report');

        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $extension = strtolower($file->getClientOriginalExtension());

        $report = time() . '-report.' . $extension;

        if (in_array($extension, $allowedExtensions)) {
            $mainImage = Image::make($file);

            $headerPath = public_path('assets/back-end/img/counselling-report/top.png');
            $footerPath = public_path('assets/back-end/img/counselling-report/bottom.png');

            $headerImage = Image::make($headerPath)->resize($mainImage->width(), null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $footerImage = Image::make($footerPath)->resize($mainImage->width(), null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $finalHeight = $headerImage->height() + $mainImage->height() + $footerImage->height();
            $canvas = Image::canvas($mainImage->width(), $finalHeight);

            $canvas->insert($headerImage, 'top');
            $canvas->insert($mainImage, 'top-left', 0, $headerImage->height());
            $canvas->insert($footerImage, 'bottom');

            Storage::put("public/consultation-order-report/{$report}", (string) $canvas->encode());
        } else {
            $file->storeAs('public/consultation-order-report', $report);
        }

        $reportVerified = Service_order::where('id', $id)->update(['counselling_report' => $report, 'counselling_report_reject_reason' => null, 'counselling_report_verified' => 0]);
        if ($reportVerified) {
            Toastr::success(translate('report submitted successfully'));
            return back();
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function orders_report_verify($id)
    {
        $reportVerified = Service_order::where('id', $id)->update(['counselling_report_reject_reason' => null, 'counselling_report_verified' => 1]);
        if ($reportVerified) {
            Toastr::success(translate('report verified successfully'));
            return back();
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function orders_report_reject(Request $request)
    {
        $reportReject = Service_order::where('id', $request->id)->update(['counselling_report_reject_reason' => $request->counselling_report_reject_reason, 'counselling_report_verified' => 2]);
        if ($reportReject) {
            Toastr::success(translate('report reject successfully'));
            return back();
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function orders_status($id, Request $request)
    {
        $commission = 0;
        $counselling = Service_order::where('id', $id)->with(['services', 'customers', 'payments'])->where('is_completed', 0)->first();
        $tax = ServiceTax::first();
        // if ($counselling) {
        //     $astrologer = Astrologer::where('id', $counselling['pandit_assign'])->first();
        //     if ($astrologer) {
        //         foreach (json_decode($astrologer['consultation_commission']) as $key => $value) {
        //             if ($key == $counselling['services']['id']) {
        //                 $commission = $value;
        //             }
        //         }
        //     }
        // }

        $packagePrice = $counselling->package_price;

        $amount = $counselling->pay_amount;

        if ($request->order_status == 1) {
            if ($counselling) {
                $transaction = new ServiceTransaction();
                $transaction->astro_id = $counselling['pandit_assign'];
                $transaction->type = $counselling['services']['product_type'];
                $transaction->order_id = $counselling['order_id'];
                $transaction->txn_id = !empty($counselling['wallet_translation_id']) ? $counselling['wallet_translation_id'] : $counselling['payment_id'];
                $transaction->amount = $counselling['pay_amount'];
                $transaction->service_id =   $counselling->service_id;
                $transaction->package_price = $packagePrice;
                $transaction->tax = $tax['consultation'];
                $transaction->save();
            }


            Service_order::where('id', $id)->update(['order_completed' => now(), 'is_edited' => 1]);
            CounsellingUser::where('order_id', $counselling->order_id)->update(['is_update' => 1]);
        }

        // =====================================================
        // 🔥 PANDIT WALLET ENTRY (COUNSELLING - SINGLE ORDER)
        // =====================================================
        if ($request->order_status == 1 && $counselling) {

            $panditId     = $counselling->pandit_assign;
            $serviceId    = (int) $counselling->service_id;
            $totalOrders = 1;

            if ($panditId) {

                $slab = PanditPriceSlab::where([
                        'pandit_id'  => $panditId,
                        'service_id' => $serviceId,
                        'type'       => 'counselling',
                        'status'     => 1,
                    ])
                    ->where('min_qty', '<=', 1)
                    ->where('max_qty', '>=', 1)
                    ->first();

                if ($slab) {

                    $creditAmount = $slab->single_price ?? 0;
                    $entryType    = 'single';

                    // ✅ allow zero also
                    if ($creditAmount >= 0) {

                        $alreadyExists = INPanditWallet::where([
                            'pandit_id'    => $panditId,
                            'service_id'   => $serviceId,
                            'type'         => 'counselling',
                            'entry_type'   => $entryType,
                        ])->exists();

                        if (!$alreadyExists) {

                            $lastBalance = INPanditWallet::where('pandit_id', $panditId)
                                ->latest('id')
                                ->value('balance') ?? 0;

                            INPanditWallet::create([
                                'pandit_id'    => $panditId,
                                'service_id'   => $serviceId,
                                'total_orders' => 1,

                                'single_price' => $slab->single_price,
                                'slab_price'   => $slab->price,

                                'amount'       => $creditAmount,
                                'credit'       => $creditAmount,
                                'debit'        => 0,
                                'balance'      => $lastBalance + $creditAmount,

                                'entry_type'   => $entryType,
                                'type'         => 'counselling',
                            ]);
                        }
                    }
                }
            }
        }

        $reportVerified = Service_order::where('id', $id)->value('counselling_report_verified');

        if ($request->order_status == 1) {
            $userInfo = \App\Models\User::where('id', $counselling->customer_id)->first();
            $service_name = \App\Models\Service::where('id', $counselling->service_id)
                ->where('product_type', 'counselling')
                ->first();

            $message_data['service_name'] = $service_name['name'];
            $message_data['orderId'] = $counselling->order_id;
            $message_data['customer_id'] = $counselling->customer_id;

            $message_data['counselling_report'] = asset('storage/app/public/consultation-order-report/' . $counselling->counselling_report);

            $messages = Helpers::whatsappMessage('consultancy', 'Order Completed + pdf link', $message_data);

            // send email

            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {

                $bookingDetails = \App\Models\Service_order::where([
                    ['service_id', $counselling->service_id],
                    ['type', 'counselling'],
                    ['customer_id', $counselling->customer_id],
                    ['order_id', $counselling->order_id]
                ])->first();

                $data = [
                    'type' => 'counselling',
                    'email' => $userInfo->email,
                    'subject' => 'Consultation Completed',
                    'htmlContent' =>
                    \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-complete', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                ];
                Helpers::emailSendMessage($data);
            }
        } else if ($request->order_status == 2) {
            Service_order::where('id', $id)->update(['order_canceled' => now(), 'order_canceled_reason' => $request->cancel_reason, 'is_edited' => 1]);
            CounsellingUser::where('order_id', $counselling->order_id)->update(['is_update' => 1]);
        }

        $status = Service_order::where('id', $id)->update(['status' => $request->order_status]);

        if ($status) {
            $orderStatus = $request->order_status;

            $order = Service_order::where('order_id', $counselling->order_id)
                ->where('status', $orderStatus)
                ->with(['customer'])
                ->first();

            if ($orderStatus == 1) {
                event(new OrderStatusEvent(key: 'counselling_1', type: 'counselling', order: $order));
            } elseif ($orderStatus == 2) {
                event(new OrderStatusEvent(key: 'counselling_2', type: 'counselling', order: $order));
            }

            Toastr::success(translate('status_changed_successfully'));
            return back();
        }

        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function lead_list(Request $request)
    {
        if ($request->has('searchValue')) {
            $leads = Leads::where('person_name', 'like', '%' . $request->searchValue . '%')->where('status', 1)->where('type', 'counselling')->with('service')->orderBy('created_at', 'Desc')->paginate(10);
        } else {
            $leads = Leads::where('status', 1)->where('type', 'counselling', 'followby')->with('service')->orderBy('created_at', 'Desc')->paginate(10);
        }
        return view('admin-views.counselling.lead.list', compact('leads'));
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

    public function send_whatsapp_leads($id)
    {
        $lead = Leads::where('id', $id)->first();
        $poojaName = Service::where('status', 1)->where('id', $lead->service_id)->first();
        $customer = User::where('is_active', 1)->where('phone', $lead->person_phone)->first();

        if ($lead) {
            $message_data = [
                'service_name' => $poojaName->name,
                'type' => 'text-with-media',
                'attachment' => asset('/storage/app/public/pooja/thumbnail/' . $poojaName->thumbnail),
                'link' => 'mahakal.com/counselling/details/' . $poojaName->slug,
                'customer_id' => ($customer->id ?? ""),
            ];

            $messages =  Helpers::whatsappMessage('consultancy', 'Lead Message', $message_data);
            Leads::where('id', $id)->increment('whatsapp_hit');
            Toastr::success(translate('message_sent_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }

    public function pending_payment_request(Request $request)
    {
        $order = Service_order::where('order_id',$request->order_id)->with('services')->first();
        $redirect_link = $this->pending_customer_payment_request($request, $order);
        $linkid = explode('=', $redirect_link)['1'];
        PaymentRequest::where('id', $linkid)->update(['previous_url' => url('counselling/order/fail')]);

        // whatsapp message
        $message_data = [
            'service_name' => $order->services->name,
            // 'type' => 'text-with-media',
            // 'attachment' =>  asset('/storage/app/public/offlinepooja/thumbnail/' . $order->offlinePooja->thumbnail),
            'final_amount' => webCurrencyConverter((float)($order->package_price)),
            'customer_id' => $order->customer_id,
            'payment_link' => $redirect_link
        ];

        
        Helpers::whatsappMessage('consultancy', 'Pending Order Request', $message_data);
        Toastr::success(translate('message_sent_successfully'));
        return back();
    }

    public function pending_customer_payment_request($request,$order)
    {
        $companyName = BusinessSetting::where('type', 'company_name')->value('value') ?? 'Company Name';
        $companyLogo = asset('storage/app/public/company/' . Helpers::get_business_settings('company_web_logo'));

        $additional_data = [
            'business_name' => $companyName,
            'business_logo' => $companyLogo,
            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
            'leads_id' => $order->leads_id,
            'order_id' => $request->order_id,
            'service_id' => $order->service_id,
            'customer_id' => $order->customer_id,
            'package_price' => $order->package_price,
            'final_amount' => $order->package_price,
        ];

        if (in_array($request->payment_request_from, ['app', 'react'])) {
            $additional_data['customer_id'] = $order->customer_id;
            $additional_data['is_guest'] = $request->is_guest;
            $additional_data['order_note'] = $request['order_note'];
            $additional_data['payment_request_from'] = $request->payment_request_from;
        }

        $currency_model = Helpers::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            $currency_code = 'USD';
        } else {
            $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
            $currency_code = Currency::find($default)->code;
        }

        $customer = Helpers::get_customer($request);
        if ($customer == 'offline') {
            $address = ShippingAddress::where(['customer_id' => $order->customer_id, 'is_guest' => 1])->latest()->first();
            if ($address) {
                $payer = new Payer(
                    $address->contact_person_name,
                    $address->email,
                    $address->phone,
                    ''
                );
            } else {
                $payer = new Payer(
                    'Contact person name',
                    '',
                    '',
                    ''
                );
            }
        } else {
            $payer = new Payer(
                $customer['f_name'] . ' ' . $customer['l_name'],
                $customer['email'],
                $customer['phone'],
                ''
            );
            if (empty($customer['phone'])) {
                Toastr::error(translate('please_update_your_phone_number'));
                return route('checkout-payment');
            }
        }

        $payment_info = new PaymentInfo(
            success_hook: 'digital_payment_success_custom',
            failure_hook: 'digital_payment_fail',
            currency_code: $currency_code,
            payment_method: $request->payment_method,
            payment_platform: $request->payment_platform,
            payer_id: $customer == 'offline' ? $order->customer_id : $customer['id'],
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: $order->package_price,
            external_redirect_link: $request->payment_platform == 'web' ? $request->external_redirect_link : null,
            attribute: 'counselling',
            attribute_id: idate("U")
        );

        $receiver_info = new Receiver('receiver_name', 'example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return $redirect_link;
    }

    public function getCustomerOrders(Request $request)
    {
        $orders = Service_order::where('customer_id', $request->customer_id)->where('type','counselling')->where('is_block', '!=', 9)
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
            ->where('type', 'counselling')
            ->update(['is_block' => 9]);

        return response()->json([
            'message' => 'Selected orders have been blocked successfully.'
        ]);
    }
}