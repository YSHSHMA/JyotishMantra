<?php

namespace App\Http\Controllers\Admin\Pooja;

use App\Events\OrderStatusEvent;
use App\Http\Controllers\Controller;
use App\Models\Astrologer\Astrologer;
use App\Models\OfflineLead;
use App\Models\OfflinepoojaFollowup;
use App\Models\OfflinePoojaOrder;
use App\Traits\PdfGenerator;
use App\Models\ServiceTax;
use App\Models\ServiceTransaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\View as PdfView;
use Intervention\Image\Facades\Image;
use App\Models\User;
use Illuminate\Http\Request;
use App\Library\Payment as PaymentInfo;
use App\Utils\Helpers;
use App\Jobs\SendWhatsappMessage;
use App\Library\Payer;
use App\Library\Receiver;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Models\PanditTransectionPooja;
use App\Models\PaymentRequest;
use App\Models\PoojaOffline;
use App\Models\ShippingAddress;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use function App\Utils\payment_gateways;
use App\Traits\Payment;

class OfflinePoojaOrderController extends Controller
{
    use PdfGenerator;

    public function orders_list($status, Request $request)
    {
        $ordersQuery = OfflinePoojaOrder::where('is_block', '!=', 9)->with(['leads', 'package', 'offlinePooja', 'customers', 'pandit']);

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
            if ($request->status == 'pandit-assigned') {
                $ordersQuery->where('status', 0)->whereNotNull('pandit_assign');
            } else{
                $ordersQuery->where('status', $request->status);
            }
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
        return view('admin-views.pooja.offlinepoojaorder.list', compact('orders', 'users', 'paymentPublishedStatus', 'paymentGatewayPublishedStatus', 'payment_gateways_list', 'digital_payment'));

        // if ($status == 'all') {
        //     $orders = OfflinePoojaOrder::whereNot('payment_status', 2)->with('leads')->with('package')->with('offlinePooja')->with('customers')->with('pandit')->orderBy('created_at', 'DESC')->paginate(10);
        // } elseif ($status == 'pending') {
        //     $orders = OfflinePoojaOrder::where('status', 0)->whereNot('payment_status', 2)->whereNull('pandit_assign')->with('leads')->with('package')->with('customers')->with('offlinePooja')->with('pandit')->orderBy('created_at', 'DESC')->paginate(10);
        // } elseif ($status == 'pandit-assigned') {
        //     $orders = OfflinePoojaOrder::where('status', 0)->whereNotNull('pandit_assign')->with('leads')->with('package')->with('customers')->with('offlinePooja')->with('pandit')->orderBy('created_at', 'DESC')->paginate(10);
        // } elseif ($status == 'completed') {
        //     $orders = OfflinePoojaOrder::where('status', 1)->with('leads')->with('package')->with('customers')->with('offlinePooja')->with('pandit')->orderBy('created_at', 'DESC')->paginate(10);
        // } elseif ($status == 'canceled') {
        //     $orders = OfflinePoojaOrder::where('status', 2)->with('leads')->with('package')->with('customers')->with('offlinePooja')->with('pandit')->orderBy('created_at', 'DESC')->paginate(10);
        // }

        // $paymentPublishedStatus = config('get_payment_publish_status');
        // $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
        // $payment_gateways_list = payment_gateways();
        // $digital_payment = getWebConfig(name: 'digital_payment');

        // $users = User::all();

        // return view('admin-views.pooja.offlinepoojaorder.list', compact('orders', 'users', 'payment_gateways_list', 'paymentGatewayPublishedStatus', 'digital_payment'));
    }

    public function orders_details($orderId)
    {
        $serviceId = OfflinePoojaOrder::select('service_id')->where('order_id', $orderId)->first()->service_id;
        $inHouseAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day')
            ->where('primary_skills', 3)
            ->where('type', 'in house')
            ->where('status', 1)
            ->whereRaw("JSON_CONTAINS_PATH(is_pandit_offlinepooja, 'one', '$.\"$serviceId\"')")
            ->get();
        $freelancerAstrologers = Astrologer::select('id', 'name', 'is_pandit_pooja_per_day', 'is_pandit_offlinepooja', 'latitude', 'longitude')
            ->where('primary_skills', 3)
            ->where('type', 'freelancer')
            ->where('status', 1)
            ->whereRaw("JSON_CONTAINS_PATH(is_pandit_offlinepooja, 'one', '$.\"$serviceId\"')")
            ->get();

        $details = OfflinePoojaOrder::where('order_id', $orderId)->with('customers')->with('offlinePooja')->with('leads')->with('package')->with('payments')->with('pandit')->with('temple')->first();

        return view('admin-views.pooja.offlinepoojaorder.details', compact('details', 'inHouseAstrologers', 'freelancerAstrologers'));
    }

    public function orders_assign_pandit($orderId, Request $request)
    {
        $pandit = OfflinePoojaOrder::where('order_id', $orderId)->where('service_id', $request->service_id)->where('booking_date', $request->booking_date)->update(['pandit_assign' => $request->pandit_id]);
        $order = OfflinePoojaOrder::where('order_id', $orderId)->first();
        if ($pandit) {
            $getPanditData = Astrologer::where('id', $request->pandit_id)->first();
            if (!$getPanditData || !$getPanditData->is_pandit_offlinepooja) {
                Toastr::error(translate('Pandit or price data not found'));
                return back();
            }

            $priceList = json_decode($getPanditData->is_pandit_offlinepooja, true);
            $price = isset($priceList[$request->service_id]) ? $priceList[$request->service_id] : 0;
            $commissionList = json_decode($getPanditData->is_pandit_offlinepooja_commission, true);
            $commission = $commissionList[$request->service_id] ?? 0;
            $tax = ServiceTax::value('offline_pooja');

            $existingTransaction = PanditTransectionPooja::where('service_order_id', $orderId)->first();

            if ($existingTransaction) {
                $existingTransaction->update([
                    'pandit_id'     => $request->pandit_id,
                    'pandit_amount' => $price,
                    'admin_commission' => $commission,
                    'govt_tax' => $tax,
                ]);
            } else {
                PanditTransectionPooja::create([
                    'pandit_id'         => $request->pandit_id,
                    'service_id'        => $request->service_id,
                    'service_order_id'  => $orderId,
                    'type'              => 'offlinepooja',
                    'pandit_amount'     => $price,
                    'booking_date'      => $request->booking_date,
                    'order_amount'    => $order->pay_amount,
                    'admin_commission' => $commission,
                    'govt_tax' => $tax,
                ]);
            }

            //Whatsapp
            $userInfo = \App\Models\User::where('id', ($order->customer_id ?? ""))->first();
            $service_name = \App\Models\PoojaOffline::where('id', $request->service_id)->first();
            $panditInfo = Astrologer::find($request->pandit_id);
            $poojaOrder = OfflinePoojaOrder::where('order_id', $orderId)
                ->where('service_id', $request->service_id)
                ->where('booking_date', $request->booking_date)
                ->first();
            $bookingDetails = $poojaOrder;
            $message_data = [
                'service_name' => $service_name['name'],
                'orderId' => $orderId,
                'customer_id' => $poojaOrder->customer_id,
                'pandit_name' => $panditInfo->name ?? '',
            ];
            $messages =  Helpers::whatsappMessage('offlinepooja', 'Pandit Assign', $message_data);

            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $data['type'] = 'pooja';
                $data['email'] = $userInfo['email'];
                $data['subject'] = 'Confirmation of pay remain amount';
                $data['htmlContent'] = \Illuminate\Support\Facades\View::make('admin-views.email.email-template.offlinepooja-pandit', compact('userInfo', 'service_name', 'bookingDetails', 'panditInfo'))->render();

                Helpers::emailSendMessage($data);
            }
            Toastr::success(translate('pandit_assigned'));
            return back();
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    // Single certificated
    public function orders_status($orderId, Request $request)
    {
        $pooja = OfflinePoojaOrder::where('order_id', $orderId)->where('service_id', $request->service_id)->where('booking_date', $request->booking_date)->where('package_id', $request->package_id)->with(['offlinePooja', 'customers', 'payments'])->first();
        if (!$pooja) {
            Toastr::error(translate('order_not_found'));
            return back();
        }
        $commission = 0;
        $tax = ServiceTax::first();
        if ($request->order_status == 1) {
            $certificate = Image::make(public_path('assets/back-end/img/certificate/format/certificate-format.png'));
            $certificate->text(@ucwords($pooja['customers']['f_name']) . ' ' . @ucwords($pooja['customers']['l_name']), 950, 630, function ($font) {
                $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                $font->size(100);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });
            $serviceName = wordwrap($pooja['offlinePooja']['name'], 65, "\n", false);
            $certificate->text($serviceName, 500, 815, function ($font) {
                $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                $font->size(40);
                $font->color('#ffffff');
                $font->align('left');
                $font->valign('top');
            });
            $certificate->text(date('d/m/Y', strtotime($pooja['created_at'])), 830, 994, function ($font) {
                $font->file(public_path('fonts/NotoSans-Regular.ttf'));
                $font->size(40);
                $font->color('#ffffff');
                $font->align('center');
                $font->valign('top');
            });
            $certificateDir = public_path('assets/back-end/img/certificate/offlinepooja/');
                if (!file_exists($certificateDir)) {
                    mkdir($certificateDir, 0777, true);
                }

            $certificatePath = 'assets/back-end/img/certificate/offlinepooja/' . $pooja['order_id'] . '.jpg';
            $certificate->save(public_path($certificatePath));
            OfflinePoojaOrder::where('order_id', $orderId)->update(['pooja_certificate' => $pooja['order_id'] . '.jpg']);
            $astrologer = Astrologer::where('id', $pooja['pandit_assign'])->first();
            if ($astrologer) {
                foreach (json_decode($astrologer['is_pandit_offlinepooja_commission']) as $key => $value) {
                    if ($key == $pooja['offlinePooja']['id']) {
                        $commission = $value;
                    }
                }
            }
            $transaction = new ServiceTransaction();
            $transaction->astro_id = $pooja['pandit_assign'];
            $transaction->type = 'offlinepooja';
            $transaction->order_id = $pooja['order_id'];
            $transaction->txn_id = !empty($pooja['wallet_translation_id']) ? $pooja['wallet_translation_id'] : $pooja['payment_id'];

            $transaction->amount = $pooja['pay_amount'];
            $transaction->commission = $commission;
            $transaction->tax = $tax['offline_pooja'] ?? 0;
            $transaction->save();
            OfflinePoojaOrder::where('order_id', $orderId)->update([
                'status' => $request->order_status,
                'is_edited' => $request->order_status,
                'pooja_certificate' => $certificatePath,
                'order_completed' => now(),
            ]);
            $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
            $service_name = \App\Models\PoojaOffline::where('id', ($pooja['service_id'] ?? ""))->first();
            $bookingDetails = \App\Models\OfflinePoojaOrder::where('service_id', ($pooja['service_id'] ?? ""))
                ->where('customer_id', ($pooja['customer_id'] ?? ""))
                ->where('order_id', ($orderId ?? ""))
                ->first();

            $messageData = [
                'service_name' => $service_name->name,
                'customer_id' => $pooja->customer_id,
                'venue_address' => $pooja->venue_address,
                'attachment' => asset('public/' . $certificatePath),
                'type' => 'text-with-media',
                'orderId' => $pooja->order_id,
                'amount' => webCurrencyConverter((float) ($pooja->pay_amount ?? 0)),
                'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
            ];
            SendWhatsappMessage::dispatch('offlinepooja', 'Completed', $messageData);

            // send email

            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {

                $data = [
                    'type' => 'pooja',
                    'email' => $userInfo->email,
                    'subject' => 'Puja Completed',
                    'htmlContent' =>
                    \Illuminate\Support\Facades\View::make('admin-views.email.email-template.offline-pooja-complete', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                ];

                Helpers::emailSendMessage($data);
            }
            // dd($transaction);
            $order = OfflinePoojaOrder::where('order_id', $pooja->order_id)->where('status', '1')->with(['customer'])->first();
            event(new OrderStatusEvent(key: 'offlinepuja_1', type: 'offlinepuja', order: $order));
            Toastr::success(translate('status_changed_successfully'));
            return redirect()->route('admin.offlinepooja.order.list', ['status' => 'completed']);
        }
        Toastr::success(translate('an_error_occurred'));
        return redirect()->back();
    }

    // schedule pooja
    public function status_times($orderId, Request $request)
    {
        $pooja = OfflinePoojaOrder::where('order_id', $orderId)->with(['offlinePooja', 'customers', 'payments', 'temple'])->first();
        if (!$pooja) {
            Toastr::error(translate('order_not_found'));
            return back();
        }
        $update = OfflinePoojaOrder::where('order_id', $orderId)->update(['status' => 3, 'time_schedule' => $request->time_schedule]);
        if ($update) {
            $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
            $service_name = \App\Models\PoojaOffline::where('id', ($pooja['service_id'] ?? ""))->first();
            $bookingDetails = \App\Models\OfflinePoojaOrder::where('service_id', ($pooja['service_id'] ?? ""))
                ->where('customer_id', ($pooja['customer_id'] ?? ""))
                ->where('order_id', ($orderId ?? ""))->with('temple')
                ->first();

            $scheduledTime = !empty($request->time_schedule) && strtotime($request->time_schedule)
                ? Carbon::parse($request->time_schedule)->format('h:i A')
                : 'N/A';
            $messageData = [
                'service_name' => $service_name->name,
                'puja' => 'Pandit Book',
                'scheduled_time' => $scheduledTime,
                'puja_venue' => $pooja->pooja_venue_type == 'address' ? $pooja->venue_address : $pooja->temple->name,
                'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                'customer_id' => $pooja->customer_id,
                'orderId' => $pooja->order_id,
            ];

            SendWhatsappMessage::dispatch('offlinepooja', 'Schedule', $messageData);

            // send email
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {

                $data = [
                    'type' => 'pooja',
                    'email' => $userInfo->email,
                    'subject' => 'Pandit Book Time Scheduled',
                    'htmlContent' =>
                    \Illuminate\Support\Facades\View::make('admin-views.email.email-template.offlinepooja-schedule-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                ];

                Helpers::emailSendMessage($data);
            }

            // dd($transaction);
            // $order = OfflinePoojaOrder::where('order_id', $pooja->order_id)->where('status', '1')->with(['customer'])->first();
            event(new OrderStatusEvent(key: 'offlinepuja_3', type: 'offlinepuja', order: $pooja));
            Toastr::success(translate('status_changed_successfully'));
            return redirect()->back();
        }
        Toastr::success(translate('an_error_occurred'));
        return redirect()->back();
    }

    // schedule pooja
    public function live_streams($orderId, Request $request)
    {
        $pooja = OfflinePoojaOrder::where('order_id', $orderId)->with(['offlinePooja', 'customers', 'payments', 'temple'])->first();
        if (!$pooja) {
            Toastr::error(translate('order_not_found'));
            return back();
        }
        $update = OfflinePoojaOrder::where('order_id', $orderId)->update(['status' => 4, 'live_url' => $request->live_url]);
        if ($update) {
            $userInfo = \App\Models\User::where('id', $pooja->customer_id)->first();
            $service_name = \App\Models\PoojaOffline::where('id', ($pooja['service_id'] ?? ""))->first();
            $bookingDetails = \App\Models\OfflinePoojaOrder::where('service_id', ($pooja['service_id'] ?? ""))
                ->where('customer_id', ($pooja['customer_id'] ?? ""))
                ->where('order_id', ($orderId ?? ""))->with('temple')
                ->first();

            $messageData = [
                'service_name' => $service_name->name,
                'live_stream' => $request->live_url ?? 'mahakal.com',
                'puja' => 'Pandit Book',
                'scheduled_time' => date('h:i A', strtotime($pooja->time_schedule)) ?? 'N/A',
                'puja_venue' => $pooja->pooja_venue_type == 'address' ? $pooja->venue_address : $pooja->temple->name,
                'booking_date' => date('d-m-Y', strtotime($pooja->booking_date)),
                'customer_id' => $pooja->customer_id,
                'orderId' => $pooja->order_id,
            ];

            SendWhatsappMessage::dispatch('offlinepooja', 'Live Stream', $messageData);

            // send email
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {

                $data = [
                    'type' => 'pooja',
                    'email' => $userInfo->email,
                    'subject' => 'Pandit Book Live Now',
                    'htmlContent' =>
                    \Illuminate\Support\Facades\View::make('admin-views.email.email-template.offlinepooja-live-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                ];

                Helpers::emailSendMessage($data);
            }

            // dd($transaction);
            // $order = OfflinePoojaOrder::where('order_id', $pooja->order_id)->where('status', '1')->with(['customer'])->first();
            event(new OrderStatusEvent(key: 'offlinepuja_4', type: 'offlinepuja', order: $pooja));
            Toastr::success(translate('status_changed_successfully'));
            return redirect()->back();
        }
        Toastr::success(translate('an_error_occurred'));
        return redirect()->back();
    }

    public function cancel_poojas($orderId, Request $request)
    {
        $cancelOrder = OfflinePoojaOrder::where('order_id', $orderId)->update(['order_canceled_reason' => $request->cancel_reason, 'order_canceled' => now(), 'status' => 2, 'is_edited' => 1, 'refund_status' => 1, 'canceled_by' => 'admin', 'refund_amount' => $request->refund_amount]);
        if ($cancelOrder) {
            $walletBal = User::where('id', $request->customer_id)->get()->value('wallet_balance');
            $currentBal = $walletBal + $request->refund_amount;
            $wallet_transaction = new WalletTransaction();
            $wallet_transaction->user_id = $request->customer_id;
            $wallet_transaction->transaction_id = \Str::uuid();
            $wallet_transaction->reference = 'offline pooja order payment';
            $wallet_transaction->transaction_type = 'offline_pooja_order_place';
            $wallet_transaction->credit = $request->refund_amount;
            $wallet_transaction->balance = $currentBal;
            $wallet_transaction->save();
            User::where('id', $request->customer_id)->update(['wallet_balance' => $currentBal]);
        }
        Toastr::success(translate('offline_pooja_cancel_successfully'));
        return redirect()->route('admin.offlinepooja.order.list', ['status' => 'canceled']);
    }

    public function refund_amount($orderId)
    {
        $orderData = OfflinePoojaOrder::where('order_id', $orderId)->first();
        $refund = OfflinePoojaOrder::where('order_id', $orderId)->update(['refund_status' => 1]);
        if ($refund) {
            $prevWalletAmt = User::where('id', $orderData['customer_id'])->value('wallet_balance');
            $newWalletAmt = $prevWalletAmt + $orderData['refund_amount'];
            User::where('id', $orderData['customer_id'])->update(['wallet_balance' => $newWalletAmt]);
            Toastr::success(translate('amount_refunded_to_customer_wallet_successfully'));
            return redirect()->back();
        }
        Toastr::success(translate('an_error_occurred'));
        return redirect()->back();
    }

    public function orders_generate_invoice($orderId)
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = OfflinePoojaOrder::where('order_id', $orderId)->with('customers')->with('offlinePooja')->with('leads')->with('package')->with('payments')->first();
        $mpdf_view = PdfView::make('admin-views.pooja.offlinepoojaorder.invoice', compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        $this->generatePdf($mpdf_view, 'order_invoice_', $details['order_id']);
    }

    public function lead_list(Request $request)
    {
        if ($request->has('searchValue')) {
            $leads = OfflineLead::where('person_name', 'like', '%' . $request->searchValue . '%')->where('status', 1)->with('offlinePooja', 'followBy')->orderBy('created_at', 'DESC')->paginate(10);
        } else {
            $leads = OfflineLead::where('status', 1)->with('offlinePooja', 'followBy')->orderBy('created_at', 'DESC')->paginate(10);
        }
        // dd($leads);
        return view('admin-views.pooja.offlinepoojalead.list', compact('leads'));
    }

    public function lead_delete($id, Request $request)
    {
        $lead = OfflineLead::where('id', $id)->first();
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
            'customer_name' => $request->input('customer_id'),
            'pooja_id' => $request->input('pooja_id'),
            'lead_id' => $request->input('lead_id'),
            'follow_by' => $request->input('follow_by'),
            'follow_by_id' => $request->input('follow_by_id'),
            'last_date' => $request->input('last_date'),
            'message' => $request->input('message'),
            'next_date' => $request->input('next_date'),
        ];
        OfflinepoojaFollowup::create($follows);
        //  dd($followStore);
        Toastr::success(translate('lead_follow_up_successfully'));
        return back();
    }
    public function getFollowList($id)
    {
        $followlist = OfflinepoojaFollowup::where('lead_id', $id)->get();
        return response()->json($followlist);
    }

    public function checked_order()
    {
        $offlinepooja = OfflinePoojaOrder::where('checked', 0)->update(['checked' => 1]);
        if ($offlinepooja) {
            return response()->json(['status' => 200]);
        }
        return response()->json(['status' => 400]);
    }

    public function send_whatsapp_leads($id)
    {
        $lead = OfflineLead::where('id', $id)->first();
        $poojaName = PoojaOffline::where('status', 1)->where('id', $lead->pooja_id)->first();
        $customer = User::where('is_active', 1)->where('phone', $lead->person_phone)->first();

        if ($lead) {
            $message_data = [
                'service_name' => $poojaName->name,
                'type' => 'text-with-media',
                'attachment' => asset('/storage/app/public/offlinepooja/thumbnail/' . $poojaName->thumbnail),
                'link' => 'mahakal.com/offline/pooja/detail/' . $poojaName->slug,
                'customer_id' => ($customer->id ?? ""),
            ];

            $messages =  Helpers::whatsappMessage('offlinepooja', 'Lead Message', $message_data);
            OfflineLead::where('id', $id)->increment('whatsapp_hit');
            Toastr::success(translate('message_sent_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }

    public function pending_payment_request(Request $request)
    {
        $order = OfflinePoojaOrder::where('order_id', $request->order_id)->with('offlinePooja')->first();
        $redirect_link = $this->pending_customer_payment_request($request, $order);
        $linkid = explode('=', $redirect_link)['1'];
        PaymentRequest::where('id', $linkid)->update(['previous_url' => url('offlinepooja/order/fail')]);

        // whatsapp message
        $message_data = [
            'service_name' => $order->offlinePooja->name,
            // 'type' => 'text-with-media',
            // 'attachment' =>  asset('/storage/app/public/offlinepooja/thumbnail/' . $order->offlinePooja->thumbnail),
            'final_amount' => webCurrencyConverter((float)($order->package_main_price)),
            'customer_id' => $order->customer_id,
            'payment_link' => $redirect_link
        ];

        Helpers::whatsappMessage('offlinepooja', 'Pending Order Request', $message_data);
        Toastr::success(translate('message_sent_successfully'));
        return back();
    }

    public function pending_customer_payment_request($request, $order)
    {
        $companyName = BusinessSetting::where('type', 'company_name')->value('value') ?? 'Company Name';
        $companyLogo = asset('storage/app/public/company/' . Helpers::get_business_settings('company_web_logo'));

        $additional_data = [
            'business_name' => $companyName,
            'business_logo' => $companyLogo,
            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
            'leads_id' => $order->leads_id,
            'order_id' => $request->order_id,
            'package_id' => $order->package_id,
            'service_id' => $order->service_id,
            'customer_id' => $order->customer_id,
            'package_main_price' => $order->package_main_price,
            'package_price' => $order->package_price,
            'final_amount' => $order->package_main_price,
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
            payment_amount: $order->package_main_price,
            external_redirect_link: $request->payment_platform == 'web' ? $request->external_redirect_link : null,
            attribute: 'pandit_booking',
            attribute_id: idate("U")
        );

        $receiver_info = new Receiver('receiver_name', 'example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return $redirect_link;
    }

    public function getCustomerOrders(Request $request)
    {
        $orders = OfflinePoojaOrder::where('customer_id', $request->customer_id)->where('is_block', '!=', 9)
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

        OfflinePoojaOrder::whereIn('order_id', $orderIds)
            ->update(['is_block' => 9]);

        return response()->json([
            'message' => 'Selected orders have been blocked successfully.'
        ]);
    }
}
