<?php

namespace App\Http\Controllers\Customer;
use App\Contracts\Repositories\EventsRepositoryInterface;
use App\Contracts\Repositories\ServiceRepositoryInterface;
use App\Utils\Helpers;
use App\Utils\ApiHelper;
use App\Http\Controllers\Controller;
use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Library\Receiver;
use App\Models\ShippingAddress;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\ShippingType; 
use App\Models\PaymentRequest;
use App\Models\Chadhava_orders;
use App\Models\BusinessSetting;
use App\Models\Cart;
use App\Models\CartShipping;
use App\Models\Service_order;
use App\Models\Leads;
use App\Models\Currency;
use App\Models\EventApproTransaction;
use App\Models\EventOrder;
use App\Models\EventLeads;
use App\Models\EventOrderItems;
use App\Models\EventOrganizer;
use App\Models\Events;
use App\Models\BirthJournalKundali;
use App\Models\KundaliLeads;
use App\Models\DonateAds;
use App\Models\DonateAllTransaction;
use App\Models\DonateLeads;
use App\Models\DonateTrust;
use App\Models\TourLeads;
use App\Models\TourOrder;
use App\Models\TourVisits;
use App\Traits\Payment;
use App\Utils\CartManager;
use App\Utils\Convert;
use App\Utils\OrderManager;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use function App\Utils\currency_converter;
use Razorpay\Api\Api;
use App\Traits\Whatsapp;
use App\Models\Admin;
use App\Models\WConsultancyTemplate;
use App\Models\WhatsappTemplate;


class oldPaymentController extends Controller
{
    use Whatsapp;
    public function __construct(PaymentRequest $payment,private EventsRepositoryInterface $eventsRepository,private ServiceRepositoryInterface $serviceRepository)
    {
        $config = DB::table('addon_settings')->where('key_name', 'razor_pay')->where('settings_type', 'payment_config')->first();
        $razor = false;
        if (!is_null($config) && $config->mode == 'live') {
            $razor = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $razor = json_decode($config->test_values);
        }

        if ($razor) {
            $config = array(
                'api_key' => $razor->api_key,
                'api_secret' => $razor->api_secret
            );
            Config::set('razor_config', $config);
        }

        $this->payment = $payment;
        $this->eventsRepository = $eventsRepository;

    }
    
    public function payment(Request $request)
    {
        $user = Helpers::get_customer($request);
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
            'payment_platform' => 'required',
        ]);

        $validator->sometimes('customer_id', 'required', function ($input) {
            return in_array($input->payment_request_from, ['app', 'react']);
        });
        $validator->sometimes('is_guest', 'required', function ($input) {
            return in_array($input->payment_request_from, ['app', 'react']);
        });

        if ($validator->fails()) { //api
            $errors = Helpers::error_processor($validator);
            if(in_array($request->payment_request_from, ['app', 'react'])){
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }else{
                foreach ($errors as $value) {
                    Toastr::error(translate($value['message']));
                }
                return back();
            }
        }

        $cart_group_ids = CartManager::get_cart_group_ids();
        $carts = Cart::whereIn('cart_group_id', $cart_group_ids)->get();
        $product_stock = CartManager::product_stock_check($carts);
        if(!$product_stock && in_array($request->payment_request_from, ['app', 'react'])){
            return response()->json(['errors' => ['code' => 'product-stock', 'message' => 'The following items in your cart are currently out of stock']], 403);
        }elseif(!$product_stock){
            Toastr::error(translate('the_following_items_in_your_cart_are_currently_out_of_stock'));
            return redirect()->route('shop-cart');
        }

        $verifyStatus = OrderManager::minimum_order_amount_verify($request);
        if($verifyStatus['status'] == 0 && in_array($request->payment_request_from, ['app', 'react'])){
            return response()->json(['errors' => ['code' => 'Check the minimum order amount requirement']], 403);
        }elseif($verifyStatus['status'] == 0){
            Toastr::info('Check the minimum order amount requirement');
            return redirect()->route('shop-cart');
        }

        if(in_array($request->payment_request_from, ['app', 'react'])) {
            $shippingMethod = Helpers::get_business_settings('shipping_method');
            $physical_product = false;
            foreach ($carts as $cart) {
                if ($cart->product_type == 'physical') {
                    $physical_product = true;
                }

                if ($shippingMethod == 'inhouse_shipping') {
                    $admin_shipping = ShippingType::where('seller_id', 0)->first();
                    $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
                } else {
                    if ($cart->seller_is == 'admin') {
                        $admin_shipping = ShippingType::where('seller_id', 0)->first();
                        $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
                    } else {
                        $seller_shipping = ShippingType::where('seller_id', $cart->seller_id)->first();
                        $shipping_type = isset($seller_shipping) == true ? $seller_shipping->shipping_type : 'order_wise';
                    }
                }

                if ($shipping_type == 'order_wise') {
                    $cart_shipping = CartShipping::where('cart_group_id', $cart->cart_group_id)->first();
                    if (!isset($cart_shipping) && $physical_product) {
                        return response()->json(['errors' => ['code' => 'shipping-method', 'message' => 'Data not found']], 403);
                    }
                }
            }
        }

        $redirect_link = $this->customer_payment_request($request);

        if(in_array($request->payment_request_from, ['app', 'react'])) {
            return response()->json(['redirect_link'=>$redirect_link], 200);
        }else{
            return redirect($redirect_link);
        }
    }

    public function success()
    {
        return response()->json(['message' => 'Payment succeeded'], 200);
    }

    public function fail()
    {
        return response()->json(['message' => 'Payment failed'], 403);
    }

    public function web_payment_success(Request $request)
    {
        if($request->flag == 'success') {
            if (session()->has('payment_mode') && session('payment_mode') == 'app') {
                return response()->json(['message' => 'Payment succeeded'], 200);
            } else {
                Toastr::success(translate('Payment_success'));
                return view(VIEW_FILE_NAMES['order_complete']);
            }
        }else{
            if(session()->has('payment_mode') && session('payment_mode') == 'app'){
                return response()->json(['message' => 'Payment failed'], 403);
            }else{
                Toastr::error(translate('Payment_failed').'!');
                return redirect(url('/'));
            }
        }

    }

    public function customer_payment_request(Request $request)
    {
        $additional_data = [
            'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
            'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
        ];

        $user = Helpers::get_customer($request);
        if(in_array($request->payment_request_from, ['app', 'react'])){
            $additional_data['customer_id'] = $request->customer_id;
            $additional_data['is_guest'] = $request->is_guest;
            $additional_data['order_note'] = $request['order_note'];
            $additional_data['address_id'] = $request['address_id'];
            $additional_data['billing_address_id'] = $request['billing_address_id'];
            $additional_data['coupon_code'] = $request['coupon_code'];
            $additional_data['coupon_discount'] = $request['coupon_discount'];
            $additional_data['payment_request_from'] = $request->payment_request_from;
        }

        $currency_model = Helpers::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            $currency_code = 'USD';
        } else {
            $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
            $currency_code = Currency::find($default)->code;
        }

        if(in_array($request->payment_request_from, ['app', 'react'])) {
            $cart_group_ids = CartManager::get_cart_group_ids($request);
            $cart_amount = 0;
            $shipping_cost_saved = 0;
            foreach ($cart_group_ids as $group_id) {
                $cart_amount += CartManager::api_cart_grand_total($request, $group_id);
                $shipping_cost_saved += CartManager::get_shipping_cost_saved_for_free_delivery($group_id);
            }
            $payment_amount = $cart_amount - $request['coupon_discount'] - $shipping_cost_saved;
        }else{
            $discount = session()->has('coupon_discount') ? session('coupon_discount') : 0;
            $order_wise_shipping_discount = CartManager::order_wise_shipping_discount();
            $shipping_cost_saved = CartManager::get_shipping_cost_saved_for_free_delivery();
            $payment_amount = CartManager::cart_grand_total() - $discount - $order_wise_shipping_discount - $shipping_cost_saved;
        }

        $customer = Helpers::get_customer($request);

        if($customer == 'offline'){
            $address = ShippingAddress::where(['customer_id'=>$request->customer_id, 'is_guest'=>1])->latest()->first();
            if($address){
                $payer = new Payer(
                    $address->contact_person_name,
                    $address->email,
                    $address->phone,
                    ''
                );
            }else {
                $payer = new Payer(
                    'Contact person name',
                    '',
                    '',
                    ''
                );
            }
        }else{
            $payer = new Payer(
                $customer['f_name'] . ' ' . $customer['l_name'] ,
                $customer['email'],
                $customer['phone'],
                ''
            );
            if(empty($customer['phone'])){
                Toastr::error(translate('please_update_your_phone_number'));
                return route('checkout-payment');
            }
        }

        $payment_info = new PaymentInfo(
            success_hook: 'digital_payment_success',
            failure_hook: 'digital_payment_fail',
            currency_code: $currency_code,
            payment_method: $request->payment_method,
            payment_platform: $request->payment_platform,
            payer_id: $customer=='offline' ? $request->customer_id : $customer['id'],
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: $payment_amount,
            external_redirect_link: $request->payment_platform == 'web' ? $request->external_redirect_link : null,
            attribute: 'order',
            attribute_id: idate("U")
        );

        $receiver_info = new Receiver('receiver_name','example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return $redirect_link;
    }

    public function customer_add_to_fund_request(Request $request)
    {
        if(Helpers::get_business_settings('add_funds_to_wallet') != 1)
        {
            if(in_array($request->payment_request_from, ['app', 'react'])){
                return response()->json(['message' => 'Add funds to wallet is deactivated'], 403);
            }

            Toastr::error(translate('add_funds_to_wallet_is_deactivated'));
            return back();
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'payment_method' => 'required',
            'payment_platform' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = Helpers::error_processor($validator);
            if(in_array($request->payment_request_from, ['app', 'react'])){
                return response()->json(['errors' => $errors]);
            }else{
                foreach ($errors as $value) {
                    Toastr::error(translate($value['message']));
                }
                return back();
            }
        }

        $currency_model = Helpers::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            $default_currency = Currency::find(Helpers::get_business_settings('system_default_currency'));
            $currency_code = $default_currency['code'];
            $current_currency = $request->current_currency_code ?? session('currency_code');
        } else {
            $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
            $currency_code = Currency::find($default)->code;
            $current_currency = $currency_code;
        }


        $minimum_add_fund_amount = Helpers::get_business_settings('minimum_add_fund_amount') ?? 0;
        $maximum_add_fund_amount = Helpers::get_business_settings('maximum_add_fund_amount') ?? 0;

        if(!(Convert::usdPaymentModule($request->amount, $current_currency) >= Convert::usdPaymentModule($minimum_add_fund_amount, 'USD')) || !(Convert::usdPaymentModule($request->amount, $current_currency) <= Convert::usdPaymentModule($maximum_add_fund_amount, 'USD')))
        {
            $errors = [
                'minimum_amount' => $minimum_add_fund_amount ?? 0,
                'maximum_amount' => $maximum_add_fund_amount ?? 1000,
            ];
            if(in_array($request->payment_request_from, ['app'])){
                return response()->json($errors, 202);
            }elseif(in_array($request->payment_request_from, ['react'])){
                return response()->json($errors, 403);
            }else{
                Toastr::error(translate('the_amount_needs_to_be_between').' '.currency_converter($minimum_add_fund_amount).' - '.currency_converter($maximum_add_fund_amount));
                return back();
            }
        }

        $additional_data = [
            'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
            'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
        ];

        $customer = Helpers::get_customer($request);

        if(in_array($request->payment_request_from, ['app', 'react'])){
            $additional_data['customer_id'] = $customer->id;
            $additional_data['payment_request_from'] = $request->payment_request_from;
        }

        $payer = new Payer(
            $customer->f_name . ' ' . $customer->l_name,
            $customer['email'],
            $customer->phone,
            ''
        );

        $payment_info = new PaymentInfo(
            success_hook: 'add_fund_to_wallet_success',
            failure_hook: 'add_fund_to_wallet_fail',
            currency_code: $currency_code,
            payment_method: $request->payment_method,
            payment_platform: $request->payment_platform,
            payer_id: $customer->id,
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: Convert::usdPaymentModule($request->amount, $current_currency),
            external_redirect_link: $request->payment_platform == 'web' ? $request->external_redirect_link : null,
            attribute: 'add_funds_to_wallet',
            attribute_id: idate("U")
        );

        $receiver_info = new Receiver('receiver_name','example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        if(in_array($request->payment_request_from, ['app', 'react'])) {
            return response()->json(['redirect_link'=>$redirect_link], 200);
        }else{
            return redirect($redirect_link);
        }
    }
     // Pooja  Order Payment Getway
     public function servicespayment(Request $request){
        // dd($request->all());
        $wallet=User::select('wallet_balance')->where('id',$request->customer_id)->first();
        if($request->payment_amount > 0){   
            $user = Helpers::get_customer($request);
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
            'payment_platform' => 'required',
        ]);
        $validator->sometimes('customer_id', 'required', function ($input) {
            return in_array($input->payment_request_from, ['app', 'react']);
        });
        
        $redirect_link = $this->services_customer_payment_request($request);
        // dd($redirect_link,$request);
        if(in_array($request->payment_request_from, ['app', 'react'])) {
            return response()->json(['redirect_link'=>$redirect_link], 200);
        }else{
            return redirect($redirect_link);
        }
    }else{
        //  dd($request->all());
            $final_amount =$wallet['wallet_balance'] - $request->wallet_balance;
            // dd($final_amount,$wallet['wallet_balance'] ,$request->payment_amount);
            $additional_data = [
                'leads_id' => $request->leads_id,
                'package_id' => $request->package_id,
                'service_id' => $request->service_id,
                'customer_id' => $request->customer_id,
                'package_price' => $request->package_price,
                'booking_date' => $request->booking_date,
                'pandit_assign' => $request->pandit_assign,
            ];
            $orderId='';
            $orderData=Service_order::select('id')->latest()->first();
            if(!empty($orderData['id'])){
                $orderId='PJ'.(100000 + $orderData['id'] + 1);
            }else{
                $orderId='PJ'.(100001);
            }
           
            // Wallet Transection Details
            $serviceData=$additional_data;
            $wallet_transaction = new WalletTransaction();
            $wallet_transaction->user_id = $serviceData['customer_id'];
            $wallet_transaction->transaction_id = \Str::uuid();
            $wallet_transaction->reference = 'pooja order payment';
            $wallet_transaction->transaction_type = 'pooja_order_place';
            $wallet_transaction->balance = $final_amount;
            $wallet_transaction->debit = $request->wallet_balance;
            // dd($wallet_transaction);
            $wallet_transaction->save();
            User::where('id', $serviceData['customer_id'])->update(['wallet_balance' => $final_amount]);
            // Service Transection Details
            $serviceOrderAdd=new Service_order();
            $serviceOrderAdd->customer_id=$serviceData['customer_id'];
            $serviceOrderAdd->service_id=$serviceData['service_id'];
            $serviceOrderAdd->type='pooja';
            $serviceOrderAdd->leads_id=$serviceData['leads_id'];
            $serviceOrderAdd->package_id=$serviceData['package_id'];
            $serviceOrderAdd->package_price=$serviceData['package_price'];
            $serviceOrderAdd->booking_date=$serviceData['booking_date'];
            $serviceOrderAdd->pandit_assign=$serviceData['pandit_assign'];
            $serviceOrderAdd->pay_amount= $request->final_amount;
            $serviceOrderAdd->wallet_amount= $request->wallet_balance;
            $serviceOrderAdd->wallet_translation_id = $wallet_transaction->transaction_id;
            $serviceOrderAdd->order_id = $orderId;
            $serviceOrderAdd->coupon_amount = session()->get('coupon_discount_pooja');
            $serviceOrderAdd->coupon_code = session()->get('coupon_code_pooja');
            // dd($request->all());
            // dd($serviceOrderAdd);
            $serviceOrderAdd->save();
            $userInfo = \App\Models\User::where('id', ($serviceData['customer_id'] ?? ""))->first();
            if ($userInfo['email'] != $serviceData['customer_id']) {
                $bookingDate = $serviceData['booking_date'];
                $orderId = $serviceOrderAdd->order_id;
                $payAmount = $request->final_amount;
                $message = "Booking Complete\n\n";
                $message .= "Pooja Booking Date: $bookingDate\n";
                $message .= "Pooja Order ID: $orderId\n";
                $message .= "Your Pay for the Wallet Amount: $payAmount\n";
                $service_email = [
                    'subject' => translate("Pooja_Booking_Success"),
                    'email' => $userInfo['email'],
                    'message' => $message,
                ];
                $this->serviceRepository->sendMails($service_email);
            }
           
            // dd($serviceOrderAdd);
            Leads::where('id', $additional_data['leads_id'])->update([
                'status' => 0,
                'payment_status' => 'Complete', 
                'order_id' => $orderId,
            ]);
            Toastr::success(translate('Payment_success'));
            session()->forget('coupon_discount_pooja');
            session()->forget('coupon_code_pooja');
            $url = $_SERVER['HTTP_HOST'];
                // if(!str_contains($url,'localhost')){
                    // whatsapp
                    $whatsapp = [];
                    $order_data = WhatsappTemplate::where('status', 1)->get();
                    if ($order_data) {
                        // dd($order_data);
                        $whatsapp = $order_data->toArray();
                        $orderPlaced = array_search('Pooja Confirmed', array_column($whatsapp, 'template_name'));
                        $wpMsg = $whatsapp[$orderPlaced]['body'];
                        $service_name=\App\Models\Service::where('id', ($serviceData['service_id'] ?? ""))->first();
                        $wpMsg = str_replace('{service_name}', $service_name['name'], $wpMsg);
                        $wpMsg = str_replace('{amount}','₹' . number_format($request->final_amount, 2),
                            $wpMsg
                        );
                        $wpMsg = str_replace('{order_id}', $orderId, $wpMsg);
                        $userInfo = \App\Models\User::where('id', ($serviceData['customer_id'] ?? ""))->first(); 
                        $wpMsg = str_replace('{user_name}', $userInfo['name'], $wpMsg); 
                        $wpMsg = str_replace('{booking_date}',date('Y-m-d', strtotime($serviceData['booking_date'])),
                            $wpMsg
                        );
                        $wpMsg = str_replace('{puja_venue}', $service_name['pooja_venue'], $wpMsg); 
                        // dd($wpMsg);
                        $device = Admin::where('id', 1)->where('status', 1)->first();
                       
                        if ($device) {
                            $text = $this->formatText($wpMsg);
                            $body["text"] = $text;
                            $body["message"] = $wpMsg;
                            $type = "plain-text";
                            $userInfo = \App\Models\User::where('id', ($serviceData['customer_id'] ?? ""))->first();
                            // dd($device['id']);
                            $response = $this->messageSend(
                                $body,
                               'device_mahakal_2024',
                                $userInfo['phone'],
                                $type,
                                true
                            );
                            // dd($response);
                        }
                    }
            return redirect()->route('sankalp',$orderId);
            
        }

    }

    public function services_customer_payment_request(Request $request){
           $additional_data = [
            'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
            'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
            'leads_id' => $request->leads_id,
            'package_id' => $request->package_id,
            'service_id' => $request->service_id,
            'customer_id' => $request->customer_id,
            'package_price' => $request->package_price,
            'booking_date' => $request->booking_date,
            'pandit_assign' => $request->pandit_assign,
            'wallet_balance' => $request->wallet_balance,
            'final_amount' => $request->final_amount,
        ];

        $user = Helpers::get_customer($request);
        if(in_array($request->payment_request_from, ['app', 'react'])){
            $additional_data['customer_id'] = $request->customer_id;
            $additional_data['is_guest'] = $request->is_guest;
            $additional_data['order_note'] = $request['order_note'];
            $additional_data['payment_request_from'] = $request->payment_request_from;
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

        if($customer == 'offline'){
            $address = ShippingAddress::where(['customer_id'=>$request->customer_id, 'is_guest'=>1])->latest()->first();
            if($address){
                $payer = new Payer(
                    $address->contact_person_name,
                    $address->email,
                    $address->phone,
                    ''
                );
            }else {
                $payer = new Payer(
                    'Contact person name',
                    '',
                    '',
                    ''
                );
            }
        }else{
            $payer = new Payer(
                $customer['f_name'] . ' ' . $customer['l_name'] ,
                $customer['email'],
                $customer['phone'],
                ''
            );
            if(empty($customer['phone'])){
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
            payer_id: $customer=='offline' ? $request->customer_id : $customer['id'],
            receiver_id: '100',
            additional_data: $additional_data, 
            payment_amount: $request->payment_amount,
            external_redirect_link: $request->payment_platform == 'web' ? $request->external_redirect_link : null,
            attribute: 'puja',
            attribute_id: idate("U")
        );
        // dd($payment_info);

        $receiver_info = new Receiver('receiver_name','example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return $redirect_link;
    }
    public function service_web_payment_success(Request $request){
        // dd($request->all());
        if ($request->flag == 'success') {
            $orderId='';
            $orderData=Service_order::select('id')->latest()->first();
            if(!empty($orderData['id'])){
                $orderId='PJ'.(100000 + $orderData['id'] + 1);
            }else{
                $orderId='PJ'.(100001);
            }
            $servicePaymentData=explode('transaction_reference=',base64_decode($request->token));
            $serviceOrder=PaymentRequest::where('transaction_id',$servicePaymentData['1'])->first();
            $additionalData = json_decode($serviceOrder['additional_data'], true); 
            // $paymentAmount = $serviceOrder['payment_amount'] + ($additionalData['wallet_balance'] ?? 0);
            $serviceData=json_decode($serviceOrder->additional_data);
            // dd($additionalData);
            // Wallet Maintan
            if ($serviceData->wallet_balance > 0) {
                $wallet_transaction = new WalletTransaction();
                $wallet_transaction->user_id = $serviceData->customer_id;
                $wallet_transaction->transaction_id = \Str::uuid();
                $wallet_transaction->reference = 'pooja order payment';
                $wallet_transaction->transaction_type = 'pooja_order_place';
                $wallet_transaction->balance = 0.00;
                $wallet_transaction->debit = $serviceData->wallet_balance;
                $wallet_transaction->save();
                User::where('id', $serviceData->customer_id)->update(['wallet_balance' => 0]);
            }
            // service_transaction
            $serviceOrderAdd=new Service_order();
            $serviceOrderAdd->customer_id=$serviceData->customer_id;
            $serviceOrderAdd->service_id=$serviceData->service_id;
            $serviceOrderAdd->type='pooja';
            $serviceOrderAdd->leads_id=$serviceData->leads_id;
            $serviceOrderAdd->package_id=$serviceData->package_id;
            $serviceOrderAdd->package_price=$serviceData->package_price;
            $serviceOrderAdd->booking_date=$serviceData->booking_date;
            $serviceOrderAdd->pandit_assign=$serviceData->pandit_assign;
            $serviceOrderAdd->wallet_amount= $serviceData->wallet_balance ?? 0;
            $serviceOrderAdd->transection_amount= $serviceOrder['payment_amount'];
            $serviceOrderAdd->wallet_translation_id = $wallet_transaction->transaction_id ?? null;
            $serviceOrderAdd->pay_amount=$additionalData['final_amount'];
            $serviceOrderAdd->order_id = $orderId;
            $serviceOrderAdd->coupon_amount = session()->get('coupon_discount_pooja');
            $serviceOrderAdd->coupon_code = session()->get('coupon_code_pooja');
            $serviceOrderAdd->payment_id=$serviceOrder['transaction_id'];
            // dd($serviceOrderAdd);
            $serviceOrderAdd->save();
            $userInfo = \App\Models\User::where('id', ($serviceData->customer_id ?? ""))->first();
            if ($userInfo['email'] != $serviceData->customer_id) {
                $bookingDate = $serviceData->booking_date;
                $orderId = $serviceOrderAdd->order_id;
                $payAmount = $additionalData['final_amount'];
                $message = "Booking Complete\n\n";
                $message .= "Pooja Booking Date: $bookingDate\n";
                $message .= "Pooja Order ID: $orderId\n";
                $message .= "Your Pay Amount: $payAmount\n";
                $service_email = [
                    'subject' => translate("Pooja_Booking_Success"),
                    'email' => $userInfo['email'],
                    'message' => $message,
                ];
                $this->serviceRepository->sendMails($service_email);
            }

            //  dd($serviceOrderAdd);
            Leads::where('id', $serviceData->leads_id)->update([
                'status' => 0,
                'payment_status' => 'Complete', // or 'Failed' based on your logic
                'order_id' => $orderId, // or 'Failed' based on your logic
            ]);
            if (session()->has('payment_mode') && session('payment_mode') == 'app') {
                return response()->json(['message' => 'Payment succeeded'], 200);
            } else {
                Toastr::success(translate('Payment_success'));
                // return view(VIEW_FILE_NAMES['order_complete']);
                session()->forget('coupon_discount_pooja');
                session()->forget('coupon_code_pooja');
                $url = $_SERVER['HTTP_HOST'];
                // if(!str_contains($url,'localhost')){
                    // whatsapp
                    $whatsapp = [];
                    $order_data = WhatsappTemplate::where('status', 1)->get();
                    if ($order_data) {
                        // dd($order_data);
                        $whatsapp = $order_data->toArray();
                        $orderPlaced = array_search('Pooja Confirmed', array_column($whatsapp, 'template_name'));
                        $wpMsg = $whatsapp[$orderPlaced]['body'];
                        $service_name=\App\Models\Service::where('id', ($serviceData->service_id ?? ""))->first();
                        $wpMsg = str_replace('{service_name}', $service_name['name'], $wpMsg);
                        $wpMsg = str_replace('{amount}','₹' . number_format($additionalData['final_amount'], 2),
                            $wpMsg
                        );
                        $wpMsg = str_replace('{order_id}', $orderId, $wpMsg);
                        $userInfo = \App\Models\User::where('id', ($serviceData->customer_id ?? ""))->first(); 
                        $wpMsg = str_replace('{user_name}', $userInfo['name'], $wpMsg); 
                        $wpMsg = str_replace('{booking_date}',date('Y-m-d', strtotime($serviceData->booking_date)),
                            $wpMsg
                        );
                        $wpMsg = str_replace('{puja_venue}', $service_name['pooja_venue'], $wpMsg); 
                        // dd($wpMsg);
                        $device = Admin::where('id', 1)->where('status', 1)->first();
                       
                        if ($device) {
                            $text = $this->formatText($wpMsg);
                            $body["text"] = $text;
                            $body["message"] = $wpMsg;
                            $type = "plain-text";
                            $userInfo = \App\Models\User::where('id', ($serviceData->customer_id ?? ""))->first();
                            // dd($device['id']);
                            $response = $this->messageSend(
                                $body,
                               'device_mahakal_2024',
                                $userInfo['phone'],
                                $type,
                                true
                            );
                            // dd($response);
                        }
                    }
            
                return redirect()->route('sankalp',$orderId);
               
            }
        } else {
            if(session()->has('payment_mode') && session('payment_mode') == 'app'){
                return response()->json(['message' => 'Payment failed'], 403);
            }else{
                Toastr::error(translate('Payment_failed').'!');
                return redirect(url('/'));
            }
        }

    }

    // counselling payment
    public function counsellingpayment(Request $request)
    {
        // dd($request->all());
        // lead insert
        $lead_details = [
            'service_id' => $request->input('service_id'),
            'type' => 'counselling',
            'package_price' => $request->input('payment_amount'),
            'person_phone' => $request->input('person_phone'),
            'person_name' => $request->input('person_name'),
        ];
        $leads = Leads::create($lead_details);
        $leadId = $leads->id;
        $request->merge(['lead_id' => $leadId]);
        // dd($request->all());
        //payment
        $wallet = User::select('wallet_balance')->where('id', $request->customer_id)->first();
        if ($request->payment_amount > 0) {
            $user = Helpers::get_customer($request);
            $validator = Validator::make($request->all(), [
                'payment_method' => 'required',
                'payment_platform' => 'required',
            ]);
            $validator->sometimes('customer_id', 'required', function ($input) {
                return in_array($input->payment_request_from, ['app', 'react']);
            });

            $redirect_link = $this->counselling_customer_payment_request($request);

            if (in_array($request->payment_request_from, ['app', 'react'])) {
                return response()->json(['redirect_link' => $redirect_link], 200);
            } else {
                return redirect($redirect_link);
            }
        } else {
            //  dd($request->all());
            $final_amount = $wallet['wallet_balance'] - $request->wallet_balance;
            // dd($final_amount,$wallet['wallet_balance'] ,$request->payment_amount);
            $additional_data = [
                'leads_id' => $request->lead_id,
                'service_id' => $request->service_id,
                'customer_id' => $request->customer_id,
            ];
            $orderId = '';
            $orderData = Service_order::select('id')->latest()->first();
            if (!empty($orderData['id'])) {
                $orderId = 'CL' . (100000 + $orderData['id'] + 1);
            } else {
                $orderId = 'CL' . (100001);
            }

            // Wallet Transection Details
            $serviceData = $additional_data;
            $wallet_transaction = new WalletTransaction();
            $wallet_transaction->user_id = $serviceData['customer_id'];
            $wallet_transaction->transaction_id = \Str::uuid();
            $wallet_transaction->reference = 'counselling order payment';
            $wallet_transaction->transaction_type = 'counselling_order_place';
            $wallet_transaction->balance = $final_amount;
            $wallet_transaction->debit = $request->wallet_balance;
            // dd($wallet_transaction);
            $wallet_transaction->save();
            User::where('id', $serviceData['customer_id'])->update(['wallet_balance' => $final_amount]);
            // Service Transection Details
            $serviceOrderAdd = new Service_order();
            $serviceOrderAdd->customer_id = $serviceData['customer_id'];
            $serviceOrderAdd->service_id = $serviceData['service_id'];
            $serviceOrderAdd->type = 'counselling';
            $serviceOrderAdd->leads_id = $serviceData['leads_id'];
            $serviceOrderAdd->order_id = $orderId;
            $serviceOrderAdd->coupon_amount = session()->get('coupon_discount_counselling');
            $serviceOrderAdd->coupon_code = session()->get('coupon_code_counselling');
            $serviceOrderAdd->pay_amount = $request->final_amount;
            $serviceOrderAdd->wallet_amount = $request->wallet_balance;
            $serviceOrderAdd->wallet_translation_id = $wallet_transaction->transaction_id;
            $serviceOrderAdd->order_id = $orderId;
            $serviceOrderAdd->coupon_amount = session()->get('coupon_discount_counselling');
            $serviceOrderAdd->coupon_code = session()->get('counselling_order_place');
            $serviceOrderAdd->save();

            // dd($serviceOrderAdd);
            Leads::where('id', $additional_data['leads_id'])->update([
                'status' => 0,
                'payment_status' => 'Complete',
                'order_id' => $orderId,
            ]);
            Toastr::success(translate('Payment_success'));
            session()->forget('coupon_discount_counselling');
            session()->forget('counselling_order_place');
            $url = $_SERVER['HTTP_HOST'];
                // if(!str_contains($url,'localhost')){
                    // whatsapp
            $whatsapp = [];
            $order_data = WConsultancyTemplate::where('status', 1)->get();
            if ($order_data) {
                // dd($order_data);
                $whatsapp = $order_data->toArray();
                $orderPlaced = array_search('Order Confirmed', array_column($whatsapp, 'template_name'));
                $wpMsg = $whatsapp[$orderPlaced]['body'];
                $service_name=\App\Models\Service::where('id', ($serviceData['service_id'] ?? ""))->first();
                $wpMsg = str_replace('{service_name}', $service_name['name'], $wpMsg);
                $wpMsg = str_replace('{amount}','?' . number_format($request->final_amount, 2),
                    $wpMsg
                );
                $wpMsg = str_replace('{order_id}', $orderId, $wpMsg);
                $userInfo = \App\Models\User::where('id', ($serviceData['customer_id'] ?? ""))->first(); 
                $wpMsg = str_replace('{user_name}', $userInfo['name'], $wpMsg); 
                // $wpMsg = str_replace('{booking_date}',date('Y-m-d', strtotime($serviceData['booking_date'])),
                //     $wpMsg
                // );
                // $wpMsg = str_replace('{puja_venue}', $service_name['pooja_venue'], $wpMsg); 
                // dd($wpMsg);
                $device = Admin::where('id', 1)->where('status', 1)->first();
               
                if ($device) {
                    $text = $this->formatText($wpMsg);
                    $body["text"] = $text;
                    $body["message"] = $wpMsg;
                    $type = "plain-text";
                    $userInfo = \App\Models\User::where('id', ($serviceData['customer_id'] ?? ""))->first();
                    // dd($device['id']);
                    $response = $this->messageSend(
                        $body,
                       'device_mahakal_2024',
                        $userInfo['phone'],
                        $type,
                        true
                    );
                    // dd($response);
                }
            }
            return redirect()->route('counselling.user.detail', $orderId);
        }
    }

    public function counselling_customer_payment_request(Request $request)
    {
        // dd($request->all());
        // $wallet=User::select('wallet_balance')->where('id',$request->customer_id)->first();
        // $wallet_balances=$wallet['wallet_balance'];
        $additional_data = [
            'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
            'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
            'leads_id' => $request->lead_id,
            'service_id' => $request->service_id,
            'customer_id' => $request->customer_id,
            'wallet_balance' => $request->wallet_balance,
            'final_amount' => $request->final_amount,
        ];
        // dd($additional_data);

        $user = Helpers::get_customer($request);
        if (in_array($request->payment_request_from, ['app', 'react'])) {
            $additional_data['customer_id'] = $request->customer_id;
            $additional_data['is_guest'] = $request->is_guest;
            $additional_data['order_note'] = $request['order_note'];
            $additional_data['payment_request_from'] = $request->payment_request_from;
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
            $address = ShippingAddress::where(['customer_id' => $request->customer_id, 'is_guest' => 1])->latest()->first();
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
            payer_id: $customer == 'offline' ? $request->customer_id : $customer['id'],
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: $request->payment_amount,
            external_redirect_link: $request->payment_platform == 'web' ? $request->external_redirect_link : null,
            attribute: 'counselling',
            attribute_id: idate("U")
        );
        // dd($payment_info);

        $receiver_info = new Receiver('receiver_name', 'example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return $redirect_link;
    }
    
    public function counselling_web_payment_success(Request $request)
    {

        if ($request->flag == 'success') {
            $orderId = '';
            $orderData = Service_order::select('id')->latest()->first();
            if (!empty($orderData['id'])) {
                $orderId = 'CL' . (100000 + $orderData['id'] + 1);
            } else {
                $orderId = 'CL' . (100001);
            }
            $servicePaymentData = explode('transaction_reference=', base64_decode($request->token));
            $serviceOrder = PaymentRequest::where('transaction_id', $servicePaymentData['1'])->first();
            // $serviceData=json_decode($serviceOrder->additional_data);
            $additionalData = json_decode($serviceOrder['additional_data'], true);
            // $paymentAmount = $serviceOrder['payment_amount'] + ($additionalData['wallet_balance'] ?? 0);
            // dd($paymentAmount);
            $serviceData = json_decode($serviceOrder->additional_data);
            // Wallet Maintan

            if ($serviceData->wallet_balance > 0) {
                $wallet_transaction = new WalletTransaction();
                $wallet_transaction->user_id = $serviceData->customer_id;
                $wallet_transaction->transaction_id = \Str::uuid();
                $wallet_transaction->reference = 'counselling order payment';
                $wallet_transaction->transaction_type = 'counselling_order_place';
                $wallet_transaction->balance = 00.00;
                $wallet_transaction->debit = $serviceData->wallet_balance ?? 0;
                //    dd($wallet_transaction);
                $wallet_transaction->save();
                User::where('id', $serviceData->customer_id)->update(['wallet_balance' => 0]);
            }

            $serviceOrderAdd = new Service_order();
            $serviceOrderAdd->customer_id = $serviceData->customer_id;
            $serviceOrderAdd->service_id = $serviceData->service_id;
            $serviceOrderAdd->type = 'counselling';
            $serviceOrderAdd->leads_id = $serviceData->leads_id;
            $serviceOrderAdd->order_id = $orderId;
            $serviceOrderAdd->payment_id = $serviceOrder['transaction_id'];
            $serviceOrderAdd->wallet_amount = $serviceData->wallet_balance ?? 0;
            $serviceOrderAdd->transection_amount = $serviceOrder['payment_amount'];
            $serviceOrderAdd->wallet_translation_id = $wallet_transaction->transaction_id ?? null;
            $serviceOrderAdd->pay_amount = $additionalData['final_amount'];
            $serviceOrderAdd->coupon_amount = session()->get('coupon_discount_counselling');
            $serviceOrderAdd->coupon_code = session()->get('coupon_code_counselling');
            // dd($serviceOrderAdd);
            $serviceOrderAdd->save();

            Leads::where('id', $serviceData->leads_id)->update(['status' => 0]);

            if (session()->has('payment_mode') && session('payment_mode') == 'app') {
                return response()->json(['message' => 'Payment succeeded'], 200);
            } else {
                Toastr::success(translate('Payment_success'));
                session()->forget('coupon_discount_counselling');
                session()->forget('coupon_code_counselling');
                 $url = $_SERVER['HTTP_HOST'];
                // if(!str_contains($url,'localhost')){
                    // whatsapp
                $whatsapp = [];
                $order_data = WConsultancyTemplate::where('status', 1)->get();
                if ($order_data) {
                    // dd($order_data);
                    $whatsapp = $order_data->toArray();
                    $orderPlaced = array_search('Order Confirmed', array_column($whatsapp, 'template_name'));
                    $wpMsg = $whatsapp[$orderPlaced]['body'];
                    $service_name=\App\Models\Service::where('id', ($serviceData->service_id ?? ""))->first();
                    $wpMsg = str_replace('{service_name}', $service_name['name'], $wpMsg);
                    $wpMsg = str_replace('{amount}','?' . number_format($additionalData['final_amount'], 2),
                        $wpMsg
                    );
                    $wpMsg = str_replace('{order_id}', $orderId, $wpMsg);
                    $userInfo = \App\Models\User::where('id', ($serviceData->customer_id ?? ""))->first(); 
                    $wpMsg = str_replace('{user_name}', $userInfo['name'], $wpMsg); 
                    // $wpMsg = str_replace('{booking_date}',date('Y-m-d', strtotime($serviceData['booking_date'])),
                    //     $wpMsg
                    // );
                    // $wpMsg = str_replace('{puja_venue}', $service_name['pooja_venue'], $wpMsg); 
                    // dd($wpMsg);
                    $device = Admin::where('id', 1)->where('status', 1)->first();
                   
                    if ($device) {
                        $text = $this->formatText($wpMsg);
                        $body["text"] = $text;
                        $body["message"] = $wpMsg;
                        $type = "plain-text";
                        $userInfo = \App\Models\User::where('id', ($serviceData->customer_id ?? ""))->first();
                        // dd($device['id']);
                        $response = $this->messageSend(
                            $body,
                           'device_mahakal_2024',
                            $userInfo['phone'],
                            $type,
                            true
                        );
                        // dd($response);
                    }
                }
                return redirect()->route('counselling.user.detail', $orderId);
            }
        } else {
            if (session()->has('payment_mode') && session('payment_mode') == 'app') {
                return response()->json(['message' => 'Payment failed'], 403);
            } else {
                Toastr::error(translate('Payment_failed') . '!');
                return redirect(url('/'));
            }
        }
    }


    // ----------------------------------------VIP POOJA PAYMENT METHOD WORKING ON 25/07/2024----------------------------------------------------
    public function vippooja_payment(Request $request){
        // dd($request->all());
        $wallet=User::select('wallet_balance')->where('id',$request->customer_id)->first();
        if($request->payment_amount > 0){   
        $user = Helpers::get_customer($request);
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
            'payment_platform' => 'required',
        ]);
        $validator->sometimes('customer_id', 'required', function ($input) {
            return in_array($input->payment_request_from, ['app', 'react']);
        });
        
        $redirect_link = $this->vippooja_customer_payment_request($request);

        if(in_array($request->payment_request_from, ['app', 'react'])) {
            return response()->json(['redirect_link'=>$redirect_link], 200);
        }else{
            return redirect($redirect_link);
        }
    }else{
        $final_amount =$wallet['wallet_balance'] - $request->wallet_balance;
            // dd($final_amount,$wallet['wallet_balance'] ,$request->payment_amount);
            $additional_data = [
                'leads_id' => $request->leads_id,
                'package_id' => $request->package_id,
                'service_id' => $request->service_id,
                'customer_id' => $request->customer_id,
                'package_price' => $request->package_price,
                'booking_date' => $request->booking_date,
                'pandit_assign' => $request->pandit_assign,
            ];
            $orderId='';
            $orderData=Service_order::select('id')->latest()->first();
            if(!empty($orderData['id'])){
                $orderId='VPJ'.(100000 + $orderData['id'] + 1);
            }else{
                $orderId='VPJ'.(100001);
            }           
            // Wallet Transection Details
            $serviceData=$additional_data;
            $wallet_transaction = new WalletTransaction();
            $wallet_transaction->user_id = $serviceData['customer_id'];
            $wallet_transaction->transaction_id = \Str::uuid();
            $wallet_transaction->reference = 'vip order payment';
            $wallet_transaction->transaction_type = 'vip_order_place';
            $wallet_transaction->balance = $final_amount;
            $wallet_transaction->debit = $request->wallet_balance;
            // dd($wallet_transaction);
            $wallet_transaction->save();
            User::where('id', $serviceData['customer_id'])->update(['wallet_balance' => $final_amount]);
            // Service Transection Details
            $serviceOrderAdd=new Service_order();
            $serviceOrderAdd->customer_id=$serviceData['customer_id'];
            $serviceOrderAdd->service_id=$serviceData['service_id'];
            $serviceOrderAdd->type='vip';
            $serviceOrderAdd->leads_id=$serviceData['leads_id'];
            $serviceOrderAdd->package_id=$serviceData['package_id'];
            $serviceOrderAdd->package_price=$serviceData['package_price'];
            $serviceOrderAdd->booking_date=$serviceData['booking_date'];
            $serviceOrderAdd->pandit_assign=$serviceData['pandit_assign'];
            $serviceOrderAdd->pay_amount= $request->final_amount;
            $serviceOrderAdd->wallet_amount= $request->wallet_balance;
            $serviceOrderAdd->wallet_translation_id = $wallet_transaction->transaction_id;
            $serviceOrderAdd->order_id = $orderId;
            if($serviceData['package_id'] == 5){
            $serviceOrderAdd->coupon_amount = session()->get('coupon_discount_vippooja');
            $serviceOrderAdd->coupon_code = session()->get('coupon_code_vippooja');
            }elseif($serviceData['package_id'] == 6){
            $serviceOrderAdd->coupon_amount = session()->get('coupon_discount_instancevip');
            $serviceOrderAdd->coupon_code = session()->get('coupon_code_instancevip');
            }
            // dd($request->all());
            // dd($serviceOrderAdd);
            $serviceOrderAdd->save();
           
            // dd($serviceOrderAdd);
            Leads::where('id', $additional_data['leads_id'])->update([
                'status' => 0,
                'payment_status' => 'Complete', 
                'order_id' => $orderId,
            ]);
            Toastr::success(translate('Payment_success'));
            session()->forget('coupon_discount_vippooja');
            session()->forget('coupon_code_vippooja');
            session()->forget('coupon_discount_instancevip');
            session()->forget('coupon_code_instancevip');
            return redirect()->route('vip.user.detail',$orderId);
    }

    }

    public function vippooja_customer_payment_request(Request $request){
        $additional_data = [
            'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
            'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
            'leads_id' => $request->leads_id,
            'package_id' => $request->package_id,
            'service_id' => $request->service_id,
            'customer_id' => $request->customer_id,
            'package_price' => $request->package_price,
            'booking_date' => $request->booking_date,
            'pandit_assign' => $request->pandit_assign,
            'wallet_balance' => $request->wallet_balance,
            'final_amount' => $request->final_amount,
        ];

        $user = Helpers::get_customer($request);
        if(in_array($request->payment_request_from, ['app', 'react'])){
            $additional_data['customer_id'] = $request->customer_id;
            $additional_data['is_guest'] = $request->is_guest;
            $additional_data['order_note'] = $request['order_note'];
            $additional_data['payment_request_from'] = $request->payment_request_from;
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

        if($customer == 'offline'){
            $address = ShippingAddress::where(['customer_id'=>$request->customer_id, 'is_guest'=>1])->latest()->first();
            if($address){
                $payer = new Payer(
                    $address->contact_person_name,
                    $address->email,
                    $address->phone,
                    ''
                );
            }else {
                $payer = new Payer(
                    'Contact person name',
                    '',
                    '',
                    ''
                );
            }
        }else{
            $payer = new Payer(
                $customer['f_name'] . ' ' . $customer['l_name'] ,
                $customer['email'],
                $customer['phone'],
                ''
            );
            if(empty($customer['phone'])){
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
            payer_id: $customer=='offline' ? $request->customer_id : $customer['id'],
            receiver_id: '100',
            additional_data: $additional_data, 
            payment_amount: $request->payment_amount,
            external_redirect_link: $request->payment_platform == 'web' ? $request->external_redirect_link : null,
            attribute: 'vippuja',
            attribute_id: idate("U")
        );

        $receiver_info = new Receiver('receiver_name','example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return $redirect_link;
    }

    public function vippooja_web_payment_success(Request $request){
        if ($request->flag == 'success') {
            $orderId='';
            $orderData=Service_order::select('id')->latest()->first();
            if(!empty($orderData['id'])){
                $orderId='VPJ'.(100000 + $orderData['id'] + 1);
            }else{
                $orderId='VPJ'.(100001);
            }
            $servicePaymentData=explode('transaction_reference=',base64_decode($request->token));
            $serviceOrder=PaymentRequest::where('transaction_id',$servicePaymentData['1'])->first();
            $additionalData = json_decode($serviceOrder['additional_data'], true); 
            // dd($serviceOrder);
            $serviceData=json_decode($serviceOrder->additional_data);
              // Wallet Maintan
            if ($serviceData->wallet_balance > 0) {
                $wallet_transaction = new WalletTransaction();
                $wallet_transaction->user_id = $serviceData->customer_id;
                $wallet_transaction->transaction_id = \Str::uuid();
                $wallet_transaction->reference = 'vip order payment';
                $wallet_transaction->transaction_type = 'vip_order_place';
                $wallet_transaction->balance = 0.00;
                $wallet_transaction->debit = $serviceData->wallet_balance;
                $wallet_transaction->save();
                User::where('id', $serviceData->customer_id)->update(['wallet_balance' => 0]);
            }
            // service_transaction
            $serviceOrderAdd=new Service_order();
            $serviceOrderAdd->customer_id=$serviceData->customer_id;
            $serviceOrderAdd->service_id=$serviceData->service_id;
            $serviceOrderAdd->type='vip';
            $serviceOrderAdd->leads_id=$serviceData->leads_id;
            $serviceOrderAdd->package_id=$serviceData->package_id;
            $serviceOrderAdd->package_price=$serviceData->package_price;
            $serviceOrderAdd->booking_date=$serviceData->booking_date;
            $serviceOrderAdd->pandit_assign=$serviceData->pandit_assign;
            $serviceOrderAdd->order_id = $orderId;
            if($serviceData->package_id == 5){
            $serviceOrderAdd->coupon_amount = session()->get('coupon_discount_vippooja');
            $serviceOrderAdd->coupon_code = session()->get('coupon_code_vippooja');
            }elseif($serviceData->package_id == 6){
            $serviceOrderAdd->coupon_amount = session()->get('coupon_discount_instancevip');
            $serviceOrderAdd->coupon_code = session()->get('coupon_code_instancevip');
            }
            $serviceOrderAdd->payment_id=$serviceOrder['transaction_id'];
            $serviceOrderAdd->wallet_amount= $serviceData->wallet_balance ?? 0;
            $serviceOrderAdd->transection_amount= $serviceOrder['payment_amount'];
            $serviceOrderAdd->wallet_translation_id = $wallet_transaction->transaction_id ?? null;
            $serviceOrderAdd->pay_amount=$additionalData['final_amount'];
            $serviceOrderAdd->save();
            //  dd($serviceOrderAdd);
            Leads::where('id', $serviceData->leads_id)->update([
                'status' => 0,
                'payment_status' => 'Complete',
                'order_id' => $orderId,
            ]);
            if (session()->has('payment_mode') && session('payment_mode') == 'app') {
                return response()->json(['message' => 'Payment succeeded'], 200);
            } else {
                Toastr::success(translate('Payment_success'));
                session()->forget('coupon_discount_vippooja');
                session()->forget('coupon_code_vippooja');
                session()->forget('coupon_discount_instancevip');
                session()->forget('coupon_code_instancevip');
                return redirect()->route('vip.user.detail',$orderId);
               
            }
        } else {
            if(session()->has('payment_mode') && session('payment_mode') == 'app'){
                return response()->json(['message' => 'Payment failed'], 403);
            }else{
                Toastr::error(translate('Payment_failed').'!');
                return redirect(url('/'));
            }
        }

    }

    // ----------------------------------------ANUSHTHAN POOJA PAYMENT METHOD WORKING ON 25/07/2024----------------------------------------------------
    public function anushthan_payment(Request $request){
         // dd($request->all());
        $wallet=User::select('wallet_balance')->where('id',$request->customer_id)->first();
        if($request->payment_amount > 0){   
            $user = Helpers::get_customer($request);
            $validator = Validator::make($request->all(), [
                'payment_method' => 'required',
                'payment_platform' => 'required',
            ]);
            $validator->sometimes('customer_id', 'required', function ($input) {
                return in_array($input->payment_request_from, ['app', 'react']);
            });
            
            $redirect_link = $this->anushthan_customer_payment_request($request);

            if(in_array($request->payment_request_from, ['app', 'react'])) {
                return response()->json(['redirect_link'=>$redirect_link], 200);
            }else{
                return redirect($redirect_link);
            }
        }else{
            $final_amount =$wallet['wallet_balance'] - $request->wallet_balance;
            // dd($final_amount,$wallet['wallet_balance'] ,$request->payment_amount);
            $additional_data = [
                'leads_id' => $request->leads_id,
                'package_id' => $request->package_id,
                'service_id' => $request->service_id,
                'customer_id' => $request->customer_id,
                'package_price' => $request->package_price,
                'booking_date' => $request->booking_date,
                'pandit_assign' => $request->pandit_assign,
            ];
            $orderId='';
            $orderData=Service_order::select('id')->latest()->first();
            if(!empty($orderData['id'])){
                $orderId='APJ'.(100000 + $orderData['id'] + 1);
            }else{
                $orderId='APJ'.(100001);
            }
              // Wallet Transection Details
            $serviceData=$additional_data;
            $wallet_transaction = new WalletTransaction();
            $wallet_transaction->user_id = $serviceData['customer_id'];
            $wallet_transaction->transaction_id = \Str::uuid();
            $wallet_transaction->reference = 'anushthan order payment';
            $wallet_transaction->transaction_type = 'anushthan_order_place';
            $wallet_transaction->balance = $final_amount;
            $wallet_transaction->debit = $request->wallet_balance;
            // dd($wallet_transaction);
            $wallet_transaction->save();
            User::where('id', $serviceData['customer_id'])->update(['wallet_balance' => $final_amount]);
            // Service Transection Details
            $serviceOrderAdd=new Service_order();
            $serviceOrderAdd->customer_id=$serviceData['customer_id'];
            $serviceOrderAdd->service_id=$serviceData['service_id'];
            $serviceOrderAdd->type='anushthan';
            $serviceOrderAdd->leads_id=$serviceData['leads_id'];
            $serviceOrderAdd->package_id=$serviceData['package_id'];
            $serviceOrderAdd->package_price=$serviceData['package_price'];
            $serviceOrderAdd->booking_date=$serviceData['booking_date'];
            $serviceOrderAdd->pandit_assign=$serviceData['pandit_assign'];
            $serviceOrderAdd->order_id = $orderId;
            if($serviceData['package_id'] == 7){
            $serviceOrderAdd->coupon_amount = session()->get('coupon_discount_vipanushthan');
            $serviceOrderAdd->coupon_code = session()->get('coupon_code_vipanushthan');
            }elseif($serviceData['package_id'] == 8){
            $serviceOrderAdd->coupon_amount = session()->get('coupon_discount_instanceanushthan');
            $serviceOrderAdd->coupon_code = session()->get('coupon_code_instanceanushthan');
            }
            $serviceOrderAdd->pay_amount= $request->final_amount;
            $serviceOrderAdd->wallet_amount= $request->wallet_balance;
            $serviceOrderAdd->wallet_translation_id = $wallet_transaction->transaction_id;
            $serviceOrderAdd->save();
            //  dd($serviceOrderAdd);
            Leads::where('id', $serviceData['leads_id'])->update([
                'status' => 0,
                'payment_status' => 'Complete',
                'order_id' => $orderId,
            ]);
            if (session()->has('payment_mode') && session('payment_mode') == 'app') {
                return response()->json(['message' => 'Payment succeeded'], 200);
            } else {
                Toastr::success(translate('Payment_success'));
                session()->forget('coupon_discount_vipanushthan');
                session()->forget('coupon_code_vipanushthan');
                session()->forget('coupon_discount_instanceanushthan');
                session()->forget('coupon_code_instanceanushthan');
                return redirect()->route('anushthan.user.detail',$orderId);
               
            }
        }

    }

    public function anushthan_customer_payment_request(Request $request){
        $additional_data = [
            'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
            'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
            'leads_id' => $request->leads_id,
            'package_id' => $request->package_id,
            'service_id' => $request->service_id,
            'customer_id' => $request->customer_id,
            'package_price' => $request->package_price,
            'booking_date' => $request->booking_date,
            'pandit_assign' => $request->pandit_assign,
            'wallet_balance' => $request->wallet_balance,
            'final_amount' => $request->final_amount,
        ];

        $user = Helpers::get_customer($request);
        if(in_array($request->payment_request_from, ['app', 'react'])){
            $additional_data['customer_id'] = $request->customer_id;
            $additional_data['is_guest'] = $request->is_guest;
            $additional_data['order_note'] = $request['order_note'];
            $additional_data['payment_request_from'] = $request->payment_request_from;
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

        if($customer == 'offline'){
            $address = ShippingAddress::where(['customer_id'=>$request->customer_id, 'is_guest'=>1])->latest()->first();
            if($address){
                $payer = new Payer(
                    $address->contact_person_name,
                    $address->email,
                    $address->phone,
                    ''
                );
            }else {
                $payer = new Payer(
                    'Contact person name',
                    '',
                    '',
                    ''
                );
            }
        }else{
            $payer = new Payer(
                $customer['f_name'] . ' ' . $customer['l_name'] ,
                $customer['email'],
                $customer['phone'],
                ''
            );
            if(empty($customer['phone'])){
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
            payer_id: $customer=='offline' ? $request->customer_id : $customer['id'],
            receiver_id: '100',
            additional_data: $additional_data, 
            payment_amount: $request->payment_amount,
            external_redirect_link: $request->payment_platform == 'web' ? $request->external_redirect_link : null,
            attribute: 'anushthan',
            attribute_id: idate("U")
        );

        $receiver_info = new Receiver('receiver_name','example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return $redirect_link;
    }

    public function anushthan_web_payment_success(Request $request){
        if ($request->flag == 'success') {
            $orderId='';
            $orderData=Service_order::select('id')->latest()->first();
            if(!empty($orderData['id'])){
                $orderId='APJ'.(100000 + $orderData['id'] + 1);
            }else{
                $orderId='APJ'.(100001);
            }
            $servicePaymentData=explode('transaction_reference=',base64_decode($request->token));
            $serviceOrder=PaymentRequest::where('transaction_id',$servicePaymentData['1'])->first();
            $additionalData = json_decode($serviceOrder['additional_data'], true); 
            // dd($serviceOrder);
            $serviceData=json_decode($serviceOrder->additional_data);
               // Wallet Maintan
            if ($serviceData->wallet_balance > 0) {
                $wallet_transaction = new WalletTransaction();
                $wallet_transaction->user_id = $serviceData->customer_id;
                $wallet_transaction->transaction_id = \Str::uuid();
                $wallet_transaction->reference = 'vip order payment';
                $wallet_transaction->transaction_type = 'vip_order_place';
                $wallet_transaction->balance = 0.00;
                $wallet_transaction->debit = $serviceData->wallet_balance;
                $wallet_transaction->save();
                User::where('id', $serviceData->customer_id)->update(['wallet_balance' => 0]);
            }
            // service_transaction
            $serviceOrderAdd=new Service_order();
            $serviceOrderAdd->customer_id=$serviceData->customer_id;
            $serviceOrderAdd->service_id=$serviceData->service_id;
            $serviceOrderAdd->type='anushthan';
            $serviceOrderAdd->leads_id=$serviceData->leads_id;
            $serviceOrderAdd->package_id=$serviceData->package_id;
            $serviceOrderAdd->package_price=$serviceData->package_price;
            $serviceOrderAdd->booking_date=$serviceData->booking_date;
            $serviceOrderAdd->pandit_assign=$serviceData->pandit_assign;
            $serviceOrderAdd->order_id = $orderId;
            if($serviceData->package_id == 7){
            $serviceOrderAdd->coupon_amount = session()->get('coupon_discount_vipanushthan');
            $serviceOrderAdd->coupon_code = session()->get('coupon_code_vipanushthan');
            }elseif($serviceData->package_id == 8){
            $serviceOrderAdd->coupon_amount = session()->get('coupon_discount_instanceanushthan');
            $serviceOrderAdd->coupon_code = session()->get('coupon_code_instanceanushthan');
            }
            $serviceOrderAdd->payment_id=$serviceOrder['transaction_id'];
            $serviceOrderAdd->wallet_amount= $serviceData->wallet_balance ?? 0;
            $serviceOrderAdd->transection_amount= $serviceOrder['payment_amount'];
            $serviceOrderAdd->wallet_translation_id = $wallet_transaction->transaction_id ?? null;
            $serviceOrderAdd->pay_amount=$additionalData['final_amount'];
            $serviceOrderAdd->save();
            //  dd($serviceOrderAdd);
            Leads::where('id', $serviceData->leads_id)->update([
                'status' => 0,
                'payment_status' => 'Complete',
                'order_id' => $orderId,
            ]);
            if (session()->has('payment_mode') && session('payment_mode') == 'app') {
                return response()->json(['message' => 'Payment succeeded'], 200);
            } else {
                Toastr::success(translate('Payment_success'));
                session()->forget('coupon_discount_vipanushthan');
                session()->forget('coupon_code_vipanushthan');
                session()->forget('coupon_discount_instanceanushthan');
                session()->forget('coupon_code_instanceanushthan');
                return redirect()->route('anushthan.user.detail',$orderId);
               
            }
        } else {
            if(session()->has('payment_mode') && session('payment_mode') == 'app'){
                return response()->json(['message' => 'Payment failed'], 403);
            }else{
                Toastr::error(translate('Payment_failed').'!');
                return redirect(url('/'));
            }
        }

    }
       // ----------------------------------------CHADHAVA PAYMENT METHOD WORKING ON 25/07/2024----------------------------------------------------
       public function chadhava_payment(Request $request){
        // dd($request->all());
        $wallet=User::select('wallet_balance')->where('id',$request->customer_id)->first();
        if($request->payment_amount > 0){   
        $user = Helpers::get_customer($request);
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
            'payment_platform' => 'required',
        ]);
        $validator->sometimes('customer_id', 'required', function ($input) {
            return in_array($input->payment_request_from, ['app', 'react']);
        });
        
        $redirect_link = $this->chadhava_customer_payment_request($request);

        if(in_array($request->payment_request_from, ['app', 'react'])) {
            return response()->json(['redirect_link'=>$redirect_link], 200);
        }else{
            return redirect($redirect_link);
        }
        } else {
        // dd($request->all());
        $final_amount =$wallet['wallet_balance'] - $request->wallet_balance;
        $additional_data = [
            'leads_id' => $request->leads_id,
            'service_id' => $request->service_id,
            'customer_id' => $request->customer_id,
            'booking_date' => $request->booking_date,
            'pandit_assign' => $request->pandit_assign,
        ];
        // dd($additional_data);
        $orderId='';
        $orderData=Chadhava_orders::select('id')->latest()->first();
        if(!empty($orderData['id'])){
            $orderId='CC'.(100000 + $orderData['id'] + 1);
        }else{
            $orderId='CC'.(100001);
        }
        $serviceData=$additional_data;
        $wallet_transaction = new WalletTransaction();
        $wallet_transaction->user_id = $serviceData['customer_id'];
        $wallet_transaction->transaction_id = \Str::uuid();
        $wallet_transaction->reference = 'pooja order payment';
        $wallet_transaction->transaction_type = 'pooja_order_place';
        $wallet_transaction->balance = $final_amount;
        $wallet_transaction->debit = $request->wallet_balance;
        // dd($wallet_transaction);
        $wallet_transaction->save();
        User::where('id', $serviceData['customer_id'])->update(['wallet_balance' => $final_amount]);

        $serviceOrderAdd=new Chadhava_orders();
        $serviceOrderAdd->customer_id= $serviceData['customer_id'];
        $serviceOrderAdd->service_id= $serviceData['service_id'];
        $serviceOrderAdd->type='chadhava';
        $serviceOrderAdd->leads_id=$serviceData['leads_id'];
        $serviceOrderAdd->booking_date=$serviceData['booking_date'];
        $serviceOrderAdd->pandit_assign=$serviceData['pandit_assign'];
        $serviceOrderAdd->order_id = $orderId;
        $serviceOrderAdd->pay_amount= $request->final_amount;
        $serviceOrderAdd->wallet_amount= $request->wallet_balance;
        $serviceOrderAdd->wallet_translation_id = $wallet_transaction->transaction_id;
        $serviceOrderAdd->save();
        //  dd($serviceOrderAdd);
        Leads::where('id', $serviceData['leads_id'])->update([
            'status' => 0,
            'payment_status' => 'Complete',
            'order_id' => $orderId,
        ]);
        if (session()->has('payment_mode') && session('payment_mode') == 'app') {
            return response()->json(['message' => 'Payment succeeded'], 200);
        } else {
            Toastr::success(translate('Payment_success'));
            return redirect()->route('chadhava.user.detail',$orderId);
           
        }

    }

    }


    public function chadhava_customer_payment_request(Request $request){
        $additional_data = [
            'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
            'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
            'leads_id' => $request->leads_id,
            'service_id' => $request->service_id,
            'customer_id' => $request->customer_id,
            'booking_date' => $request->booking_date,
            'pandit_assign' => $request->pandit_assign,
            'wallet_balance' => $request->wallet_balance,
            'final_amount' => $request->final_amount,
        ];

        $user = Helpers::get_customer($request);
        if(in_array($request->payment_request_from, ['app', 'react'])){
            $additional_data['customer_id'] = $request->customer_id;
            $additional_data['is_guest'] = $request->is_guest;
            $additional_data['order_note'] = $request['order_note'];
            $additional_data['payment_request_from'] = $request->payment_request_from;
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

        if($customer == 'offline'){
            $address = ShippingAddress::where(['customer_id'=>$request->customer_id, 'is_guest'=>1])->latest()->first();
            if($address){
                $payer = new Payer(
                    $address->contact_person_name,
                    $address->email,
                    $address->phone,
                    ''
                );
            }else {
                $payer = new Payer(
                    'Contact person name',
                    '',
                    '',
                    ''
                );
            }
        }else{
            $payer = new Payer(
                $customer['f_name'] . ' ' . $customer['l_name'] ,
                $customer['email'],
                $customer['phone'],
                ''
            );
            if(empty($customer['phone'])){
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
            payer_id: $customer=='offline' ? $request->customer_id : $customer['id'],
            receiver_id: '100',
            additional_data: $additional_data, 
            payment_amount: $request->payment_amount,
            external_redirect_link: $request->payment_platform == 'web' ? $request->external_redirect_link : null,
            attribute: 'chadhava',
            attribute_id: idate("U")
        );

        $receiver_info = new Receiver('receiver_name','example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return $redirect_link;
    }
    public function chadhava_web_payment_success(Request $request){
        if ($request->flag == 'success') {
            $orderId='';
            $orderData=Chadhava_orders::select('id')->latest()->first();
            if(!empty($orderData['id'])){
                $orderId='CC'.(100000 + $orderData['id'] + 1);
            }else{
                $orderId='CC'.(100001);
            }
            $servicePaymentData=explode('transaction_reference=',base64_decode($request->token));
            $serviceOrder=PaymentRequest::where('transaction_id',$servicePaymentData['1'])->first();
            $additionalData = json_decode($serviceOrder['additional_data'], true); 
            $serviceData=json_decode($serviceOrder->additional_data);
            // dd($additionalData);
            $serviceData=json_decode($serviceOrder->additional_data);
            if ($serviceData->wallet_balance > 0) {
                $wallet_transaction = new WalletTransaction();
                $wallet_transaction->user_id = $serviceData->customer_id;
                $wallet_transaction->transaction_id = \Str::uuid();
                $wallet_transaction->reference = 'chadhava order payment';
                $wallet_transaction->transaction_type = 'chadhava_order_place';
                $wallet_transaction->balance = 0.00;
                $wallet_transaction->debit = $serviceData->wallet_balance;
                $wallet_transaction->save();
                User::where('id', $serviceData->customer_id)->update(['wallet_balance' => 0]);
            }
            $serviceOrderAdd=new Chadhava_orders();
            $serviceOrderAdd->customer_id=$serviceData->customer_id;
            $serviceOrderAdd->service_id=$serviceData->service_id;
            $serviceOrderAdd->type='chadhava';
            $serviceOrderAdd->leads_id=$serviceData->leads_id;
            $serviceOrderAdd->booking_date=$serviceData->booking_date;
            $serviceOrderAdd->pandit_assign=$serviceData->pandit_assign;
            $serviceOrderAdd->order_id = $orderId;
           
            $serviceOrderAdd->payment_id=$serviceOrder['transaction_id'];
            $serviceOrderAdd->wallet_amount= $serviceData->wallet_balance ?? 0;
            $serviceOrderAdd->transection_amount= $serviceOrder['payment_amount'];
            $serviceOrderAdd->wallet_translation_id = $wallet_transaction->transaction_id ?? null;
            $serviceOrderAdd->pay_amount=$additionalData['final_amount'];
            $serviceOrderAdd->pay_amount=$serviceOrder['payment_amount'];
            $serviceOrderAdd->save();
            //  dd($serviceOrderAdd);
            Leads::where('id', $serviceData->leads_id)->update([
                'status' => 0,
                'payment_status' => 'Complete',
                'order_id' => $orderId,
            ]);
            if (session()->has('payment_mode') && session('payment_mode') == 'app') {
                return response()->json(['message' => 'Payment succeeded'], 200);
            } else {
                Toastr::success(translate('Payment_success'));
               
                return redirect()->route('chadhava.user.detail',$orderId);
               
            }
        } else {
            if(session()->has('payment_mode') && session('payment_mode') == 'app'){
                return response()->json(['message' => 'Payment failed'], 403);
            }else{
                Toastr::error(translate('Payment_failed').'!');
                return redirect(url('/'));
            }
        }

    }

    public function success_event(Request $request)
    {
        $flag = $request->get('flag');
        $token = $request->get('token');
        $decodedToken = base64_decode($token);
        parse_str($decodedToken, $transactionDetails);
        $paymentMethod = $transactionDetails['payment_method'] ?? null;
        $transactionReference = $transactionDetails['transaction_reference'] ?? '';

        $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));
        $payment = $api->payment->fetch($transactionReference);
        $getlist =  $this->payment::where(['transaction_id' => $transactionReference])->first();
        $getadditional = json_decode($getlist->additional_data);

        if (($payment->status === 'captured') && !empty($transactionReference)) {
            $array['payment_requests_id'] = $getlist->id;
            $array['transaction_id'] = $transactionReference;
            $array['status'] = 1;
            EventApproTransaction::where('event_id', $getlist->payer_id)->update($array);
            Events::where('id', $getlist->payer_id)->update(['approve_amount_status' => 1, 'is_approve' => 1]);
            $status = 1;
            $message = 'Event is approve Event booking open';
        } else {
            $array['payment_requests_id'] = $getlist->id;
            $array['transaction_id'] = $transactionReference;
            $array['status'] = 2;
            EventApproTransaction::where('event_id', $getlist->payer_id)->update($array);
            Events::where('id', $getlist->payer_id)->update(['approve_amount_status' => 3, 'is_approve' => 4]);
            $status = 2;
            $message = 'Amount Transaction Failed';
        }
        return view('payment.razor-pay-expires_day', compact('status', 'message'));
    }


     public function eventordersuccess(Request $request)
    {
        $flag = $request->get('flag');
        $token = $request->get('token');
        $decodedToken = base64_decode($token);
        parse_str($decodedToken, $transactionDetails);
        $paymentMethod = $transactionDetails['payment_method'] ?? null;
        $transactionReference = $transactionDetails['transaction_reference'] ?? '';

        $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));
        $payment = $api->payment->fetch($transactionReference);
        $getlist =  $this->payment::where(['transaction_id' => $transactionReference])->first();
        $getadditional = json_decode($getlist->additional_data);

        if (($payment->status === 'captured') && !empty($transactionReference)) {

            $getLead = EventLeads::where('id',  $getadditional->leads_id)->first();
            $EventId = Events::where('id',  $getadditional->event_id)->first();
            $listOrganizer =  EventOrganizer::where('id', $EventId['event_organizer_id'])->first();
            $bookingSeats = json_decode($EventId['all_venue_data'], true);
            $foundPackage = [];
            if ($bookingSeats) {
                $pn = 0;
                $amdin_commission = 0;
                $final_amount = 0;
                $govtTax = 0;
                foreach ($bookingSeats as $key=>$bo_se) {
                    $foundPackage['all_venue_data'][$key] = $bo_se;
                    if ((($bo_se['id'] ?? "") == $getLead['venue_id']) && !empty($bo_se['package_list'])) {
                        $package = collect($bo_se['package_list'])->firstWhere('package_name', $getLead['package_id']);
                        if (empty($package) && $package['available'] < $getLead['qty']) {
                            $array['transaction_id'] = $transactionReference;
                            $array['transaction_status'] = 3;
                            $refund['transaction_id'] = $transactionReference;
                            $refund['amount'] = $getLead['total_amount'];
                            $refund['event_id'] = $getadditional->order_id;
                            EventOrder::where('id', $getadditional->order_id)->update($array);
                           return $this->Event_Order_Refund($refund);
                            return response()->json(['status' => 0, 'message' => $getLead['qty'] . ' seats are not available. ' . $bo_se['available']  . ' seats are available.', 'recode' => '', 'data' => []], 400);
                        } else {
                            foreach ($bo_se['package_list'] as $keys => $val2) {
                                if($val2['package_name'] == $getLead['package_id']){
                                $foundPackage['all_venue_data'][$key]['package_list'][$keys]['available'] = ($val2['available'] - $getLead['qty']);
                                $foundPackage['all_venue_data'][$key]['package_list'][$keys]['sold'] = ($val2['sold'] + $getLead['qty']);
                                }
                            }

                            $array['transaction_id'] = $transactionReference;
                            $array['transaction_status'] = 1;
                            $eventtax = \App\Models\ServiceTax::find(1);
                            $orderamount = $getLead['total_amount'];
                            if (!empty($EventId) && $EventId['commission_seats']) {
                                $govtTax = (($orderamount * ($eventtax['event_tax'] ?? 0)) / 100);
                                $orderamount = $orderamount - $govtTax;
                                $amdin_commission =  (($orderamount * $EventId['commission_seats']) / 100);
                                $final_amount = $orderamount - $amdin_commission;
                            }
                            $array['admin_commission'] = $amdin_commission;
                            $array['gst_amount'] = $govtTax;
                            $array['final_amount'] = $final_amount;
                            EventOrder::where('id', $getadditional->order_id)->update($array);
                            \App\Models\EventLeads::where('id', $getadditional->leads_id)->update(['status' => 1]);
                        }
                    } 
                    
                }
                EventOrganizer::where('id', $EventId['event_organizer_id'])->update(
                    [
                        'org_total_tax' => ($listOrganizer['org_total_tax'] + $govtTax),
                        "org_withdrawable_ready" => ($listOrganizer["org_withdrawable_ready"] + $final_amount),
                        "org_total_commission" => ($listOrganizer["org_total_commission"] + $amdin_commission),
                    ]
                );
                Events::where('id',  $getadditional->event_id)->update($foundPackage);
            }
            return response()->json(['message' => 'Payment Success'], 200);
        }
        return response()->json(['message' => 'Payment failed'], 403);
    }

    static function BirthJournalSuccess(Request $request)
    {
        if (isset($request->wallet_type) && $request->wallet_type == 1) {
            $findData =  BirthJournalKundali::with('birthJournal')->find($request->insertedId);
            $kundaliPdf = "";
            if ($findData && $findData['birthJournal']['name'] == 'kundali') {
                $apiData = array(
                    'name' => $findData['name'],
                    'gender' => $findData['gender'],
                    'day' => date('d', strtotime($findData['bod'])),
                    'month' => date('m', strtotime($findData['bod'])),
                    'year' => date('Y', strtotime($findData['bod'])),
                    'hour' => date('h', strtotime($findData['time'])),
                    'min' => date('i', strtotime($findData['time'])),
                    'lat' => $findData['lat'],
                    'lon' => $findData['log'],
                    'language' => $findData['language'],
                    'tzone' => $findData['tzone'],
                    'place' => $findData['state'],
                    'chart_style' => $findData['chart_style'],
                    'footer_link' => 'mahakal.rizrv.in',
                    'logo_url' => 'https://mahakal.rizrv.in/storage/app/public/company/2024-03-21-65fbe1b6eee45.webp',
                    'company_name' => 'Manal Softech Pvt Ltd.',
                    'company_info' => 'Description of Manal Softech Pvt Ltd.',
                    'domain_url' => 'https://www.mahakal.rizrv.in/',
                    'company_email' => 'contact@mahakal.com',
                    'company_landline' => '+91- 221232 22',
                    'company_mobile' => '+91 1212 1212 12'
                );

                $language = in_array($findData['language'], ['hi', 'en']) ? $findData['language'] : 'hi';
                $kundali_Pdf = '';
                if (($findData['birthJournal']['type'] ?? "basic") == "basic") {
                    $kundali_Pdf = json_decode(ApiHelper::astroApi('https://pdf.astrologyapi.com/v1/basic_horoscope_pdf', $language, $apiData), true);
                } else if (($findData['birthJournal']['type'] ?? "basic") == "pro") {
                    $kundali_Pdf = json_decode(ApiHelper::astroApi('https://pdf.astrologyapi.com/v1/pro_horoscope_pdf', $language, $apiData), true);
                }
                if (!empty($kundali_Pdf['pdf_url'])) {
                    $fileName = $kundaliPdf = 'kundali_' . time() . '.pdf';
                    $filePath = storage_path('app/public/birthjournal/kundali/' . $fileName);

                    if (!file_exists(dirname($filePath))) {
                        mkdir(dirname($filePath), 0755, true);
                    }
                    $pdfContent = file_get_contents($kundali_Pdf['pdf_url']);
                    file_put_contents($filePath, $pdfContent);
                }
                $array['milan_verify'] = 1;
            }
            $array['transaction_id'] = 'wallet';
            $array['payment_status'] = 1;
            $array['kundali_pdf'] = $kundaliPdf;
            BirthJournalKundali::where('id', $request->insertedId)->update($array);
            if (isset($request->leads) && !empty($request->leads)) {
                KundaliLeads::where('id', $request->leads)->update(['payment_status' => 1, 'status' => 1]);
            }
            return response()->json(['message' => 'Payment Success'], 200);
        } else {
            $flag = $request->get('flag');
            $token = $request->get('token');
            $decodedToken = base64_decode($token);
            parse_str($decodedToken, $transactionDetails);
            $paymentMethod = $transactionDetails['payment_method'] ?? null;
            $transactionReference = $transactionDetails['transaction_reference'] ?? '';
            $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));
            $payment = $api->payment->fetch($transactionReference);
            $getlist = \App\Models\PaymentRequest::where(['transaction_id' => $transactionReference])->first();
            $getadditional = json_decode($getlist->additional_data);
            if (($payment->status === 'captured') && !empty($transactionReference)) {
                $findData =  BirthJournalKundali::with('birthJournal')->find($getlist->payer_id);
                $kundaliPdf = "";
                if ($findData && $findData['birthJournal']['name'] == 'kundali') {
                    $apiData = array(
                        'name' => $findData['name'],
                        'gender' => $findData['gender'],
                        'day' => date('d', strtotime($findData['bod'])),
                        'month' => date('m', strtotime($findData['bod'])),
                        'year' => date('Y', strtotime($findData['bod'])),
                        'hour' => date('h', strtotime($findData['time'])),
                        'min' => date('i', strtotime($findData['time'])),
                        'lat' => $findData['lat'],
                        'lon' => $findData['log'],
                        'language' => $findData['language'],
                        'tzone' => $findData['tzone'],
                        'place' => $findData['state'],
                        'chart_style' => $findData['chart_style'],
                        'footer_link' => 'mahakal.rizrv.in',
                        'logo_url' => 'https://mahakal.rizrv.in/storage/app/public/company/2024-03-21-65fbe1b6eee45.webp',
                        'company_name' => 'Manal Softech Pvt Ltd.',
                        'company_info' => 'Description of Manal Softech Pvt Ltd.',
                        'domain_url' => 'https://www.mahakal.rizrv.in/',
                        'company_email' => 'contact@mahakal.com',
                        'company_landline' => '+91- 221232 22',
                        'company_mobile' => '+91 1212 1212 12'
                    );

                    $language = in_array($findData['language'], ['hi', 'en']) ? $findData['language'] : 'hi';
                    $kundali_Pdf = '';
                    if (($findData['birthJournal']['type'] ?? "basic") == "basic") {
                        $kundali_Pdf = json_decode(ApiHelper::astroApi('https://pdf.astrologyapi.com/v1/basic_horoscope_pdf', $language, $apiData), true);
                    } else if (($findData['birthJournal']['type'] ?? "basic") == "pro") {
                        $kundali_Pdf = json_decode(ApiHelper::astroApi('https://pdf.astrologyapi.com/v1/pro_horoscope_pdf', $language, $apiData), true);
                    }
                    // dd($kundali_Pdf['pdf_url']);
                    if (!empty($kundali_Pdf['pdf_url'])) {
                        $fileName = $kundaliPdf = 'kundali_' . time() . '.pdf';
                        $filePath = storage_path('app/public/birthjournal/kundali/' . $fileName);

                        if (!file_exists(dirname($filePath))) {
                            mkdir(dirname($filePath), 0755, true);
                        }
                        $pdfContent = file_get_contents($kundali_Pdf['pdf_url']);
                        file_put_contents($filePath, $pdfContent);
                    }
                    $array['milan_verify'] = 1;
                }

                $array['transaction_id'] = $transactionReference;
                $array['payment_status'] = 1;
                $array['kundali_pdf'] = $kundaliPdf;
                BirthJournalKundali::where('id', $getadditional->order_id)->update($array);
                if (isset($getadditional->leads_id) && !empty($getadditional->leads_id)) {
                    KundaliLeads::where('id', $getadditional->leads_id)->update(['payment_status' => 1, 'status' => 1]);
                }
                return response()->json(['message' => 'Payment Success'], 200);
            }
        }
    }

    public function BirthJournalKundliSuccess(Request $request)
    {
        $data =   $this->BirthJournalSuccess($request);

        $token = $request->get('token');
        $decodedToken = base64_decode($token);
        parse_str($decodedToken, $transactionDetails);
        $transactionReference = $transactionDetails['transaction_reference'] ?? '';
        $getlist =  $this->payment::where(['transaction_id' => $transactionReference])->first();
        $findData =  BirthJournalKundali::with('birthJournal')->find($getlist->payer_id);
        if ($findData && $findData['birthJournal']['name'] == 'kundali') {
            $url = 'saved.paid.kundali';
        } else {
            $url = 'saved.paid.kundali.milan';
        }

        if ($data) {
            return redirect()->route($url);
        }
    }

    //donate
    public function donate_payment(Request $request)
    {
        $flag = $request->get('flag');
        $token = $request->get('token');
        $decodedToken = base64_decode($token);
        parse_str($decodedToken, $transactionDetails);
        $paymentMethod = $transactionDetails['payment_method'] ?? null;
        $transactionReference = $transactionDetails['transaction_reference'] ?? '';

        $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));
        $payment = $api->payment->fetch($transactionReference);
        $getlist =  $this->payment::where(['transaction_id' => $transactionReference])->first();
        $getadditional = json_decode($getlist->additional_data);
        $findData =  DonateAllTransaction::where('payment_requests_id', $getlist->id)->first();
        if (($payment->status === 'captured') && !empty($transactionReference)) {
            $array['transaction_id'] = $transactionReference;
            $array['amount_status'] = 1;
            DonateAllTransaction::where('id', $findData['id'])->update($array);
            $gettrust = DonateTrust::where('id', $findData['trust_id'])->first();
            if ($gettrust) {
                DonateTrust::where('id', $findData['trust_id'])->update(['trust_total_amount' => ($gettrust['trust_total_amount'] + $findData['final_amount']), 'admin_commission' => ($gettrust['admin_commission'] + $findData['admin_commission'])]);
            }
            $adsTrust = DonateAds::where('id', $findData['ads_id'])->first();
            if ($adsTrust) {
                DonateAds::where('id', $findData['ads_id'])->update(['total_amount_ads' => ($adsTrust['total_amount_ads'] + $findData['final_amount']), 'admin_commission_amount' => ($adsTrust['admin_commission_amount'] + $findData['admin_commission'])]);
            }

            if (isset($getadditional->leads_id) && !empty($getadditional->leads_id)) {
                DonateLeads::where('id', $getadditional->leads_id)->update(['status' => 1]);
            }
            if ($getlist->payment_platform == 'web') {
                return redirect()->route('donate-success', [$findData['id']]);
            } else {
                return response()->json(['message' => 'Payment Success'], 200);
            }
        }
    }


 public function success_adsApprove(Request $request)
    {
        $flag = $request->get('flag');
        $token = $request->get('token');
        $decodedToken = base64_decode($token);
        parse_str($decodedToken, $transactionDetails);
        $paymentMethod = $transactionDetails['payment_method'] ?? null;
        $transactionReference = $transactionDetails['transaction_reference'] ?? '';

        $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));
        $payment = $api->payment->fetch($transactionReference);
        $getlist =  $this->payment::where(['transaction_id' => $transactionReference])->first();
        $getadditional = json_decode($getlist->additional_data);
        // dd($getadditional->donate_all_transaction_id);
        if (($payment->status === 'captured') && !empty($transactionReference)) {
            $array['payment_requests_id'] = $getlist->id;
            $array['transaction_id'] = $transactionReference;
            $array['amount_status'] = 1;
            if ($getadditional->type == 'ad_approval') {
                DonateAds::where('id', $getlist->payer_id)->update(['is_approve' => 1]);
                DonateAllTransaction::where('id', $getadditional->donate_all_transaction_id)->update($array);
            }
            $status = 1;
            $message = 'Trust Ads approve and Ads Donate Active';
        } else {
            $array['payment_requests_id'] = $getlist->id;
            $array['transaction_id'] = $transactionReference;
            $array['status'] = 2;
            if ($getadditional->type == 'ad_approval') {
                DonateAds::where('id', $getlist->payer_id)->update(['is_approve' => 4]);
                DonateAllTransaction::where('id', $getadditional->donate_all_transaction_id)->update($array);
            }
            $status = 2;
            $message = 'Trust Ads Amount Transaction Failed';
        }
        return view('payment.razor-pay-expires_day', compact('status', 'message'));
    }

    public function Event_Order_Refund($refundDetails)
    {
        $apiKey = config('razor_config.api_key');
        $apiSecret = config('razor_config.api_secret');
        $transactionId = $refundDetails['transaction_id'];
        $amount = $refundDetails['amount'];
        $event_id = $refundDetails['event_id'];

        $refundUrl = "https://api.razorpay.com/v1/payments/{$transactionId}/refund";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $refundUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":" . $apiSecret);
        $refundData = json_encode(['amount' => $amount]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $refundData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode == 200) {
            $refund_id = json_decode($response);
            EventOrder::where('id', $event_id)->update(['refund_id' => $refund_id->id, 'status' => 3, 'transaction_status' => 1]);
            return response()->json(['message' => 'Refund processed successfully'], 200);
        } else {
            $error = json_decode($response);
            return response()->json(['message' => 'Refund failed: ' . $error->error->description], 200);
        }
    }
    public function Event_customer_payment_request(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $user = Helpers::get_customer($request);

            $event_booking = new EventOrder();
            $event_booking->event_id = $request->event_id;
            $event_booking->user_id = $request->user_id;
            $event_booking->venue_id = $request->venue_id;
            $event_booking->amount = $request->payment_amount;
            $event_booking->coupon_amount = $request->coupon_amount ?? 0;
            $event_booking->coupon_id = $request->coupon_id;
            $event_booking->transaction_status = 0;
            $event_booking->status = 1;
            $event_booking->save();

            $event_book_item = new EventOrderItems();
            $event_book_item->order_id = $event_booking->id;
            $event_book_item->package_id = $request->package_id;
            $event_book_item->no_of_seats = \App\Models\EventLeads::where('id', $request->leads_id)->first()['qty'] ?? 0;
            $event_book_item->amount = $request->payment_amount;
            $event_book_item->save();

            $additional_data = [
                'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
                'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
                'leads_id' => $request->leads_id,
                'package_id' => $request->package_id,
                'customer_id' => $request->customer_id,
                "order_id" => $event_booking->id,
                "event_id" => $request->event_id,
                "amount" => $request->payment_amount,
                "user_name" => $user['name'],
                "user_email" => $user['email'],
                "user_phone" => $user['phone'],
            ];

            // if (in_array($request->payment_request_from, ['app', 'react'])) {
            //     $additional_data['customer_id'] = $request->customer_id;
            //     $additional_data['is_guest'] = $request->is_guest;
            //     $additional_data['order_note'] = $request['order_note'];
            //     $additional_data['payment_request_from'] = $request->payment_request_from;
            //     $additional_data['payment_request_from'] = $request->payment_request_from;
            // }
            $currency_model = Helpers::get_business_settings('currency_model');
            if ($currency_model == 'multi_currency') {
                $currency_code = 'USD';
            } else {
                $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
                $currency_code = Currency::find($default)->code;
            }
            $customer = Helpers::get_customer($request);

            if ($customer == 'offline') {
                $address = ShippingAddress::where(['customer_id' => $request->customer_id, 'is_guest' => 1])->latest()->first();
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
                    DB::rollBack();
                    Toastr::error(translate('please_update_your_phone_number'));
                    return redirect()->route('event-booking', [$id]);
                }
            }

            $payment_info = new PaymentInfo(
                success_hook: 'digital_payment_success_custom',
                failure_hook: 'digital_payment_fail',
                currency_code: $currency_code,
                payment_method: $request->payment_method,
                payment_platform: $request->payment_platform,
                payer_id: $customer == 'offline' ? $request->customer_id : $customer['id'],
                receiver_id: '100',
                additional_data: $additional_data,
                payment_amount: $request->payment_amount,
                external_redirect_link: $request->payment_platform == 'web' ? $request->external_redirect_link : null,
                attribute: 'event_order',
                attribute_id: idate("U")
            );

            $receiver_info = new Receiver('receiver_name', 'example.png');

            DB::commit();
            $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);
            $parsed_url = parse_url($redirect_link);
            $query_string = $parsed_url['query'];
            parse_str($query_string, $query_params);
            EventOrder::where('id', $event_booking->id)->update(['payment_requests_id' => $query_params['payment_id']]);
            return $redirect_link;
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('An error occurred: ' . $e->getMessage());
            return redirect()->route('event-booking', [$id]);
        }
    }

    // Event Booking Order Payment Getway
    public function Eventpayment(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'payment_method' => 'required',
                'payment_platform' => 'required',
                'event_id' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (!Events::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                            $fail('The selected event is invalid or inactive.');
                        }
                    },
                ],
                'leads_id' => 'required',
            ]);
            $getLead = EventLeads::where('id', $request->get('leads_id'))->first();
            $EventId = Events::where('id', $request->input('event_id'))->first();
            if (empty($EventId) || empty($getLead) || ($getLead['package_id'] != $request->input('package_id'))) {
                Toastr::error('Invalid data passed.');
                return redirect()->route('event-booking', [$id]);
            }

            $bookingSeats = json_decode($EventId['all_venue_data'], true);
            $foundPackage = false;
            if ($bookingSeats) {
                foreach ($bookingSeats as $bo_se) {
                    if (($bo_se['id'] ?? "") == $getLead['venue_id'] && !empty($bo_se['package_list'])) {
                        foreach ($bo_se['package_list'] as $ch_seat) {
                            if ($ch_seat['package_name'] == $getLead['package_id']) {
                                $foundPackage = true;
                                if ($ch_seat['available'] < $getLead['qty']) {
                                    Toastr::error($getLead['qty'] . ' seats are not available. ' . $ch_seat['available'] . ' seats are available.');
                                    return redirect()->route('event-booking', [$id]);
                                }
                                break;
                            }
                        }
                    }
                }
            }

            if (!$foundPackage) {
                $PackagesSeats = json_decode($EventId['package_list'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Toastr::error('Booking seats data is not properly formatted.');
                    return redirect()->route('event-booking', [$id]);
                }
            }

            if ($request->wallet_type == 1) {
                $user = Helpers::get_customer($request);

                $event_booking = new EventOrder();
                $event_booking->event_id = $request->event_id;
                $event_booking->user_id = $request->user_id;
                $event_booking->venue_id = $request->venue_id;
                $event_booking->amount = $request->payment_amount;
                $event_booking->coupon_amount = $request->coupon_amount ?? 0;
                $event_booking->coupon_id = $request->coupon_id;
                $event_booking->transaction_status = 0;
                $event_booking->status = 1;
                $event_booking->save();

                $event_book_item = new EventOrderItems();
                $event_book_item->order_id = $event_booking->id;
                $event_book_item->package_id = $request->package_id;
                $event_book_item->no_of_seats = \App\Models\EventLeads::where('id', $request->leads_id)->first()['qty'] ?? 0;
                $event_book_item->amount = $request->payment_amount;
                $event_book_item->save();

                if ($user['wallet_balance'] >= $request['payment_amount']) {
                    // wallet dedication

                    $getLead = EventLeads::where('id',  $request->leads_id)->first();
                    if ($user['email'] != $getLead['user_phone']) {
                        $data_email = [
                            'subject' => translate("Event_booking_successfully"),
                            'email' => $user['email'],
                            'message' => "Booking Completed",
                        ];
                        $this->eventsRepository->sendMails($data_email);
                    }
                    $EventId = Events::where('id',  $request->event_id)->first();
                    $listOrganizer =  EventOrganizer::where('id', $EventId['event_organizer_id'])->first();
                    $array['transaction_id'] = 'wallet';
                    $array['transaction_status'] = 1;

                    $eventtax = \App\Models\ServiceTax::find(1);
                    $amdin_commission = 0;
                    $final_amount = 0;
                    $govtTax = 0;
                    $orderamount = $getLead['total_amount'];
                    if (!empty($EventId) && $EventId['commission_seats']) {
                        $govtTax = (($orderamount * ($eventtax['event_tax'] ?? 0)) / 100);
                        $orderamount = $orderamount - $govtTax;
                        $amdin_commission =  (($orderamount * $EventId['commission_seats']) / 100);
                        $final_amount = $orderamount - $amdin_commission;
                    }
                    $array['admin_commission'] = $amdin_commission;
                    $array['gst_amount'] = $govtTax;
                    $array['final_amount'] = $final_amount;

                    $bookingSeats = json_decode($EventId['all_venue_data'], true);
                    $foundPackage = [];
                    if ($bookingSeats) {
                        $pn = 0;
                        foreach ($bookingSeats as $keys => $bo_se) {
                            $foundPackage[$keys] = $bo_se;
                            if (($bo_se['id'] ?? "") == $getLead['venue_id'] && !empty($bo_se['package_list'])) {
                                foreach ($bo_se['package_list'] as $kp => $ch_seat) {
                                    if ($ch_seat['package_name'] == $getLead['package_id']) {
                                        if ($ch_seat['available'] < $getLead['qty']) {
                                            Toastr::error($getLead['qty'] . ' seats are not available. ' . $ch_seat['available'] . ' seats are available.');
                                            return redirect()->route('event-booking', [$id]);
                                        } else {
                                            $foundPackage[$keys]['package_list'][$kp]['available'] = ($ch_seat['available'] - $getLead['qty']);
                                            $foundPackage[$keys]['package_list'][$kp]['sold'] = ($ch_seat['sold'] + $getLead['qty']);

                                            $array['transaction_id'] = 'wallet';
                                            $array['transaction_status'] = 1;

                                            $eventtax = \App\Models\ServiceTax::find(1);
                                            $amdin_commission = 0;
                                            $final_amount = 0;
                                            $govtTax = 0;
                                            $orderamount = $getLead['total_amount'];

                                            if (!empty($EventId) && $EventId['commission_seats']) {
                                                $govtTax = (($orderamount * ($eventtax['event_tax'] ?? 0)) / 100);
                                                $orderamount = $orderamount - $govtTax;
                                                $amdin_commission =  (($orderamount * $EventId['commission_seats']) / 100);
                                                $final_amount = $orderamount - $amdin_commission;
                                            }
                                            $array['admin_commission'] = $amdin_commission;
                                            $array['gst_amount'] = $govtTax;
                                            $array['final_amount'] = $final_amount;
                                            EventOrder::where('id', $event_booking->id)->update($array);

                                            EventOrganizer::where('id', $EventId['event_organizer_id'])->update(
                                                [
                                                    'org_total_tax' => ($listOrganizer['org_total_tax'] + $govtTax),
                                                    "org_withdrawable_ready" => ($listOrganizer["org_withdrawable_ready"] + $final_amount),
                                                    "org_total_commission" => ($listOrganizer["org_total_commission"] + $amdin_commission),
                                                ]
                                            );
                                        }
                                    }
                                }
                                User::where('id', $user['id'])->update(['wallet_balance' => DB::raw('wallet_balance - ' . $request['payment_amount'])]);
                                \App\Models\EventLeads::where('id', $request->leads_id)->update(['status' => 1]);
                            }
                        }
                        Events::where('id',  $request->event_id)->update(['all_venue_data' => $foundPackage]);
                    }
                    $wallet_transaction = new WalletTransaction();
                    $wallet_transaction->user_id = $user['id'];;
                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                    $wallet_transaction->reference = 'Event order';
                    $wallet_transaction->transaction_type = 'event_order';
                    $wallet_transaction->balance = User::where('id', $user['id'])->first()['wallet_balance'];
                    $wallet_transaction->debit = $request->payment_amount;
                    $wallet_transaction->save();
                    DB::commit();
                    return redirect()->route('event-booking-success', [$id]);
                } else {
                    // wallet dedication
                    $wallet_amount = ($request['wallet_balance']);
                    $total_amount = $request['payment_amount'];
                    $onlinepay = ($request['payment_amount'] - $user['wallet_balance']);
                    $data = [
                        'additional_data' => [
                            'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
                            'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
                            'leads_id' => $request->leads_id,
                            'package_id' => $request->package_id,
                            'customer_id' => $request->customer_id,
                            "order_id" => $event_booking->id,
                            "event_id" => $request->event_id,
                            "amount" => $request->payment_amount,
                            "user_name" => $user['name'],
                            "user_email" => $user['email'],
                            "user_phone" => $user['phone'],
                            'total_amount' => $total_amount,
                            'wallet_amount' => $wallet_amount,
                            "online_pay" => $onlinepay,
                            'page_name' => 'event_order',
                            'success_url' => route('event-booking-success', [$id]),
                        ],
                        'user_id' => $user['id'],
                        'payment_method' => $request->payment_method,
                        'payment_platform' => $request->payment_platform,
                        'payment_amount' => $onlinepay,
                        'attribute' => "Event Order",
                        'external_redirect_link' => route('all-pay-wallet-payment-success', [$id]),
                    ];
                    $url_open = $this->Wallet_amount_add($data);
                    DB::commit();
                    return redirect($url_open);
                }
            } else {
                $redirect_link = $this->Event_customer_payment_request($request, $id);
                DB::commit();
                if (in_array($request->payment_request_from, ['app', 'react'])) {
                    return response()->json(['redirect_link' => $redirect_link], 200);
                } else {
                    return redirect($redirect_link);
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('An error occurred: ' . $e->getMessage());
            return redirect()->route('event-booking', [$id]);
        }
    }

     public function EventpaySuccess(Request $request, $id)
    {
        $data_id = json_decode(base64_decode($id));

        if ($request->flag == 'success') {
            $token = $request->get('token');
            $decodedToken = base64_decode($token);
            parse_str($decodedToken, $transactionDetails);
            $paymentMethod = $transactionDetails['payment_method'] ?? null;
            $transactionReference = ($transactionDetails['transaction_reference'] ?? '');
            $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));
            $payment = $api->payment->fetch($transactionReference);
            $getlist =  \App\Models\PaymentRequest::where(['transaction_id' => $transactionReference])->first();
            $getadditional = json_decode($getlist->additional_data);
            if (($payment->status === 'captured') && !empty($transactionReference)) {
                $getLead = EventLeads::where('id',  $data_id->lead)->first();
                $EventId = Events::where('id',  $data_id->event)->first();
                $listOrganizer =  EventOrganizer::where('id', $EventId['event_organizer_id'])->first();
                $bookingSeats = json_decode($EventId['all_venue_data'], true);
                $foundPackage = [];
                if ($bookingSeats) {
                    $pn = 0;
                    foreach ($bookingSeats as $keys=>$bo_se) {
                            $foundPackage[$keys] = $bo_se;
                            if (($bo_se['id'] ?? "") == $getLead['venue_id'] && !empty($bo_se['package_list'])) {
                                foreach($bo_se['package_list'] as $kp=>$ch_seat){
                                    if($ch_seat['package_name'] == $getLead['package_id']){
                                        if ($ch_seat['available'] < $getLead['qty']) {
                                            Toastr::error($getLead['qty'] . ' seats are not available. ' . $bo_se['available'] . ' seats are available.');
                                            $array['transaction_id'] = $transactionReference;
                                            $array['transaction_status'] = 3;
                                            $refund['transaction_id'] = $transactionReference;
                                            $refund['amount'] = $getLead['total_amount'];
                                            $refund['event_id'] = $getadditional->order_id;
                                            EventOrder::where('id', $getadditional->order_id)->update($array);
                                            $this->Event_Order_Refund($refund);
                                            return redirect()->route('event-booking', [$id]);
                                        }else{
                                            $foundPackage[$keys]['package_list'][$kp]['available'] = ($ch_seat['available'] - $getLead['qty']);
                                            $foundPackage[$keys]['package_list'][$kp]['sold'] = ($ch_seat['sold'] + $getLead['qty']);

                                            $array['transaction_id'] = $transactionReference;
                                            $array['transaction_status'] = 1;
            
                                            $eventtax = \App\Models\ServiceTax::find(1);
                                            $amdin_commission = 0;
                                            $final_amount = 0;
                                            $govtTax = 0;
                                            $orderamount = $getLead['total_amount'];
                                            if (!empty($EventId) && $EventId['commission_seats']) {
                                                $govtTax = (($orderamount * ($eventtax['event_tax'] ?? 0)) / 100);
                                                $orderamount = $orderamount - $govtTax;
                                                $amdin_commission =  (($orderamount * $EventId['commission_seats']) / 100);
                                                $final_amount = $orderamount - $amdin_commission;
                                            }
                                            $array['admin_commission'] = $amdin_commission;
                                            $array['gst_amount'] = $govtTax;
                                            $array['final_amount'] = $final_amount;
                                            EventOrder::where('id', $getadditional->order_id)->update($array);
            
                                            EventOrganizer::where('id', $EventId['event_organizer_id'])->update(
                                                [
                                                    'org_total_tax' => ($listOrganizer['org_total_tax'] + $govtTax),
                                                    "org_withdrawable_ready" => ($listOrganizer["org_withdrawable_ready"] + $final_amount),
                                                    "org_total_commission" => ($listOrganizer["org_total_commission"] + $amdin_commission),
                                                ]
                                            );
                                        }
                                    }
                                }
                            } 
                    }


                    Events::where('id',  $data_id->event)->update(['all_venue_data' => $foundPackage]);
                }
                \App\Models\EventLeads::where('id', $data_id->lead)->update(['status' => 1]);
                $userInfo = \App\Models\User::where('phone', ($getLead['user_phone'] ?? ""))->first();
                if ($userInfo['email'] != $getLead['user_phone']) {
                    $data_email = [
                        'subject' => translate("Event_Booking_Success"),
                        'email' => $userInfo['email'],
                        'message' => "Booking Complete",
                    ];
                    $this->eventsRepository->sendMails($data_email);
                }

                return redirect()->route('event-booking-success', [$id]);
            } else {
                $array['transaction_id'] = $transactionReference;
                $array['transaction_status'] = 2;
                EventOrder::where('id', $getadditional->order_id)->update($array);
            }
        } else {
            \App\Models\EventLeads::where('id', $data_id->lead)->update(['test' => 2]);
        }
    }

    public function TourBookingPay(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'payment_method' => 'required',
                'payment_platform' => 'required',
                'tour_id' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (!TourVisits::where('id', $value)->where('status', 1)->exists()) {
                            $fail('The selected Tour is invalid or inactive.');
                        }
                    },
                ],
                'leads_id' => 'required',
            ]);


            if ($request->use_date == 1) {
                $getseats =   \App\Models\TourOrder::where('tour_id', $request->tour_id)->where('amount_status', 1)->where('status', 1)->where('available_seat_cab_id', ($request->available_seat_cab_id ?? 0))->sum('qty');
                if (($request->totals_seat_cab_id - $getseats) < $request->qty) {
                    Toastr::error('Currently '.($request->totals_seat_cab_id - $getseats).' seats are available');
                    return redirect()->route('tour.tour-visit-id', [$id]);
                }
            }
            $user = Helpers::get_customer($request);
            $event_booking = new TourOrder();
            $event_booking->user_id = $request->user_id;
            $event_booking->tour_id = $request->tour_id;
            $event_booking->package_id = $request->package_id;
            $event_booking->coupon_amount = $request->coupon_amount ?? 0;
            $event_booking->coupon_id = $request->coupon_id ?? '';
            $event_booking->amount = $request->payment_amount;
            $event_booking->qty = $request->qty;
            $event_booking->available_seat_cab_id = $request->available_seat_cab_id ?? 0;
            $event_booking->total_seats_cab = $request->totals_seat_cab_id ?? 0;
            $event_booking->pickup_address = $request->pickup_address;
            $event_booking->pickup_date = $request->pickup_date;
            $event_booking->pickup_time = $request->pickup_time;
            $event_booking->pickup_lat = $request->pickup_lat;
            $event_booking->pickup_long = $request->pickup_long;
            $event_booking->payment_method = $request->payment_method;
            $event_booking->payment_platform = $request->payment_platform;
            $event_booking->leads_id = $request->leads_id;
            $event_booking->use_date = $request->use_date;
            $event_booking->part_payment = ((!empty($request->part_payment))?$request->part_payment:'full');

            $event_booking->traveller_id = $request->traveller_id;
            $event_booking->cab_assign = $request->traveller_id;
            $event_booking->booking_package = $request->bookings_packages;

            $event_booking->pickup_otp = mt_rand(1000, 9999);
            $event_booking->drop_opt = mt_rand(1000, 9999);
            $event_booking->amount_status = 0;
            $event_booking->status = 1;
            $event_booking->save();
            /////////////////////////////////////////// WALLET AND ONLINE /////////////////////////////////////////////////
            if ($request->wallet_type == 1) {
                if ($user['wallet_balance'] >= $request['payment_amount']) {
                    // wallet dedication
                    User::where('id', $user['id'])->update(['wallet_balance' => DB::raw('wallet_balance - ' . $request['payment_amount'])]);
                    \App\Models\TourLeads::where('id', $request->leads_id)->update(['amount_status' => 1]);
                    $getLead = TourLeads::where('id',  $request->leads_id)->first();
                    $tourData = TourVisits::where('id',  $request->tour_id)->first();
                    if ($user['email'] != $getLead['user_phone']) {
                        $data_email = [
                            'subject' => translate("Tour_booking_successfully"),
                            'email' => $user['email'],
                            'message' => "Booking Completed",
                        ];
                        $this->eventsRepository->sendMails($data_email);
                    }
                    $gst_amount = 0;
                    $admin_commission = 0;
                    $final_amount = $request['payment_amount'];
                    if ($tourData['tour_commission']) {
                        $admin_commission = (($final_amount * $tourData['tour_commission']) / 100);
                        $final_amount = ($final_amount - $admin_commission);
                    }
                    TourOrder::where('id', $event_booking->id)->update(['payment_method' => 'wallet', 'amount_status' => 1, 'admin_commission' => $admin_commission, 'gst_amount' => $gst_amount, 'final_amount' => $final_amount, 'transaction_id' => 'wallet']);
                    $wallet_transaction = new WalletTransaction();
                    $wallet_transaction->user_id = $user['id'];;
                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                    $wallet_transaction->reference = 'Tour order';
                    $wallet_transaction->transaction_type = 'tour_order';
                    $wallet_transaction->balance = User::where('id', $user['id'])->first()['wallet_balance'];
                    $wallet_transaction->debit = $request->payment_amount;
                    $wallet_transaction->save();
                    DB::commit();
                    return redirect()->route('tour.tour-booking-success', [$id]);
                } else {
                    // wallet dedication
                    $wallet_amount = ($request['wallet_balance']);
                    $total_amount = $request['payment_amount'];
                    $onlinepay = ($request['payment_amount'] - $user['wallet_balance']);
                    $data = [
                        'additional_data' => [
                            'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
                            'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
                            'leads_id' => $request->leads_id,
                            'package_id' => $request->package_id,
                            'customer_id' => $request->customer_id,
                            "order_id" => $event_booking->id,
                            "tour_id" => $request->tour_id,
                            "amount" => $request->payment_amount,
                            "user_name" => $user['name'],
                            "user_email" => $user['email'],
                            "user_phone" => $user['phone'],
                            'total_amount' => $total_amount,
                            'wallet_amount' => $wallet_amount,
                            "online_pay" => $onlinepay,
                            'page_name' => 'tour_order',
                            'success_url' => route('tour.tour-booking-success', [$id]),
                        ],
                        'user_id' => $user['id'],
                        'payment_method' => $request->payment_method,
                        'payment_platform' => $request->payment_platform,
                        'payment_amount' => $onlinepay,
                        'attribute' => "Tour Order",
                        'external_redirect_link' => route('all-pay-wallet-payment-success', [$id]),
                    ];

                    $url_open = $this->Wallet_amount_add($data);
                    DB::commit();
                    return redirect($url_open);
                }
                // dd($request['payment_amount']);
            } else {
                // dd($request['payment_amount'] - $user['wallet_balance']);

                $additional_data = [
                    'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
                    'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                    'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
                    'leads_id' => $request->leads_id,
                    'package_id' => $request->package_id,
                    'customer_id' => $request->customer_id,
                    "order_id" => $event_booking->id,
                    "tour_id" => $request->tour_id,
                    "amount" => $request->payment_amount,
                    "user_name" => $user['name'],
                    "user_email" => $user['email'],
                    "user_phone" => $user['phone'],
                ];
                $currency_model = Helpers::get_business_settings('currency_model');
                if ($currency_model == 'multi_currency') {
                    $currency_code = 'USD';
                } else {
                    $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
                    $currency_code = Currency::find($default)->code;
                }
                $customer = Helpers::get_customer($request);

                if ($customer == 'offline') {
                    $address = ShippingAddress::where(['customer_id' => $request->customer_id, 'is_guest' => 1])->latest()->first();
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
                        DB::rollBack();
                        Toastr::error(translate('please_update_your_phone_number'));
                        return redirect()->route('tour.tour-booking', [$id]);
                    }
                }

                $payment_info = new PaymentInfo(
                    success_hook: 'digital_payment_success_custom',
                    failure_hook: 'digital_payment_fail',
                    currency_code: $currency_code,
                    payment_method: $request->payment_method,
                    payment_platform: $request->payment_platform,
                    payer_id: $customer == 'offline' ? $request->customer_id : $customer['id'],
                    receiver_id: '100',
                    additional_data: $additional_data,
                    payment_amount: $request->payment_amount,
                    external_redirect_link: $request->payment_platform == 'web' ? $request->external_redirect_link : null,
                    attribute: 'event_order',
                    attribute_id: idate("U")
                );

                DB::commit();
                $receiver_info = new Receiver('receiver_name', 'example.png');
                $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);
                $parsed_url = parse_url($redirect_link);
                $query_string = $parsed_url['query'];
                parse_str($query_string, $query_params);
                return redirect($redirect_link);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('An error occurred: ' . $e->getMessage());
            return redirect()->route('tour.tour-booking', [$id]);
        }
    }

    public function TourSuccess(Request $request, $id)
    {
        $data_id = json_decode(base64_decode($id));

        if ($request->flag == 'success') {
            $token = $request->get('token');
            $decodedToken = base64_decode($token);
            parse_str($decodedToken, $transactionDetails);
            $paymentMethod = $transactionDetails['payment_method'] ?? null;
            $transactionReference = ($transactionDetails['transaction_reference'] ?? '');
            $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));
            $payment = $api->payment->fetch($transactionReference);
            $getlist =  \App\Models\PaymentRequest::where(['transaction_id' => $transactionReference])->first();
            $getadditional = json_decode($getlist->additional_data);
            if (($payment->status === 'captured') && !empty($transactionReference)) {
                $getLead = TourLeads::where('id',  $data_id->leads)->first();
                $tourData = TourVisits::where('id',  $data_id->id)->first();
                $tourOrder = TourOrder::where('id', ($getadditional->order_id ?? ''))->first();
                \App\Models\TourLeads::where('id', $data_id->leads)->update(['amount_status' => 1]);
                $userInfo = \App\Models\User::where('id', ($getLead['user_id'] ?? ""))->first();
                if ($userInfo['email'] != $getLead['user_phone']) {
                    $data_email = [
                        'subject' => translate("Tour_booking_successfully"),
                        'email' => $userInfo['email'],
                        'message' => "Booking Completed",
                    ];
                    $this->eventsRepository->sendMails($data_email);
                }
                
                $eventtax = \App\Models\ServiceTax::find(1);
                $gst_amount = 0;
                $admin_commission = 0;
                $final_amount = $tourOrder['amount'];
                if ($eventtax['tour_tax']) {
                    $gst_amount = (($final_amount * ($eventtax['event_tax'] ?? 0)) / 100);
                    $final_amount = $final_amount - $gst_amount;
                }
                if ($tourData['tour_commission']) {
                    $admin_commission = (($final_amount * $tourData['tour_commission']) / 100);
                    $final_amount = ($final_amount - $admin_commission);
                }
                
                TourOrder::where('id', ($getadditional->order_id ?? ''))->update(['amount_status' => 1, 'admin_commission' => $admin_commission, 'gst_amount' => $gst_amount, 'final_amount' => $final_amount, 'transaction_id' => $transactionReference]);

                if ($tourOrder['use_date'] == 1) {
                    $getseats =   \App\Models\TourOrder::where('tour_id', $tourOrder['tour_id'])->where('id', "!=",($getadditional->order_id ?? ''))->where('amount_status', 1)->where('status', 1)->where('available_seat_cab_id', ($tourOrder['available_seat_cab_id'] ?? 0))->sum('qty');
                    if (((int)$tourOrder['qty'] + (int)$getseats) > (int)$tourOrder['total_seats_cab']) {
                        Toastr::error('Currently '.($tourOrder['total_seats_cab'] - $getseats).' seats are available');
                        TourOrder::where('id', ($getadditional->order_id ?? ''))->update(['status' => 2,'refound_id'=>'wallet','refund_status'=>1,'refund_amount'=>$tourOrder['amount'],'refund_date'=>date('Y-m-d H:i:s')]);
                        User::where('id', $getadditional->customer_id)->update(['wallet_balance' => DB::raw('wallet_balance + ' . $tourOrder['amount'])]);
                        $wallet_transaction = new WalletTransaction();
                        $wallet_transaction->user_id = $getadditional->customer_id;
                        $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                        $wallet_transaction->reference = 'Tour refund';
                        $wallet_transaction->transaction_type = 'tour_refund';
                        $wallet_transaction->balance = ($userInfo['wallet_balance'] - $tourOrder['amount']);
                        $wallet_transaction->credit = $tourOrder['amount'];
                        $wallet_transaction->save();
                        return redirect()->route('tour.tour-visit-id', [$id]);
                    }
                }

                return redirect()->route('tour.tour-booking-success', [$id]);
            } else {
                $array['transaction_id'] = $transactionReference;
                $array['amount_status'] = 2;
                TourOrder::where('id', $getadditional->order_id)->update($array);
                return redirect()->route('tour.tour-booking-failed', [$id]);
            }
        } else {
            \App\Models\TourLeads::where('id', $data_id->leads)->update(['amount_status' => 1]);
            return redirect()->route('tour.tour-booking-failed', [$id]);
        }
    }

    public function TourBookingApi(Request $request)
    {
        $request->validate([
            'wallet_type' => 'required|in:0,1',
            'tour_id' =>  ['required', function ($attribute, $value, $fail) {
                if (!TourVisits::where('id', $value)->where('status', 1)->exists()) {
                    $fail('The selected tour is invalid or inactive.');
                }
            },],
            'leads_id' => 'required',
            'package_id' => 'required',
            'payment_amount' => 'required|numeric|min:1',
            'qty' => 'required|numeric|min:1',

            'pickup_address' => 'required',
            'pickup_date' => 'required',
            'pickup_time' => 'required',
            'pickup_lat' => 'required',
            'pickup_long' => 'required',
            'use_date' => 'required|in:0,1',

            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
        ], [
            'tour_id.required' => "tour is required!",
            'leads_id.required' => "lead Id is required!",
            'user_id.required' => "user Id is required!",
        ]);


        DB::beginTransaction();
        try {
            // dd($request['payment_amount']);
            $user = User::find($request->user_id);
            $event_booking = new TourOrder();
            $event_booking->user_id = $request->user_id;
            $event_booking->tour_id = $request->tour_id;
            $event_booking->package_id = $request->package_id;
            $event_booking->coupon_amount = $request->coupon_amount ?? 0;
            $event_booking->coupon_id = $request->coupon_id ?? '';
            $event_booking->amount = $request->payment_amount;
            $event_booking->qty = $request->qty;
            $event_booking->pickup_address = $request->pickup_address;
            $event_booking->pickup_date = $request->pickup_date;
            $event_booking->pickup_time = $request->pickup_time;
            $event_booking->pickup_lat = $request->pickup_lat;
            $event_booking->pickup_long = $request->pickup_long;
            $event_booking->payment_method = 'razor_pay';
            $event_booking->payment_platform = 'api';
            $event_booking->leads_id = $request->leads_id;
            $event_booking->use_date = $request->use_date;
            $event_booking->pickup_otp = mt_rand(1000, 9999);
            $event_booking->drop_opt = mt_rand(1000, 9999);
            $event_booking->amount_status = 0;
            $event_booking->status = 0;
            $event_booking->save();
            /////////////////////////////////////////// WALLET AND ONLINE /////////////////////////////////////////////////
            if ($request->wallet_type == 1) {
                if ($user['wallet_balance'] >= $request['payment_amount']) {
                    // wallet dedication
                    User::where('id', $user['id'])->update(['wallet_balance' => DB::raw('wallet_balance - ' . $request['payment_amount'])]);
                    \App\Models\TourLeads::where('id', $request->leads_id)->update(['amount_status' => 1]);
                    $getLead = TourLeads::where('id',  $request->leads_id)->first();
                    $tourData = TourVisits::where('id',  $request->tour_id)->first();
                    if ($user['email'] != $getLead['user_phone']) {
                        $data_email = [
                            'subject' => translate("Tour_booking_successfully"),
                            'email' => $user['email'],
                            'message' => "Booking Completed",
                        ];
                        $this->eventsRepository->sendMails($data_email);
                    }
                    $gst_amount = 0;
                    $admin_commission = 0;
                    $final_amount = $request['payment_amount'];
                    if ($tourData['tour_commission']) {
                        $admin_commission = (($final_amount * $tourData['tour_commission']) / 100);
                        $final_amount = ($final_amount - $admin_commission);
                    }
                    TourOrder::where('id', $event_booking->id)->update(['payment_method' => 'wallet', 'amount_status' => 1, 'admin_commission' => $admin_commission, 'gst_amount' => $gst_amount, 'final_amount' => $final_amount, 'transaction_id' => 'wallet']);
                    $wallet_transaction = new WalletTransaction();
                    $wallet_transaction->user_id = $user['id'];;
                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                    $wallet_transaction->reference = 'Tour order';
                    $wallet_transaction->transaction_type = 'tour_order';
                    $wallet_transaction->balance = User::where('id', $user['id'])->first()['wallet_balance'];
                    $wallet_transaction->debit = $request->payment_amount;
                    $wallet_transaction->save();
                    DB::commit();
                    return response()->json(['status' => 1, 'message' => "booking Successfully", 'data' => []], 200);
                } else {
                    // wallet dedication
                    return response()->json(['status' => 0, 'message' => 'please wallet Amount Check', 'data' => []], 200);
                }
            } else {
                $additional_data = [
                    'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
                    'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                    'payment_mode' => 'app',
                    'leads_id' => $request->leads_id,
                    'package_id' => $request->package_id,
                    'customer_id' => $request->user_id,
                    "order_id" => $event_booking->id,
                    "tour_id" => $request->tour_id,
                    "amount" => $request->payment_amount,
                    "user_name" => $user['name'],
                    "user_email" => $user['email'],
                    "user_phone" => $user['phone'],
                ];
                $currency_model = Helpers::get_business_settings('currency_model');
                if ($currency_model == 'multi_currency') {
                    $currency_code = 'USD';
                } else {
                    $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
                    $currency_code = Currency::find($default)->code;
                }
                $customer = Helpers::get_customer($request);
                if ($customer == 'offline') {
                    $address = ShippingAddress::where(['customer_id' => $request->user_id, 'is_guest' => 1])->latest()->first();
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
                        DB::rollBack();
                        response()->json(['status' => 0, 'message' => 'please update your phone number', 'data' => []], 200);
                    }
                }

                $payment_info = new PaymentInfo(
                    success_hook: 'digital_payment_success_custom',
                    failure_hook: 'digital_payment_fail',
                    currency_code: $currency_code,
                    payment_method: 'razor_pay',
                    payment_platform: 'app',
                    payer_id: $customer == 'offline' ? $request->user_id : $customer['id'],
                    receiver_id: '100',
                    additional_data: $additional_data,
                    payment_amount: $request->payment_amount,
                    external_redirect_link: url('api/v1/tour/tour-payamount-success'),
                    attribute: 'tour_order',
                    attribute_id: idate("U")
                );

                DB::commit();
                $receiver_info = new Receiver('receiver_name', 'example.png');
                $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);
                $parsed_url = parse_url($redirect_link);
                $query_string = $parsed_url['query'];
                parse_str($query_string, $query_params);
                return response()->json(['status' => 1, 'message' => 'pay Now', 'data' => ['url' => $redirect_link]], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 0, 'message' => 'An error occurred: ' . $e->getMessage(), 'data' => []], 200);
        }
    }

    public function TourBookingSuccess(Request $request)
    {
        if ($request->flag == 'success') {
            $token = $request->get('token');
            $decodedToken = base64_decode($token);
            parse_str($decodedToken, $transactionDetails);
            $paymentMethod = $transactionDetails['payment_method'] ?? null;
            $transactionReference = ($transactionDetails['transaction_reference'] ?? '');
            $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));
            $payment = $api->payment->fetch($transactionReference);
            $getlist =  \App\Models\PaymentRequest::where(['transaction_id' => $transactionReference])->first();
            $getadditional = json_decode($getlist->additional_data);
            if (($payment->status === 'captured') && !empty($transactionReference)) {
                $getLead = TourLeads::where('id',  $getadditional->leads_id)->first();
                $tourData = TourVisits::where('id',  $getadditional->tour_id)->first();
                $tourOrder = TourOrder::where('id', ($getadditional->order_id ?? ''))->first();

                \App\Models\TourLeads::where('id', $getadditional->leads_id)->update(['amount_status' => 1]);
                $userInfo = \App\Models\User::where('id', ($getLead['user_id'] ?? ""))->first();
                if ($userInfo['email'] != $getLead['user_phone']) {
                    $data_email = [
                        'subject' => translate("Tour_booking_successfully"),
                        'email' => $userInfo['email'],
                        'message' => "Booking Completed",
                    ];
                    $this->eventsRepository->sendMails($data_email);
                }
                $eventtax = \App\Models\ServiceTax::find(1);
                $gst_amount = 0;
                $admin_commission = 0;
                $final_amount = $tourOrder['amount'];
                if ($eventtax['tour_tax']) {
                    $gst_amount = (($final_amount * ($eventtax['tour_tax'] ?? 0)) / 100);
                    $final_amount = $final_amount - $gst_amount;
                }
                if ($tourData['tour_commission']) {
                    $admin_commission = (($final_amount * $tourData['tour_commission']) / 100);
                    $final_amount = ($final_amount - $admin_commission);
                }
                TourOrder::where('id', ($getadditional->order_id ?? ''))->update(['amount_status' => 1, 'admin_commission' => $admin_commission, 'gst_amount' => $gst_amount, 'final_amount' => $final_amount, 'transaction_id' => $transactionReference]);

                ///////////

                // $deviceToken = 'YOUR_DEVICE_TOKEN_HERE';
                // $title = 'Hello World';
                // $message = 'This is a sample notification.';        
                // sendFirebasePushNotification($deviceToken, $title, $message);

                ///////////
                return response()->json(['status' => 1, 'message' => 'Amount Transaction Successfully ', 'data' => []], 200);
            } else {
                $array['transaction_id'] = $transactionReference;
                $array['amount_status'] = 2;
                TourOrder::where('id', $getadditional->order_id)->update($array);
                return response()->json(['status' => 0, 'message' => 'Amount Transaction Failed ', 'data' => []], 200);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'Amount Transaction Failed ', 'data' => []], 200);
        }
    }

    static function Wallet_amount_add($all_info)
    {
        $additional_data = $all_info['additional_data'];
        $currency_model = Helpers::get_business_settings('currency_model');
        if ($currency_model == 'multi_currency') {
            $currency_code = 'USD';
        } else {
            $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
            $currency_code = Currency::find($default)->code;
        }
        $customer = Helpers::get_customer(['customer_id' => $all_info['user_id']]);

        if ($customer == 'offline') {
            $address = ShippingAddress::where(['customer_id' => $all_info['user_id'], 'is_guest' => 1])->latest()->first();
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
        }

        $payment_info = new PaymentInfo(
            success_hook: 'digital_payment_success_custom',
            failure_hook: 'digital_payment_fail',
            currency_code: $currency_code,
            payment_method: $all_info['payment_method'],
            payment_platform: $all_info['payment_platform'],
            payer_id: $customer == 'offline' ? $all_info['user_id'] : $customer['id'],
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: $all_info['payment_amount'],
            external_redirect_link: $all_info['payment_platform'] == 'web' ? $all_info['external_redirect_link'] : null,
            attribute: $all_info['attribute'],
            attribute_id: idate("U")
        );
        $receiver_info = new Receiver('receiver_name', 'example.png');
        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);
        $parsed_url = parse_url($redirect_link);
        $query_string = $parsed_url['query'];
        parse_str($query_string, $query_params);
        return ($redirect_link);
        // return redirect($redirect_link);
    }

    public function AllWalletSuccess(Request $request, $id)
    {
        if ($request->flag == 'success') {
            $token = $request->get('token');
            $decodedToken = base64_decode($token);
            parse_str($decodedToken, $transactionDetails);
            $paymentMethod = $transactionDetails['payment_method'] ?? null;
            $transactionReference = ($transactionDetails['transaction_reference'] ?? '');
            $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));
            $payment = $api->payment->fetch($transactionReference);
            $getlist =  \App\Models\PaymentRequest::where(['transaction_id' => $transactionReference])->first();
            $getadditional = json_decode($getlist->additional_data);
            if (($payment->status === 'captured') && !empty($transactionReference)) {
                User::where('id', $getadditional->customer_id)->update(['wallet_balance' => DB::raw('wallet_balance + ' . $getadditional->online_pay)]);
                $userInfo = \App\Models\User::where('id', ($getadditional->customer_id))->first();
                $wallet_transaction = new WalletTransaction();
                $wallet_transaction->user_id = $getadditional->customer_id;
                $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                $wallet_transaction->pay_transaction_id =  $transactionReference;
                $wallet_transaction->reference = 'add_funds_to_wallet';
                $wallet_transaction->transaction_type = 'add_fund';
                $wallet_transaction->balance = $userInfo['wallet_balance'];
                $wallet_transaction->credit = $getadditional->online_pay;
                $wallet_transaction->save();
                if ($getadditional->page_name == 'tour_order') {
                    $data_id = json_decode(base64_decode($id));
                    $getLead = TourLeads::where('id',  $data_id->leads)->first();
                    $tourData = TourVisits::where('id',  $data_id->id)->first();
                    $tourOrder = TourOrder::where('id', ($getadditional->order_id ?? ''))->first();
                    \App\Models\TourLeads::where('id', $data_id->leads)->update(['amount_status' => 1]);
                    if ($userInfo['email'] != $getLead['user_phone']) {
                        $data_email = [
                            'subject' => translate("Tour_booking_successfully"),
                            'email' => $userInfo['email'],
                            'message' => "Booking Completed",
                        ];
                        $this->eventsRepository->sendMails($data_email);
                    }
                    $eventtax = \App\Models\ServiceTax::find(1);
                    $gst_amount = 0;
                    $admin_commission = 0;
                    $final_amount = $getadditional->total_amount;
                    if ($eventtax['tour_tax']) {
                        $gst_amount = (($final_amount * ($eventtax['event_tax'] ?? 0)) / 100);
                        $final_amount = $final_amount - $gst_amount;
                    }
                    if ($tourData['tour_commission']) {
                        $admin_commission = (($final_amount * $tourData['tour_commission']) / 100);
                        $final_amount = ($final_amount - $admin_commission);
                    }
                    TourOrder::where('id', ($getadditional->order_id ?? ''))->update(['status' => 1, 'amount_status' => 1, 'admin_commission' => $admin_commission, 'gst_amount' => $gst_amount, 'final_amount' => $final_amount, 'transaction_id' => 'wallet']);
                    User::where('id', $getadditional->customer_id)->update(['wallet_balance' => DB::raw('wallet_balance - ' . $getadditional->total_amount)]);
                    $wallet_transaction = new WalletTransaction();
                    $wallet_transaction->user_id = $getadditional->customer_id;
                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                    $wallet_transaction->reference = 'Tour Order';
                    $wallet_transaction->transaction_type = 'tour_order';
                    $wallet_transaction->balance = ($userInfo['wallet_balance'] - $getadditional->total_amount);
                    $wallet_transaction->debit = $getadditional->total_amount;
                    $wallet_transaction->save();

                    if ($tourOrder['use_date'] == 1) {
                        $getseats =   \App\Models\TourOrder::where('tour_id', $tourOrder['tour_id'])->where('id', "!=",($getadditional->order_id ?? ''))->where('amount_status', 1)->where('status', 1)->where('available_seat_cab_id', ($tourOrder['available_seat_cab_id'] ?? 0))->sum('qty');
                        if (((int)$tourOrder['qty'] + (int)$getseats) > (int)$tourOrder['total_seats_cab']) {                        
                            Toastr::error('Currently '.($tourOrder['total_seats_cab'] - $getseats).' seats are available');
                            TourOrder::where('id', ($getadditional->order_id ?? ''))->update(['status' => 2,'refound_id'=>'wallet','refund_status'=>1,'refund_amount'=>$tourOrder['amount'],'refund_date'=>date('Y-m-d H:i:s')]);
                            User::where('id', $getadditional->customer_id)->update(['wallet_balance' => DB::raw('wallet_balance + ' . $tourOrder['amount'])]);
                            $wallet_transaction = new WalletTransaction();
                            $wallet_transaction->user_id = $getadditional->customer_id;
                            $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                            $wallet_transaction->reference = 'Tour refund';
                            $wallet_transaction->transaction_type = 'tour_refund';
                            $wallet_transaction->balance = ($userInfo['wallet_balance'] - $tourOrder['amount']);
                            $wallet_transaction->credit = $tourOrder['amount'];
                            $wallet_transaction->save();
                            return redirect()->route('tour.tour-visit-id', [$id]);
                        }
                    }

                    return redirect()->route('tour.tour-booking-success', [$id]);
                } elseif ($getadditional->page_name == 'event_order') {
                    $data_id = json_decode(base64_decode($id));
                    $getLead = EventLeads::where('id',  $data_id->lead)->first();
                    $EventId = Events::where('id',  $data_id->event)->first();
                    $listOrganizer =  EventOrganizer::where('id', $EventId['event_organizer_id'])->first();
                    $bookingSeats = json_decode($EventId['all_venue_data'], true);
                    $foundPackage = [];
                    if ($bookingSeats) {
                        $pn = 0;
                        foreach ($bookingSeats as $keys => $bo_se) {
                            $foundPackage[$keys] = $bo_se;
                            if (($bo_se['id'] ?? "") == $getLead['venue_id'] && !empty($bo_se['package_list'])) {
                                foreach ($bo_se['package_list'] as $kp => $ch_seat) {
                                    if ($ch_seat['package_name'] == $getLead['package_id']) {
                                        if ($ch_seat['available'] < $getLead['qty']) {
                                            Toastr::error($getLead['qty'] . ' seats are not available. ' . $ch_seat['available'] . ' seats are available.');
                                            $array['transaction_id'] = 'wallet';
                                            $array['transaction_status'] = 3;
                                            EventOrder::where('id', $getadditional->order_id)->update($array);
                                            return redirect()->route('event-booking', [$id]);
                                        } else {
                                            User::where('id', $getadditional->customer_id)->update(['wallet_balance' => DB::raw('wallet_balance - ' . $getadditional->total_amount)]);
                                            $foundPackage[$keys]['package_list'][$kp]['available'] = ($ch_seat['available'] - $getLead['qty']);
                                            $foundPackage[$keys]['package_list'][$kp]['sold'] = ($ch_seat['sold'] + $getLead['qty']);
                                            $array['transaction_id'] = 'wallet';
                                            $array['transaction_status'] = 1;
                                            $eventtax = \App\Models\ServiceTax::find(1);
                                            $amdin_commission = 0;
                                            $final_amount = 0;
                                            $govtTax = 0;
                                            $orderamount = $getLead['total_amount'];
                                            if (!empty($EventId) && $EventId['commission_seats']) {
                                                $govtTax = (($orderamount * ($eventtax['event_tax'] ?? 0)) / 100);
                                                $orderamount = $orderamount - $govtTax;
                                                $amdin_commission =  (($orderamount * $EventId['commission_seats']) / 100);
                                                $final_amount = $orderamount - $amdin_commission;
                                            }
                                            $array['admin_commission'] = $amdin_commission;
                                            $array['gst_amount'] = $govtTax;
                                            $array['final_amount'] = $final_amount;
                                            EventOrder::where('id', $getadditional->order_id)->update($array);
                                            EventOrganizer::where('id', $EventId['event_organizer_id'])->update(
                                                [
                                                    'org_total_tax' => ($listOrganizer['org_total_tax'] + $govtTax),
                                                    "org_withdrawable_ready" => ($listOrganizer["org_withdrawable_ready"] + $final_amount),
                                                    "org_total_commission" => ($listOrganizer["org_total_commission"] + $amdin_commission),
                                                ]
                                            );
                                        }
                                    }
                                }
                            }
                        }
                        Events::where('id',  $data_id->event)->update(['all_venue_data' => $foundPackage]);
                    }
                    \App\Models\EventLeads::where('id', $data_id->lead)->update(['status' => 1]);
                    $userInfo = \App\Models\User::where('phone', ($getLead['user_phone'] ?? ""))->first();
                    if ($userInfo['email'] != $getLead['user_phone']) {
                        $data_email = [
                            'subject' => translate("Event_Booking_Success"),
                            'email' => $userInfo['email'],
                            'message' => "Booking Complete",
                        ];
                        $this->eventsRepository->sendMails($data_email);
                    }
                    $wallet_transaction = new WalletTransaction();
                    $wallet_transaction->user_id = $getadditional->customer_id;
                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                    $wallet_transaction->reference = 'Event Order';
                    $wallet_transaction->transaction_type = 'event_order';
                    $wallet_transaction->balance = ($userInfo['wallet_balance'] - $getadditional->total_amount);
                    $wallet_transaction->debit = $getadditional->total_amount;
                    $wallet_transaction->save();

                    return redirect()->route('event-booking-success', [$id]);
                } elseif ($getadditional->page_name == "donate_order") {
                    User::where('id', $getadditional->customer_id)->update(['wallet_balance' => DB::raw('wallet_balance - ' . $getadditional->total_amount)]);
                    $findData =  DonateAllTransaction::where('id', $getadditional->transaction_id)->first();
                    $array['transaction_id'] = 'wallet';
                    $array['amount_status'] = 1;
                    DonateAllTransaction::where('id', $findData['id'])->update($array);
                    $gettrust = DonateTrust::where('id', $findData['trust_id'])->first();
                    if ($gettrust) {
                        DonateTrust::where('id', $findData['trust_id'])->update(['trust_total_amount' => ($gettrust['trust_total_amount'] + $findData['final_amount']), 'admin_commission' => ($gettrust['admin_commission'] + $findData['admin_commission'])]);
                    }
                    $adsTrust = DonateAds::where('id', $findData['ads_id'])->first();
                    if ($adsTrust) {
                        DonateAds::where('id', $findData['ads_id'])->update(['total_amount_ads' => ($adsTrust['total_amount_ads'] + $findData['final_amount']), 'admin_commission_amount' => ($adsTrust['admin_commission_amount'] + $findData['admin_commission'])]);
                    }

                    if (isset($getadditional->leads_id) && !empty($getadditional->leads_id)) {
                        DonateLeads::where('id', $getadditional->leads_id)->update(['status' => 1]);
                    }
                    $wallet_transaction = new WalletTransaction();
                    $wallet_transaction->user_id = $getadditional->customer_id;
                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                    $wallet_transaction->reference = 'Donate';
                    $wallet_transaction->transaction_type = 'donate';
                    $wallet_transaction->balance = ($userInfo['wallet_balance'] - $getadditional->total_amount);
                    $wallet_transaction->debit = $getadditional->total_amount;
                    $wallet_transaction->save();

                    return redirect()->route('donate-success', [$findData['id']]);
                } elseif ($getadditional->page_name == "kundli_order") {
                    User::where('id', $getadditional->customer_id)->update(['wallet_balance' => DB::raw('wallet_balance - ' . $getadditional->total_amount)]);
                    $request['wallet_type'] = 1;
                    $request['insertedId'] = $getadditional->order_id;
                    $request['leads'] = $getadditional->leads_id;
                    $data =   $this->BirthJournalSuccess($request);
                    $findData =  BirthJournalKundali::with('birthJournal')->find($getadditional->order_id);
                    if ($findData && $findData['birthJournal']['name'] == 'kundali') {
                        $url = 'saved.paid.kundali';
                    } else {
                        $url = 'saved.paid.kundali.milan';
                    }
                    $wallet_transaction = new WalletTransaction();
                    $wallet_transaction->user_id = $getadditional->customer_id;
                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                    $wallet_transaction->reference = 'kundli_order';
                    $wallet_transaction->transaction_type = 'kundli_order';
                    $wallet_transaction->balance = ($userInfo['wallet_balance'] - $getadditional->total_amount);
                    $wallet_transaction->debit = $getadditional->total_amount;
                    $wallet_transaction->save();
                    return redirect()->route($url);
                }
            } else {
                if ($getadditional->page_name == 'tour_order') {
                    $array['transaction_id'] = 'wallet';
                    $array['amount_status'] = 2;
                    TourOrder::where('id', $getadditional->order_id)->update($array);
                    return redirect()->route('tour.tour-booking-failed', [$id]);
                } elseif ($getadditional->page_name == 'event_order') {
                    $array['transaction_id'] = 'wallet';
                    $array['transaction_status'] = 2;
                    EventOrder::where('id', $getadditional->order_id)->update($array);
                    Toastr::error('Transaction Failed.');
                    return redirect()->route('event-booking', [$id]);
                } elseif ($getadditional->page_name == "kundli_order") {
                    Toastr::error('Transaction Failed.');
                    return url('/');
                }
            }
        } else {
            return back();
        }
    }

    public function addTourRemainingpay(Request $request,$id){ 
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'payment_method' => 'required',
                'payment_platform' => 'required',
            ]);
            $user = Helpers::get_customer($request);
            $tourDataOrder = TourOrder::where('id',$id)->where('status',1)->first();
            if(empty($tourDataOrder)){
                return back(); 
            }
            $tourData = TourVisits::where('id',  $tourDataOrder['tour_id'])->first();
            
            /////////////////////////////////////////// WALLET AND ONLINE /////////////////////////////////////////////////
            if ($request->wallet_type == 1) {
                if ($user['wallet_balance'] >= $request['payment_amount']) {
                    // wallet dedication
                    User::where('id', $user['id'])->update(['wallet_balance' => DB::raw('wallet_balance - ' . $request['payment_amount'])]);
                    if ($user['email'] != $user['phone']) {
                        $data_email = [
                            'subject' => translate("Tour_booking_successfully"),
                            'email' => $user['email'],
                            'message' => "remaining pay success",
                        ];
                        $this->eventsRepository->sendMails($data_email);
                    }
                    $eventtax = \App\Models\ServiceTax::find(1);
                    $gst_amount = 0;
                    $admin_commission = 0;
                    $final_amount = $request['payment_amount'];
                    if ($eventtax['tour_tax']) {
                        $gst_amount = (($final_amount * ($eventtax['tour_tax'] ?? 0)) / 100);
                        $final_amount = $final_amount - $gst_amount;
                    }
                    if ($tourData['tour_commission']) {
                        $admin_commission = (($final_amount * $tourData['tour_commission']) / 100);
                        $final_amount = ($final_amount - $admin_commission);
                    }
                    TourOrder::where('id', $id)->update(['part_payment'=>'full','admin_commission' => DB::raw('admin_commission + ' . $admin_commission), 'gst_amount' =>DB::raw('gst_amount + ' . $gst_amount), 'final_amount' => DB::raw('final_amount + ' . $final_amount),'amount'=>DB::raw('amount + ' . $request['payment_amount'])]);
                    $wallet_transaction = new WalletTransaction();
                    $wallet_transaction->user_id = $user['id'];
                    $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                    $wallet_transaction->reference = 'Tour remaining pay';
                    $wallet_transaction->transaction_type = 'tour_order_remaining_pay';
                    $wallet_transaction->balance = User::where('id', $user['id'])->first()['wallet_balance'];
                    $wallet_transaction->debit = $request->payment_amount;
                    $wallet_transaction->save();
                    DB::commit();
                    return redirect($request->external_redirect_link);
                } else {
                    // wallet dedication
                    $wallet_amount = ($user['wallet_balance']);
                    $total_amount = $request['payment_amount'];
                    $onlinepay = ($request['payment_amount'] - $user['wallet_balance']);
                    $data = [
                        'additional_data' => [
                            'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
                            'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
                            'customer_id' => $request->customer_id,
                            "order_id" => $id,
                            "tour_id" => $tourDataOrder['tour_id'],
                            "amount" => $request->payment_amount,
                            "user_name" => $user['name'],
                            "user_email" => $user['email'],
                            "user_phone" => $user['phone'],
                            'total_amount' => $total_amount,
                            'wallet_amount' => $wallet_amount,
                            "online_pay" => $onlinepay,
                            'page_name' => 'tour_order_wallet',
                            'success_url' => $request->external_redirect_link,
                        ],
                        'user_id' => $user['id'],
                        'payment_method' => $request->payment_method,
                        'payment_platform' => $request->payment_platform,
                        'payment_amount' => $onlinepay,
                        'attribute' => "Tour Order",
                        'external_redirect_link' => route('tour.tour-remaining-payment-success', [$id]),
                    ];

                    $url_open = $this->Wallet_amount_add($data);
                    DB::commit();
                    return redirect($url_open);
                }
                // dd($request['payment_amount']);
            } else {
                // dd($request['payment_amount'] - $user['wallet_balance']);

                $additional_data = [
                    'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
                    'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                    'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
                    'customer_id' => $request->customer_id,
                    "order_id" => $id,
                    "tour_id" => $tourDataOrder['tour_id'],
                    "amount" => $request->payment_amount,
                    "user_name" => $user['name'],
                    "user_email" => $user['email'],
                    "user_phone" => $user['phone'],
                    'page_name' => 'tour_order_online',
                ];
                $currency_model = Helpers::get_business_settings('currency_model');
                if ($currency_model == 'multi_currency') {
                    $currency_code = 'USD';
                } else {
                    $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
                    $currency_code = Currency::find($default)->code;
                }
                $customer = Helpers::get_customer($request);

                if ($customer == 'offline') {
                    $address = ShippingAddress::where(['customer_id' => $request->customer_id, 'is_guest' => 1])->latest()->first();
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
                        DB::rollBack();
                        Toastr::error(translate('please_update_your_phone_number'));
                        return redirect($request->external_redirect_link);
                    }
                }

                $payment_info = new PaymentInfo(
                    success_hook: 'digital_payment_success_custom',
                    failure_hook: 'digital_payment_fail',
                    currency_code: $currency_code,
                    payment_method: $request->payment_method,
                    payment_platform: $request->payment_platform,
                    payer_id: $customer == 'offline' ? $request->customer_id : $customer['id'],
                    receiver_id: '100',
                    additional_data: $additional_data,
                    payment_amount: $request->payment_amount,
                    external_redirect_link: route('tour.tour-remaining-payment-success', [$id]),
                    attribute: 'tour_order',
                    attribute_id: idate("U")
                );
                DB::commit();
                $receiver_info = new Receiver('receiver_name', 'example.png');
                $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);
                $parsed_url = parse_url($redirect_link);
                $query_string = $parsed_url['query'];
                parse_str($query_string, $query_params);
                return redirect($redirect_link);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('An error occurred: ' . $e->getMessage());
            return redirect($request->external_redirect_link);
        }
    }

    public function TourRemainingpaysuccess(Request $request,$id){
        if ($request->flag == 'success') {
            $token = $request->get('token');
            $decodedToken = base64_decode($token);
            parse_str($decodedToken, $transactionDetails);
            $paymentMethod = $transactionDetails['payment_method'] ?? null;
            $transactionReference = ($transactionDetails['transaction_reference'] ?? '');
            $api = new Api(config('razor_config.api_key'), config('razor_config.api_secret'));
            $payment = $api->payment->fetch($transactionReference);
            $getlist =  \App\Models\PaymentRequest::where(['transaction_id' => $transactionReference])->first();
            $getadditional = json_decode($getlist->additional_data);
            if (($payment->status === 'captured') && !empty($transactionReference)) {
                $tourOrder = TourOrder::where('id', ($id ?? ''))->first();               
                $tourData = TourVisits::where('id',  $tourOrder['tour_id'])->first();

                $userInfo = \App\Models\User::where('id', ($tourOrder['user_id'] ?? ""))->first();
                if ($userInfo['email'] != $userInfo['phone']) {
                    $data_email = [
                        'subject' => translate("Tour_booking_successfully"),
                        'email' => $userInfo['email'],
                        'message' => "remaining pay success",
                    ];
                    $this->eventsRepository->sendMails($data_email);
                }
                
                $eventtax = \App\Models\ServiceTax::find(1);
                $gst_amount = 0;
                $admin_commission = 0;
                $final_amount = $tourOrder['amount'];
                if ($eventtax['tour_tax']) {
                    $gst_amount = (($final_amount * ($eventtax['tour_tax'] ?? 0)) / 100);
                    $final_amount = $final_amount - $gst_amount;
                }
                if ($tourData['tour_commission']) {
                    $admin_commission = (($final_amount * $tourData['tour_commission']) / 100);
                    $final_amount = ($final_amount - $admin_commission);
                }
                TourOrder::where('id', $id)->update(['part_payment'=>'full','admin_commission' => DB::raw('admin_commission + ' . $admin_commission), 'gst_amount' =>DB::raw('gst_amount + ' . $gst_amount), 'final_amount' => DB::raw('final_amount + ' . $final_amount),'amount'=>DB::raw('amount + ' . $tourOrder['amount'])]);
                if ($getadditional->page_name == 'tour_order_wallet') {
                        User::where('id', $getadditional->customer_id)->update(['wallet_balance' => DB::raw('wallet_balance - ' . ($getadditional->total_amount - $getadditional->online_pay))]);
                        $wallet_transaction = new WalletTransaction();
                        $wallet_transaction->user_id = $getadditional->customer_id;
                        $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                        $wallet_transaction->reference = 'add_funds_to_wallet';
                        $wallet_transaction->transaction_type = 'add_fund';
                        $wallet_transaction->balance = ($userInfo['wallet_balance'] + $getadditional->online_pay);
                        $wallet_transaction->credit = $getadditional->online_pay;
                        $wallet_transaction->save();

                        $wallet_transaction = new WalletTransaction();
                        $wallet_transaction->user_id = $getadditional->customer_id;
                        $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                        $wallet_transaction->reference = 'Tour remaining pay';
                        $wallet_transaction->transaction_type = 'tour_order_remaining_pay';
                        $wallet_transaction->balance = ($userInfo['wallet_balance'] - $getadditional->total_amount);
                        $wallet_transaction->debit = $getadditional->total_amount;
                        $wallet_transaction->save();
                        return redirect()->route('tour.view-details', [$id]);
                    }
                }
                return redirect()->route('tour.view-details', [$id]);
            } else {
                return redirect()->route('tour.view-details', [$id]);
            }
    }

}