<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderStatusEvent;
use App\Http\Controllers\Controller;
use App\Library\Payer;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Models\Package;
use App\Models\Prashad_deliverys;
use App\Models\Service;
use App\Models\Service_order;
use App\Models\ShippingAddress;
use App\Models\User;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Library\Payment as PaymentInfo;
use App\Library\Receiver;
use App\Models\Leads;
use App\Models\PaymentRequest;
use App\Traits\Payment;

use function App\Utils\getNextPoojaDay;

class ServiceBookingController extends Controller
{
    public function type(){
        return view('admin-views.service-booking.type');
    }

    public function pooja(Request $request){
        $type = $request->type;
        $services = [];
        if($type=="online-pooja"){
            $services = Service::where('product_type','pooja')->where('pooja_type',0)->where('status',1)->get();
        }
        return view('admin-views.service-booking.service',compact('type','services'));
    }

    public function package(Request $request){
        if($request->type=="online-pooja"){
            $service = Service::where('id',$request->id)->where('status',1)->first();
            if($service){
                return view('admin-views.service-booking.package',compact('service'));
            }
            Toastr::error(translate('service not found'));
            return back();
        }
        Toastr::error(translate('an error occured'));
        return back();
    }

    public function user_check(Request $request){
        $user = User::where('phone',$request->phone)->first();
        if($user){
            return response()->json(['status'=>true,'user'=>$user],200);
        } else{
            return response()->json(['status'=>false,'message'=>'user not found'],200);
        }
        return response()->json(['status'=>false,'message'=>'an error occured'],400);
    }

    public function user_register(Request $request){
        $nameParts = explode(' ', $request->name);
        $userStore = new User;
        $userStore->name = $request->name;
        $userStore->f_name = $nameParts[0];
        $userStore->l_name = $nameParts[1]??null;
        $userStore->phone = $request->phone;
        $userStore->email = 'user@mahakal.com';
        $userStore->password = Hash::make('12345678');
        $userStore->is_active = 1;
        if($userStore->save()){
            return response()->json(['status'=>true,'user'=>$userStore],200);
        }
        return response()->json(['status'=>false,'message'=>'unable to store user'],400);
    }

    public function sankalp(Request $request){
        $service = Service::where('id',$request->service_id)->first();
        $userId = $request->user_id;
        $packageId = $request->package_id;
        $persons = Package::where('id',$packageId)->value('person');
        $price = $request->price;
        return view('admin-views.service-booking.sankalp',compact('service','userId','packageId','persons','price'));
    }

    public function order_place(Request $request){
        $service = Service::where('id',$request->service_id)->first();
        $weekDay = json_decode($service->week_days);
        $time = date('H:i:s', strtotime($service->pooja_time));
        $nextPoojaDay = getNextPoojaDay($weekDay, $time);
        $bookingDate = $nextPoojaDay->format('Y-m-d H:i:s');
        $orderData = Service_order::select('id')->latest()->first();
        if (!empty($orderData['id'])) {
            $orderId = 'PJ' . (100000 + $orderData['id'] + 1);
        } else {
            $orderId = 'PJ' . (100001);
        }

        //lead store
        $packageData = Package::where('id',$request->package_id)->first();
        $userData = User::where('id',$request->user_id)->first();
        $leadsData = Leads::latest()->first();
        if (!empty($leadsData)) {
            $leadno = 'PJ' . (100 + $leadsData->id + 1);
        } else {
            $leadno = 'PJ' . (101);
        }
        $leadStore = new Leads;
        $leadStore->service_id = $request->service_id;
        $leadStore->leadno = $leadno;
        $leadStore->order_id = $orderId;
        $leadStore->type = 'pooja';
        $leadStore->package_id = $request->package_id;
        $leadStore->package_name = $packageData->title;
        $leadStore->noperson = $packageData->person;
        $leadStore->package_price = $request->price;
        $leadStore->person_name = $userData->f_name;
        $leadStore->person_phone = $userData->phone;
        $leadStore->booking_date = $bookingDate;
        $leadStore->status = 1;
        $leadStore->save();

        //order store
        $orderPlace = new Service_order;
        $orderPlace->customer_id = $request->user_id;
        $orderPlace->service_id = $request->service_id;
        $orderPlace->type = 'pooja';
        $orderPlace->leads_id = $leadStore->id??0;
        $orderPlace->package_id = $request->package_id;
        $orderPlace->package_price = $request->price;
        $orderPlace->pay_amount = 0;
        $orderPlace->order_id = $orderId;
        $orderPlace->booking_date = $bookingDate;
        if($request->sankalp == 'yes'){
            $orderPlace->members = json_encode($request->member);
            $orderPlace->gotra = $request->gotra;
            $orderPlace->is_prashad = $request->prashad;
            $orderPlace->house_no = $request->house_no;
            $orderPlace->area = $request->area;
            $orderPlace->city = $request->city;
            $orderPlace->state = $request->state;
            $orderPlace->landmark = $request->landmark;
            $orderPlace->pincode = $request->pincode;
            $orderPlace->latitude = $request->latitude;
            $orderPlace->longitude = $request->longitude;
        }
        if($orderPlace->save()){
            if($request->prashad == 1){
                $prashadStore = new Prashad_deliverys;
                $prashadStore->seller_id = '14';
                $prashadStore->order_id = $orderId;
                $prashadStore->warehouse_id = '61202';
                $prashadStore->service_id = $request->service_id;
                $prashadStore->user_id =   $request->user_id;
                $prashadStore->product_id = '853';
                $prashadStore->type = 'pooja';
                $prashadStore->payment_type = 'P';
                $prashadStore->booking_date = $bookingDate;
                $prashadStore->save();
            }

            $redirect_link = $this->services_customer_payment_request($request,$orderId);
            $linkid = explode('=',$redirect_link)['1'];
            PaymentRequest::where('id',$linkid)->update(['previous_url'=>url('service/order/book/report/failure')]);

            // whatsapp message
            $message_data = [
                'service_name' => $service->name,
                'type' => 'text-with-media',
                'attachment' =>  asset('/storage/app/public/pooja/thumbnail/' . $service->thumbnail),
                'booking_date' => date('d-m-Y', strtotime($bookingDate)),
                'puja_venue' => $service->pooja_venue,
                'orderId' => $orderId,
                'final_amount' => webCurrencyConverter((float)($request->price)),
                'customer_id' => $request->user_id,
                'payment_link' => $redirect_link
            ];
            Helpers::whatsappMessage('whatsapp', 'Service Payment', $message_data);
            Toastr::success(translate('order_placed_successfully'));
            return redirect()->route('admin.book.type');
        }
        Toastr::error(translate('an_error_occured'));
        return back();
    }

    public function services_customer_payment_request(Request $request,$orderId)
    {

        $additional_data = [
            'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
            'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
            'payment_mode' => $request->has('payment_platform') ? $request->payment_platform : 'web',
            'package_id' => $request->package_id,
            'service_id' => $request->service_id,
            'customer_id' => $request->user_id,
            'package_price' => $request->price,
            'order_id' => $orderId,
        ];

        $user = Helpers::get_customer($request);
        if (in_array($request->payment_request_from, ['app', 'react'])) {
            $additional_data['customer_id'] = $request->user_id;
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
        
        $customer = User::where('id',$request->user_id)->first();

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

        $payment_info = new PaymentInfo(
            success_hook: 'digital_payment_success_custom',
            failure_hook: 'digital_payment_fail',
            currency_code: $currency_code,
            payment_method: 'razor_pay',
            payment_platform: 'web',
            payer_id: $customer == 'offline' ? $request->user_id : $customer['id'],
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: $request->price,
            external_redirect_link: url('admin/book/pooja-payment-success'),
            attribute: 'puja',
            attribute_id: idate("U")
        );
        // dd($payment_info);

        $receiver_info = new Receiver('receiver_name', 'example.png');
        // dd($payer, $payment_info, $receiver_info);
        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return $redirect_link;
    }
    
    public function pooja_payment_success(Request $request)
    {
        $servicePaymentData = explode('transaction_reference=', base64_decode($request->token));
        $serviceOrder = PaymentRequest::where('transaction_id', $servicePaymentData['1'])->first();
        $additionalData = json_decode($serviceOrder['additional_data'], true);

        $serviceData = json_decode($serviceOrder->additional_data);
        $order = Service_order::where('order_id', $serviceData->order_id)->where('status', '0')->with(['customer'])->first();
        if ($request->flag == 'success') {
            // service_transaction
            $serviceOrderAdd = Service_order::where('order_id',$serviceData->order_id)->first();
            $serviceOrderAdd->transection_amount = $serviceData->package_price;
            $serviceOrderAdd->pay_amount = $serviceData->package_price;
            $serviceOrderAdd->payment_id = $serviceOrder['transaction_id'];
            $serviceOrderAdd->save();
            $order = Service_order::where('order_id', $serviceData->order_id)->where('status', '0')->with(['customer'])->first();
            event(new OrderStatusEvent(key: '0', type: 'puja', order: $order));

            if (session()->has('payment_mode') && session('payment_mode') == 'app') {
                return response()->json(['message' => 'Payment succeeded'], 200);
            } else {
                Toastr::success(translate('Payment_success'));
                // whatsapp
                $service_name = Service::where('id', $serviceData->service_id)->first();
                $message_data = [
                    'service_name' => $service_name['name'],
                    'type' => 'text-with-media',
                    'attachment' =>  asset('/storage/app/public/pooja/thumbnail/' . $service_name['thumbnail']),
                    'booking_date' => date('d-m-Y', strtotime($order->booking_date)),
                    'puja_venue' => $service_name['pooja_venue'],
                    'orderId' => $serviceData->order_id,
                    'final_amount' => webCurrencyConverter((float)$serviceData->package_price),
                    'customer_id' => $serviceData->customer_id
                ];

                Helpers::whatsappMessage('whatsapp', 'Pooja Confirmed', $message_data);
                return redirect()->route('service.order.book.report', [$request->flag]);
            }
        } else {
            $user = User::where('id',$serviceOrder->payer_id)->first();
            $message_data = [
                'order_amount' => $serviceOrder->payment_amount,
                'customer_id' => $user->id,
                'booking_date' => date('d M Y', strtotime($order->created_at)),
                'transaction_id' => $serviceOrder->id,
                'puja' => $order->type,
                'link' => $serviceOrder->previous_url ?? 'mahakal.com',
                'user_name' => $user['f_name'] . ' ' . $user['l_name'],
                'number' => $user->phone,
            ];
            Helpers::whatsappMessage('whatsapp', 'Payment Fail', $message_data);
            Toastr::error(translate('Payment_failed') . '!');
            return redirect()->route('service.order.book.report', [$request->flag]);
        }
    }
}
