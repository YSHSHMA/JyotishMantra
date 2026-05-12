<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\DarshanOrder;
use App\Models\DarshanOrderMembers;
use App\Models\PaymentRequest;
use App\Models\TempleDarshanLead;
use App\Models\User;
use App\Models\UserAadhaarKyc;
use App\Models\WalletTransaction;
use App\Utils\Helpers;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Milon\Barcode\DNS1D;
use App\Models\Temple;
use App\Models\TempleServicePrice;
use App\Models\Purohit;
use App\Models\templePackages;
use App\Models\TempleServiceSlot;

class TempleDarshan extends Controller
{

    public function CreateVIPPass(Request $request)
    {
        $getData = DarshanOrderMembers::where('barcode', base64_decode(urldecode($request['barcode'])))->with(['darshanOrder'])->first();
        if ($getData) {
            $barcodes =  DNS1D::getBarcodePNG(base64_decode(urldecode($request['barcode'])), 'C128', '3', '80');
            $mpdf_view = View::make('web-views.darshan.booking.entrypass', compact('getData', 'barcodes'));
            Helpers::gen_mpdf($mpdf_view, 'darshan_', str_replace(' ', '_', $getData['name']));
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(["status" => 1, "message" => "Entry Pass generated successfully."]);
            } else {
                return back();
            }
        } else {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 0, 'message' => 'Not Found Barcode', 'recode' => 0, 'data' => []], 200);
            } else {
                return back();
            }
        }
    }

    public function DarshanOrderInvoice(Request $request)
    {
        $getData = DarshanOrder::where('id', $request['id'])->with(['userData', 'Temple'])->first();
        if ($getData) {
            $mpdf_view = View::make('web-views.darshan.booking.invoice', compact('getData'));
            Helpers::gen_mpdf($mpdf_view, 'darshan_invoice_', str_replace(' ', '_', $getData['order_id']));
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(["status" => 1, "message" => "Invoice generated successfully."]);
            } else {
                return back();
            }
        } else {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 0, 'message' => 'Not Found Barcode', 'recode' => 0, 'data' => []], 200);
            } else {
                return back();
            }
        }
    }


    public function LeadAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'temple_id' => 'required',
            'user_id' => 'required',
            'price' => 'required',
            'package_name' => 'required',
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $leads = new TempleDarshanLead();
        $user_info = User::where('id', $request['user_id'])->first();
        if ($user_info) {
            $leads->user_id = $user_info['id'];
            $leads->name = $user_info['f_name'] . " " . $user_info['l_name'];
            $leads->phone = $user_info['phone'];
            $leads->temple_id = $request['temple_id'];
            $leads->package_id = $request['id'];
            $leads->title = $request['name'];
            $leads->package_name = $request['package_name'];
            $leads->price = $request['price'];
            $leads->status = 0;
            $leads->save();
            return response()->json(['status' => 1, "message" => "lead Create", 'data' => ["lead_id" => $leads->id]], 200);
        } else {
            return response()->json(['status' => 0, "message" => "user Id Invalid", 'data' => []], 200);
        }
    }

    public function DarshanBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "lead_id" => ['required', function ($attribute, $value, $fail) {
                if (!TempleDarshanLead::where('id', $value)->where('status', 0)->exists()) {
                    $fail('The selected lead Id is invalid.');
                }
            },]
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $getTemple = \App\Models\TempleDarshanLead::where('id', $request->lead_id)->with(['Temple'])->first();
        $BookingInfo = [];
        if ($getTemple) {
            $BookingInfo['include'] = [];
            $vipPlans = json_decode($getTemple['Temple']['vip_plans'] ?? '[]', true);
            $vipDarshan = [];
            if (!empty($vipPlans)) {
                foreach ($vipPlans as $plan) {
                    if ($plan['id'] == ($getTemple['package_id'] ?? 0)) {
                        $vipDarshan = $plan['package'][0] ?? [];
                        break;
                    }
                }
            }
            $BookingInfo['en_temple_name'] = $getTemple['Temple']['name'] ?? "";
            $translate = $getTemple['Temple'] ? $getTemple['Temple']->translations()->pluck('value', 'key')->toArray() : [];
            $BookingInfo['hi_temple_name'] = $translate['name'] ?? "";
            $BookingInfo['image'] = getValidImage(path: 'storage/app/public/temple/thumbnail/' . $getTemple['Temple']['thumbnail'] ?? '', type: 'backend-product');

            $BookingInfo['name'] = $vipDarshan['name'] ?? '';
            $BookingInfo['price'] = $vipDarshan['price'] ?? 0;
            if (!empty($vipDarshan['include']) && count($vipDarshan['include']) > 0) {
                foreach ($vipDarshan['include'] as $in_val) {
                    $BookingInfo['include'][] = $in_val['name'];
                }
            }
            $BookingInfo['time_slot'] = [];
            if (!empty($vipDarshan['include']) && count($vipDarshan['include']) > 0) {
                foreach ($vipDarshan['date'] as $in_date) {
                    $BookingInfo['time_slot'][] =     $in_date['time'];
                }
            }
            $BookingInfo['aadhaar_verify_status']  = ($getTemple['Temple']['aadhaar_verify_status'] ?? 0);
            return response()->json(['status' => 1, "message" => "get Data Successfully", 'data' => $BookingInfo], 200);
        } else {
            return response()->json(['status' => 0, "message" => "user Id Invalid", 'data' => []], 200);
        }
    }

    public function LeadUpdates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "lead_id" => ['required', function ($attribute, $value, $fail) {
                if (!TempleDarshanLead::where('id', $value)->where('status', 0)->exists()) {
                    $fail('The selected lead Id is invalid.');
                }
            },],
            'date' => 'required|date_format:d-m-Y',
            'time' => 'required',
            'price' => 'required',
            'qty' => 'required',
            "user_information" => "required"
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $userInformationRaw = $request->input('user_information', '[]');
        // $userInformationRaw = str_replace("'", '"', $userInformationRaw);
        $info = json_decode($userInformationRaw, true);
        // if (json_last_error() !== JSON_ERROR_NONE) {
        //     return response()->json(['status' => 0, 'error' => 'Invalid user_information format', 'data' => []], 200);
        // }
        $hasError = false;
        $user_Infor = [];
        foreach ($info as $key => $user) {
            if (empty($user['name']) || empty($info[0]['phone']) || empty($user['aadhar'])) {
                $hasError = true;
                break;
            } else {
                $user_Infor[] = ["fullName" => $user['name'], "phone" => $user['phone'], "aadhar" => $user['aadhar'], 'verify' => $user['verify'] ?? 0];
            }
        }
        if ($hasError) {
            return response()->json(['status' => 0, 'message' => 'Each user must have name, phone, and aadhar', 'data' => []], 200);
        }
        if (count($info) > $request['qty'] || count($info) < $request['qty']) {
            return response()->json(['status' => 0, 'message' => 'Person Information and qty Invalid', 'data' => []], 200);
        }
        $getTemple = TempleDarshanLead::where('id', $request->lead_id)->with(['Temple', 'userData'])->first();
        if ($getTemple) {
            $vipPlans = json_decode($getTemple['Temple']['vip_plans'] ?? '[]', true);
            $vipDarshan = [];
            if (!empty($vipPlans)) {
                foreach ($vipPlans as $plan) {
                    if ($plan['id'] == ($getTemple['package_id'] ?? 0)) {
                        $vipDarshan = $plan['package'][0] ?? [];
                        break;
                    }
                }
            }

            $getTemple->people_qty = $request['qty'];
            $getTemple->price = (($vipDarshan['price'] ?? 0) * $request['qty'] ?? 1);
            $getTemple->date = $request['date'];
            $getTemple->time = $request['time'];
            $getTemple->people_info = json_encode($user_Infor);
            $getTemple->save();
            $userDetails = User::where('id', $getTemple['user_id'])->first();
            $currency_model = Helpers::get_business_settings('currency_model');
            if ($currency_model == 'multi_currency') {
                $currency_code = 'USD';
            } else {
                $default = BusinessSetting::where(['type' => 'system_default_currency'])->first()->value;
                $currency_code = \App\Models\Currency::find($default)->code;
            }
            $final_amount = ($getTemple->price ?? 0);
            $darshantax = \App\Models\ServiceTax::find(1);
            // $getTemple['Temple']['admin_commission'] = 5;
            $vip_admin_commission = (optional(optional($getTemple->Temple)->Trust)->vip_darshan_commission) ?? 0;
            if ($darshantax['vip_darshan_tax']) {
                $gst_amount = (($final_amount * ($darshantax['vip_darshan_tax'] ?? 0)) / 100);
                $final_amount = $final_amount - $gst_amount;
            }
            if (($vip_admin_commission ?? 0)) {
                $admin_commission = (($final_amount * $vip_admin_commission) / 100);
                $final_amount = ($final_amount - $admin_commission);
            }
            $check_paymants = PaymentRequest::where('attribute', 'vip_darshan_order')->where('attribute_id', $request['lead_id'])->first();
            $order_id = 0;
            $member_getid = [];
            if ($check_paymants) {
                $Paymants = PaymentRequest::find($check_paymants['id']);
                $order_id = json_decode($Paymants->additional_data, true)['order_id'] ?? 0;
                $member_getid = json_decode($Paymants->additional_data, true)['member_id'] ?? 0;
                if ($member_getid) {
                    DarshanOrderMembers::whereIn('id', $member_getid)->delete();
                }
            } else {
                $Paymants = new PaymentRequest();
            }
            if ($order_id) {
                $darshan_booking = DarshanOrder::find($order_id);
            } else {
                $darshan_booking = new DarshanOrder();
            }
            $darshan_booking->user_id = $getTemple['user_id'];
            $darshan_booking->temple_id = $getTemple['temple_id'];
            $darshan_booking->package_id = $getTemple['package_id'];
            $darshan_booking->title = $getTemple['title'] ?? '';
            $darshan_booking->package_name = $getTemple['package_name'] ?? '';
            $darshan_booking->date = $getTemple['date'];
            $darshan_booking->time = $getTemple['time'];
            $darshan_booking->price = $getTemple['price'] ?? 0;
            $darshan_booking->people_qty = $getTemple['people_qty'] ?? 0;
            $darshan_booking->admin_commission = $admin_commission ?? 0;
            $darshan_booking->gst_amount = $gst_amount ?? 0;
            $darshan_booking->final_amount = $final_amount ?? 0;
            $darshan_booking->status = 0;
            $darshan_booking->save();
            $member_id = [];
            for ($iq = 0; $iq < ($getTemple['people_qty'] ?? 0); $iq++) {
                $darshan_memberbook = new DarshanOrderMembers();
                $darshan_memberbook->darshan_id = $darshan_booking->id;
                $darshan_memberbook->name = $user_Infor[$iq]['fullName'] ?? '';
                $darshan_memberbook->phone = $user_Infor[$iq]['phone'] ?? '';
                $darshan_memberbook->aadhar = $user_Infor[$iq]['aadhar'] ?? '';
                $darshan_memberbook->aadhar_verify_status = $user_Infor[$iq]['verify'] ?? 0;
                $darshan_memberbook->save();
                $member_id[] = $darshan_memberbook->id;
            }
            $additional_data = [
                'business_name' => BusinessSetting::where(['type' => 'company_name'])->first()->value,
                'business_logo' => asset('storage/app/public/company') . '/' . Helpers::get_business_settings('company_web_logo'),
                'payment_mode' => 'web',
                'leads_id' => $request->lead_id,
                'package_id' => $getTemple['package_id'],
                'customer_id' => $getTemple['user_id'],
                "order_id" => $darshan_booking->id,
                'member_id' => $member_id,
                "temple_id" => $getTemple['temple_id'],
                "amount" => ($getTemple['price'] ?? 0),
                "user_name" => ($getTemple['userData']['f_name'] ?? '') . " " . ($getTemple['userData']['l_name'] ?? ''),
                "user_email" => $getTemple['userData']['email'],
                "user_phone" => $getTemple['userData']['phone'],
            ];
            $Paymants->payer_id =  $getTemple['user_id'];
            $Paymants->receiver_id =  "100";
            $Paymants->payment_amount =  $getTemple->price;
            $Paymants->success_hook = "digital_payment_success_custom";
            $Paymants->failure_hook = "digital_payment_fail";
            $Paymants->currency_code = $currency_code;
            $Paymants->payment_method = "razor_pay";
            $Paymants->additional_data = json_encode($additional_data);
            $Paymants->is_paid = 0;
            $Paymants->payer_information = json_encode(["name" => $userDetails['name'] ?? "", "email" => $userDetails['email'], "phone" => $userDetails['phone'], "address" => ""]);
            $Paymants->external_redirect_link = route('vip-darshan-booking-pay-received');
            $Paymants->attribute_id = $request['lead_id'];
            $Paymants->attribute = 'vip_darshan_order';
            $Paymants->payment_platform = 'app';
            $Paymants->save();
            return response()->json(['status' => 1, "message" => "Update Lead Successfully", 'data' => $Paymants->id], 200);
        } else {
            return response()->json(['status' => 0, "message" => "user Id Invalid", 'data' => []], 200);
        }
    }

    public function BookingSuccess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "payment_request_id" => ['required', function ($attribute, $value, $fail) {
                if (!PaymentRequest::where('id', $value)->where('is_paid', 0)->exists()) {
                    $fail('The selected paymant Request Id is invalid.');
                }
            },],
            'payment_method' => "required",
            "transaction_id" => "required",
            "wallet_type" => "required|in:0,1",
            'online_pay' => 'required_unless:transaction_id,wallet',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $Paymants = PaymentRequest::where('id', $request['payment_request_id'])->first();
        $order_id = json_decode($Paymants->additional_data ?? '[]', true)['order_id'] ?? 0;
        $lead_id = json_decode($Paymants->additional_data ?? '[]', true)['leads_id'] ?? 0;

        if ($request->wallet_type == 1 && ($request['online_pay'] ?? 0) > 0) {
            User::where('id', $Paymants->payer_id)->update(['wallet_balance' => DB::raw('wallet_balance + ' . $request['online_pay'])]);
            $wallet_transaction = new \App\Models\WalletTransaction();
            $wallet_transaction->user_id = $Paymants->payer_id;
            $wallet_transaction->transaction_id = (($request->transaction_id) ? $request->transaction_id : \Illuminate\Support\Str::uuid());
            $wallet_transaction->reference = 'add_funds_to_wallet';
            $wallet_transaction->transaction_type = 'add_fund';
            $wallet_transaction->balance = User::where('id', $Paymants->payer_id)->first()['wallet_balance'];
            $wallet_transaction->credit = $request['online_pay'];
            $wallet_transaction->save();
        }
        $userDetails = User::where('id', $Paymants->payer_id)->first();
        if ($request->wallet_type == 1 && ($Paymants['payment_amount'] >= $userDetails['wallet_balance'])) {
            return response()->json(['status' => 0, "message" => "Invalid Amount Pass", 'data' => []], 200);
        }
        if ($order_id && $lead_id) {
            if ($request->wallet_type == 1 && ($Paymants['payment_amount'] <= $userDetails['wallet_balance'])) {
                User::where('id', $userDetails['id'])->update(['wallet_balance' => DB::raw('wallet_balance - ' . ($Paymants['payment_amount'] ?? 0))]);
                $wallet_transaction = new WalletTransaction();
                $wallet_transaction->user_id = $userDetails['id'];
                $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
                $wallet_transaction->reference = 'vip Darshan order';
                $wallet_transaction->transaction_type = 'vip_darshan_order';
                $wallet_transaction->balance = User::where('id', $userDetails['id'])->first()['wallet_balance'];
                $wallet_transaction->debit = ($Paymants->payment_amount ?? 0);
                $wallet_transaction->save();
            }
            $darshanLead = TempleDarshanLead::find($lead_id);
            $darshanLead->status = 1;
            $darshanLead->save();

            $DarshanOrder = DarshanOrder::where('id', $order_id)->with(['Temple', 'Members'])->first();
            $templeId = $DarshanOrder['temple_id'];
            $purohits = \App\Models\Purohit::where('temple_id', $templeId)->pluck('id')->toArray();
            if (count($purohits) > 0) {
                $lastOrder = DarshanOrder::where('temple_id', $templeId)->orderBy('id', 'desc')->first();
                if ($lastOrder) {
                    $lastIndex = array_search($lastOrder->purohit_id, $purohits);
                    $nextIndex = ($lastIndex === false) ? 0 : ($lastIndex + 1) % count($purohits);
                    $purohitId = $purohits[$nextIndex];
                } else {
                    $purohitId = $purohits[0];
                }
                $DarshanOrder->purohit_id = $purohitId;
            }
            $DarshanOrder->payment_method = $request['payment_method'];
            $DarshanOrder->transaction_id = $request['transaction_id'];
            $DarshanOrder->status = 1;
            $DarshanOrder->save();

            $PaymantsNew = PaymentRequest::find($request['payment_request_id']);
            $PaymantsNew->transaction_id = $request['transaction_id'];
            $PaymantsNew->payment_method = $request['payment_method'];
            $PaymantsNew->is_paid = 1;
            $PaymantsNew->save();
            $message_data['orderId'] = ($DarshanOrder['order_id'] ?? '');
            $message_data['temple_name'] = ($DarshanOrder['Temple']['name'] ?? '');
            $message_data['title_name'] = ($DarshanOrder['title'] ?? '');
            $message_data['service_name'] = ($DarshanOrder['package_name'] ?? '');
            $message_data['booking_date'] = date("d M,Y", strtotime($DarshanOrder['date'] ?? ''));
            $message_data['time'] = ($DarshanOrder['time'] ?? '');
            $message_data['final_amount'] = webCurrencyConverter(amount: (float)($DarshanOrder['price'] ?? 0));
            $message_data['customer_id'] = $userDetails['id'];
            if (($DarshanOrder['Temple']['thumbnail'] ?? '')) {
                $message_data['type'] = 'text-with-media';
                $message_data['attachment'] = getValidImage(path: 'storage/app/public/temple/thumbnail/' . ($DarshanOrder['Temple']['thumbnail'] ?? ''), type: 'backend-logo');
            }
            Helpers::whatsappMessage('vipdarshan', 'Vip Darshan booking Confirmed', $message_data);
            if ($DarshanOrder['Members'] && count($DarshanOrder['Members']) > 0) {
                foreach ($DarshanOrder['Members'] as $key => $vals) {
                    $messageData = [
                        'customer_id' => $DarshanOrder['user_id'],
                        'member_names' => $vals['name'],
                        'link' => url('api/v1/darshan/vip-pass', ['barcode' => base64_encode($vals['barcode'])]),
                    ];
                    \App\Jobs\SendWhatsappMessage::dispatch('vipdarshan', 'Vip Darshan pass attachment', $messageData);
                }
            }
            return response()->json(['status' => 1, "message" => "Paymant Successfully", 'data' => []], 200);
        } else {
            return response()->json(['status' => 0, "message" => "Invalid Data Pass", 'data' => []], 200);
        }
    }

    public function BookingList(Request $request)
    {
        $query = DarshanOrder::where('user_id', $request->user()->id)->whereIn('status', [1, 2])->with(['Temple', 'userData', 'Members']);
        if ($request['id'] && !empty($request['id'])) {
            $query->where('id', $request['id']);
        }
        $DarshanOrder = $query->orderBy('id', 'desc')->get();
        if ($DarshanOrder) {
            $OrderList = [];
            $p = 0;
            foreach ($DarshanOrder as $key => $value) {
                $OrderList[$p]['id'] = $value['id'] ?? "";
                $OrderList[$p]['order_id'] = $value['order_id'] ?? "";
                $OrderList[$p]['title'] = $value['title'] ?? "";
                $OrderList[$p]['package_name'] = $value['package_name'] ?? "";
                $OrderList[$p]['people_qty'] = $value['people_qty'] ?? "";
                $OrderList[$p]['price'] = (int)$value['price'] ?? "";
                $OrderList[$p]['status'] = $value['status'] ?? "";
                $OrderList[$p]['created_at'] = date('d-m-Y h:i A', strtotime($value['created_at'] ?? ""));
                $OrderList[$p]['en_temple_name'] = $value['Temple']['name'] ?? "";
                $translate = $value['Temple'] ? $value['Temple']->translations()->pluck('value', 'key')->toArray() : [];
                $OrderList[$p]['hi_temple_name'] = $translate['name'] ?? "";
                $OrderList[$p]['invoice'] = url('api/v1/darshan/vip-invoice', [$value['id']]);
                $OrderList[$p]['image'] = getValidImage(path: 'storage/app/public/temple/thumbnail/' . $value['Temple']['thumbnail'] ?? '', type: 'backend-product');
                if ($request['id'] && !empty($request['id'])) {
                    $OrderList[$p]['user_name']  = $value['userData']['name'] ?? '';
                    $OrderList[$p]['user_phone']  = $value['userData']['phone'] ?? '';
                    $OrderList[$p]['user_email']  = $value['userData']['email'] ?? '';

                    $OrderList[$p]['booking_date']  = date('d-m-Y', strtotime($value['date']));
                    $OrderList[$p]['time_slot']  = $value['time'] ?? '';
                    $OrderList[$p]['payment_status']  = (($value['status'] == 1) ? "paid" : "unpaid");
                    $OrderList[$p]['payment_method']  = (($value['transaction_id'] == 'wallet') ? 'Wallet' : "online");
                    $OrderList[$p]['sub_total']  = (int)((float) $value['price'] ?? 0) - ((float) $value['gst_amount'] ?? 0);
                    $OrderList[$p]['total_tax']  = (int)((float) $value['gst_amount'] ?? 0);
                    $OrderList[$p]['pay_amount']  = (int)((float) $value['price'] ?? 0);
                    $OrderList[$p]['member_list'] = [];
                    if ($value['Members'] && count($value['Members']) > 0) {
                        $qy = 0;
                        foreach ($value['Members'] as $k => $val) {
                            $OrderList[$p]['member_list'][$qy]['name'] = ucwords($val['name']);
                            $OrderList[$p]['member_list'][$qy]['phone'] = $val['phone'];
                            $OrderList[$p]['member_list'][$qy]['aadhar'] = $val['aadhar'];
                            $getImages = \App\Models\UserAadhaarKyc::where('aadhaar_number', $val['aadhar'])->first();
                            $OrderList[$p]['member_list'][$qy]['image'] = $getImages['image'] ?? '';
                            $OrderList[$p]['member_list'][$qy]['pass'] = url('api/v1/darshan/vip-pass', ['barcode' => base64_encode($val['barcode'])]);
                            $qy++;
                        }
                    }
                }
                $p++;
            }
            if ($request['id'] && !empty($request['id'])) {
                return response()->json(['status' => 1, "message" => "Get List", 'data' => $OrderList[0] ?? []], 200);
            } else {
                return response()->json(['status' => 1, "message" => "Get List", 'data' => $OrderList], 200);
            }
        } else {
            return response()->json(['status' => 0, "message" => "Not Found Data", 'data' => []], 200);
        }
    }

    public function AadharSendOtp(Request $request)
    {
        $request->validate([
            'aadhaar_number' => ['required', 'digits:12'],
        ]);

        try {
            $key_check = UserAadhaarKyc::where('aadhaar_number', $request['aadhaar_number'])->first();

            if ($key_check) {
                $newRe = [];
                $newRe['name']   = $key_check->full_name ?? '';
                $newRe['phone']  = $key_check->phone_no ?? '';
                $newRe['aadhar'] = $key_check->aadhaar_number ?? '';
                $newRe['image'] = $key_check->image ?? '';
                $newRe['verify'] = 1;
                $addr = $key_check->address ?? [];
                if (is_string($addr)) {
                    $addr = json_decode($addr, true);
                }
                if (is_array($addr)) {
                    $parts = array_filter([
                        $addr['house']     ?? '',
                        $addr['street']    ?? '',
                        $addr['landmark']  ?? '',
                        $addr['loc']       ?? '',
                        $addr['vtc']       ?? '',
                        $addr['po']        ?? '',
                        $addr['subdist']   ?? '',
                        $addr['dist']      ?? '',
                        $addr['state']     ?? '',
                        $addr['country']   ?? ''
                    ]);
                    $newRe['address'] = implode(', ', $parts);
                } else {
                    $newRe['address'] = '';
                }
                return response()->json([
                    'status'  => 2,
                    'message' => 'Already KYC Completed',
                    'data'    => $newRe,
                ], 200);
            }
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Accept' => 'application/json'
            ])->post('https://quickekyc.com/api/v1/aadhaar-v2/generate-otp', [
                'key' => getWebConfig(name: 'aadhaar_verification_key'),
                'id_number' => $request->aadhaar_number,
            ]);

            if ($response->successful()) {
                $data_message = $response->json();
                if ($data_message['status'] == 'success') {
                    return response()->json([
                        'status' => 1,
                        'message' => $data_message['message'] ?? 'OTP sent successfully',
                        'data' => $data_message,
                        'request_id' => $data_message['request_id'],
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => $data_message['message'] ?? '',
                        'data' => $data_message,
                        'request_id' => $data_message['request_id'],
                    ], 200);
                }
            }

            return response()->json([
                'status' => 0,
                'message' => 'Failed to send OTP.',
                'error' => $response->json(),
                'request_id' => '',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
                'request_id' => '',
            ], 200);
        }
    }

    public function AadharOtpVerify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
            'request_id' => 'required',
            "user_id" => "nullable|exists:users,id",
        ]);

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Accept' => 'application/json'
            ])->post('https://quickekyc.com/api/v1/aadhaar-v2/submit-otp', [
                'key' => getWebConfig(name: 'aadhaar_verification_key'),
                'request_id' => $request->request_id,
                'otp' => $request->otp,
            ]);

            if ($response->successful()) {
                $message_data = $response->json();
                if ($message_data['status'] == "error") {
                    return response()->json([
                        'status' => 0,
                        'message' => $message_data['message'] ?? '.',
                        'data' => $message_data,
                        'data1' => [],
                    ], 200);
                }
                $message_data1 = [
                    'full_name' => $message_data['data']['full_name'] ?? "",
                    'user_id' => $request['user_id'] ?? "",
                    'aadhaar_number' => $message_data['data']['aadhaar_number'] ?? "",
                    'dob' => $message_data['data']['dob'] ?? "",
                    'gender' => $message_data['data']['gender'] ?? "",
                    'aadhaar_pdf' => $message_data['data']['aadhaar_pdf'] ?? "",
                    'mobile_verified' => $message_data['data']['mobile_verified'] ?? "",
                    'zip' => $message_data['data']['zip'] ?? "",
                    'mobile_hash' => $message_data['data']['mobile_hash'] ?? "",
                    'address' => json_encode($message_data['data']['address'] ?? []),
                    'image' => "data:image/jpeg;base64," . $message_data['data']['profile_image'],
                    'phone_no' => $request['phone_no'] ?? '',
                ];
                UserAadhaarKyc::insert($message_data1);
                $newRe['name'] = $message_data1['full_name'];
                $newRe['phone'] = $message_data1['phone_no'];
                $newRe['aadhar'] = $message_data1['aadhaar_number'];
                $addr = $message_data1['address'] ?? [];
                if (is_string($addr)) {
                    $addr = json_decode($addr, true);
                }

                if (is_array($addr)) {
                    $parts = array_filter([
                        $addr['house']    ?? '',
                        $addr['street']   ?? '',
                        $addr['landmark'] ?? '',
                        $addr['loc']      ?? '',
                        $addr['vtc']      ?? '',
                        $addr['po']       ?? '',
                        $addr['subdist']  ?? '',
                        $addr['dist']     ?? '',
                        $addr['state']    ?? '',
                        $addr['country']  ?? '',
                        $addr['pc']       ?? ''
                    ]);
                    $newRe['address'] = implode(', ', $parts);
                } else {
                    $newRe['address'] = '';
                }

                $newRe['image']  = $message_data1['image'] ?? '';
                $newRe['verify'] = 1;

                return response()->json([
                    'status' => 1,
                    'message' => $message_data['status'] ?? 'successfully.',
                    'data' => $message_data,
                    'data1' => $newRe,
                ], 200);
            }

            return response()->json([
                'status' => 0,
                'message' => 'Failed.',
                'error' => $response->json(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 200);
        }
    }



    public function AadharDetailsGet(Request $request)
    {
        $request->validate([
            'aadhar' => ['required', 'digits:12'],
        ]);

        try {
            $key_check = UserAadhaarKyc::where('aadhaar_number', $request['aadhar'])->first();
            if ($key_check) {
                return response()->json([
                    'status' => 1,
                    'message' => 'get Aadhaar Information Successfully',
                    'data' => $key_check,
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'not Found',
                    'data' => [],
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 200);
        }
    }

    public function TempleDarshanLimitCheck(Request $request)
    {
        // Validate request
        $data = $request->validate([
            'temple_id'  => 'required|integer|exists:temples,id',
            'package'    => 'required|integer',
            'purohit_id' => 'required|integer',
            'date'       => 'required|date_format:d-m-Y',
            'time'       => 'required|string',
            'count'      => 'required|integer|min:1',
        ]);
        $temple = \App\Models\Temple::findOrFail($data['temple_id']);
        $vipPlans = json_decode($temple->vip_plans, true);
        $purohitExists = \App\Models\Purohit::where('temple_id', $temple->id)
            ->where('id', $data['purohit_id'])
            ->where('status', 1)
            ->exists();

        if (!$purohitExists) {
            return response()->json([
                'status' => 0,
                'message' => 'Selected purohit not available.'
            ], 404);
        }

        $packageLimit = null;
        foreach ($vipPlans as $plan) {
            if (!isset($plan['package'])) continue;

            foreach ($plan['package'] as $pkg) {
                if (isset($plan['id']) && $plan['id'] == $data['package']) {
                    $packageLimit = isset($pkg['limit']) ? (int)$pkg['limit'] : 0;
                    break 2;
                }
            }
        }

        if ($packageLimit === null) {
            return response()->json([
                'status'  => 0,
                'message' => 'Package not found in temple VIP plans.'
            ], 404);
        }

        if ($packageLimit === 0) {
            return response()->json([
                'status'  => 1,
                'message' => 'Unlimited package available.'
            ]);
        }

        // Get current booked quantity
        $currentQty = \App\Models\DarshanOrder::where('temple_id', $data['temple_id'])
            ->where('package_id', $data['package'])
            ->where('status', 1)
            ->where('date', $data['date']) // make sure DB column is Y-m-d
            ->where('time', $data['time'])
            ->sum('people_qty');

        // Check remaining slots
        $remaining = max(0, $packageLimit - $currentQty);
        if (($currentQty + $data['count']) > $packageLimit) {
            $message = $remaining > 0 ? "$remaining slots remaining" : "All slots are booked";
            return response()->json([
                'status' => 0,
                'message' => $message
            ], 400);
        }

        // Allowed
        return response()->json([
            'status'  => 1,
            'message' => 'Please wait...'
        ]);
    }

    public function templePackages($templeId)
    {
        // Temple fetch
        $temple = Temple::select('id', 'name', 'package_service')->find($templeId);

        if (!$temple) {
            return response()->json([
                'status' => 404,
                'message' => 'Temple not found'
            ]);
        }

        // Package services decode
        $packages = json_decode($temple->package_service, true);

        if (!$packages || !is_array($packages)) {
            $packages = [];
        }

        $finalPackages = [];

        foreach ($packages as $package) {

            $packageId = $package['id'];

            // Price list for this package
            $prices = TempleServicePrice::where('package_id', $packageId)
                ->where('temple_id', $templeId)
                ->where('status', 1)
                ->get([
                    'id',
                    'varient_name',
                    'description',
                    'base_price',
                    'image',
                    'platform_fee_percentage',
                    'receipt_fee_percentage',
                    'gst_rate',
                    'color',
                    'is_available'
                ]);

            // Add slots inside each price
            foreach ($prices as $price) {

                // Default slot empty
                $price->slots = [];

                // Only show slots if price.is_available == 1
                if ($price->is_available == 1) {

                    $price->slots = TempleServiceSlot::where('temple_service_prices_id', $price->id)
                        ->where('is_available', 1)
                        ->where('status', 1)
                        ->get([
                            'id',
                            'day_of_week',
                            'start_time',
                            'end_time',
                            'slots_limi_capacity'
                        ]);
                }
            }

            $package['prices'] = $prices;

            $finalPackages[] = $package;
        }

        return response()->json([
            'status' => 200,
            'temple_id' => $temple->id,
            'temple_name' => $temple->name,
            'packages' => $finalPackages
        ]);
    }
    public function templePurohit($templeId)
    {
        // Fetch purohits based on temple_id
        $purohits = Purohit::where('temple_id', $templeId)
            ->where('status', 1) // only active purohits
            ->get([
                'id',
                'temple_id',
                'name',
                'mobile',
                'profile',
                'address',
                'description'
            ]);

        if ($purohits->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No Purohit found for this temple'
            ]);
        }

        return response()->json([
            'status' => 200,
            'temple_id' => $templeId,
            'purohits' => $purohits
        ]);
    }

    public function templeTimeSlot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required',
            'date' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        $datestr = date('l', strtotime($request['date']));
        $getData = \App\Models\TempleServiceSlot::select('id', 'start_time', 'end_time', 'day_of_week', 'slots_limi_capacity')->where(['status' => 1, 'temple_service_prices_id' => $request['package_id'], 'day_of_week' => $datestr])->get()->toArray();
        if ($getData) {
            return response()->json(['status' => 1, "message" => "get Data Successfully", 'data' => $getData], 200);
        } else {
            return response()->json(['status' => 0, "message" => "Not Found Data", 'data' => []], 200);
        }
    }

    public function PujaGetPackages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temple_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        $getTemple = Temple::where('id', $request['temple_id'])->orWhere('slug',$request['temple_id'])->first();
        $request['temple_id'] = $getTemple['id'];
        $groupByArray = [];
        if ($getTemple) {
            $groupByArray['service'] = [];
            if ($getTemple['package_service'] && json_decode($getTemple['package_service'] ?? "[]", true)) {
                foreach (json_decode($getTemple['package_service'] ?? "[]", true) as $ke => $val) {
                    if ($val['status'] == '1') {
                        $groupByArray['service'][] = $val['name'];
                    }
                }
            }
            foreach ($groupByArray['service'] as $key => $value) {
                $getDataN = \App\Models\TempleServicePrice::select('varient_name', 'base_price', 'daily_slots_limit', 'id', 'platform_fee_percentage', 'receipt_fee_percentage', 'gst_rate')
                    ->where([
                        'temple_id' => $request['temple_id'],
                        'trust_id' => $getTemple['trust_id'],
                        'status' => 1,
                        'package_id' => \App\Models\TempleServicePackages::where('name', $value)->value('id'),
                    ])
                    ->get()
                    ->toArray();
                $groupByArray[$value] = $getDataN;
            }
            $groupByArray['purohit'] = \App\Models\Purohit::select('name', 'id')
                ->where([
                    'temple_id' => $request['temple_id'],
                    'status' => 1
                ])
                ->get()
                ->toArray();
            $getorders = \App\Models\TempleOrderMaster::where('user_id', $request->user()->id)->get();

            $finalResult = [];

            foreach ($getorders as $orderMaster) {
                $details = \App\Models\TempleOrderDetails::where('order_id', $orderMaster->order_id)->get();
                foreach ($details as $detail) {
                    $kycRecords = $detail->getCustomerAadhaarNumbers();
                    if ($kycRecords->isNotEmpty()) {
                        foreach ($kycRecords as $key => $aname) {
                            $aadhaar = ($aname->aadhaar_number??'');
                            if (!$aadhaar) continue;
                            $finalResult[$aadhaar] = [
                                'name'     => $aname->full_name ?? "",
                                'phone'    => $aname->phone_no ?? "",
                                'aadhar' => $aname->aadhaar_number ?? "",
                            ];
                        }
                    }
                }
            }
            $translates = $getTemple ? $getTemple->translations()->pluck('value', 'key')->toArray() : [];
            $groupByArray['user_aadhar'] = array_values($finalResult);
            $groupByArray['id'] = $getTemple['id'];
            $groupByArray['en_temple_name'] = $getTemple['name'];
            $groupByArray['hi_temple_name'] = $translates['name'] ?? "";
            $groupByArray['en_short_description'] = $getTemple['short_description'];
            $groupByArray['hi_short_description'] = $translates['short_description'] ?? "";
            $groupByArray['en_details'] = $getTemple['details'];
            $groupByArray['hi_details'] = $translates['details'] ?? "";
            $groupByArray['aadhaar_verify_status'] = $getTemple['aadhaar_verify_status'];
            $groupByArray['image'] = getValidImage(path: 'storage/app/public/temple/thumbnail/' . $getTemple['thumbnail'] ?? '', type: 'backend-product');

            return response()->json(['status' => 1, "message" => "get Data Successfully", 'data' => $groupByArray], 200);
        } else {
            return response()->json(['status' => 0, "message" => "Temple Id Invalid", 'data' => []], 200);
        }
    }

    public function PujaGetBookingleadUpdate(Request $request){
        $validator = Validator::make($request->all(), [
            "temple_id"=>"required",
            'customer_qty' => 'required',
            'package_id' => 'required',
            "type"=>"required|in:locker,bhojan,puja,darshan",
            "customers"=>'required',
            "date"=>"required",
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }
        
        $orderId = '';
        $typeOrderId = '';
        $lastOrder = \App\Models\TempleLeadMaster::select('id')->latest()->first();
        $lastId = !empty($lastOrder['id']) ? (100001 + $lastOrder['id']) : 100001;
        $leadmasterCheck = \App\Models\TempleLeadMaster::where('user_id', $request->user()->id)
            ->where('payment_status', 0)
            ->where('status', 0)
            ->first();
        $packageData = TempleServicePrice::find($request->package_id);
        if ($packageData) {
            $basePrice   = $packageData->base_price;
            $platformFee = $packageData->platform_fee_percentage;
            $receiptFee  = $packageData->receipt_fee_percentage;
            $perCustomerPrice = $basePrice;
            if ($request->type == 'bhojan' || $request->type == 'locker') {
                $gstAmount = 0;
            } else {
                $gstAmount = ($perCustomerPrice * $packageData->gst_rate) / 100;
            }
            $totalPricePerCustomer = $perCustomerPrice + $gstAmount + $platformFee + $receiptFee;
            $totalPrice = $totalPricePerCustomer * $request->customer_qty;
        } else {
            $totalPrice = 0;
            $totalPricePerCustomer = 0;
        }

        if ($leadmasterCheck) {
            $orderId = $leadmasterCheck->order_id;
            \App\Models\TempleLeadMaster::where('order_id', $orderId)->update([
                'customer_qty' => $leadmasterCheck->customer_qty + $request->customer_qty,
                'amount'       => $leadmasterCheck->amount + $totalPrice,
                'payment_mode' => ($leadmasterCheck->amount + $totalPrice) > 0 ? 'cash' : 'free',
            ]);
        } else {
            $orderId = 'MCOM' . $lastId;
            $trustid = Temple::select('trust_id')->where('id', $request->temple_id)->first();
            \App\Models\TempleLeadMaster::create([
                'temple_id'    => $request->temple_id ?? null,
                'user_id'      => $request->user()->id,
                'trust_id'     => $trustid['trust_id'] ?? null,
                'order_id'     => $orderId,
                'customer_qty' => $request->customer_qty,
                'amount'       => $totalPrice,
                'payment_mode' => $totalPrice > 0 ? 'cash' : 'free',
            ]);
        }

        // Lead details
        $leadDetailsCheck = \App\Models\TempleLeadDetail::where('order_id', $orderId)
            ->where('type', $request->type)
            ->exists();

        if (!$leadDetailsCheck) {
            if ($request->type == 'puja' && !empty($request->pandit_id)) {
                $panditId = $request->pandit_id;
            } else {
                $purohits = Purohit::where('temple_id', $request->temple_id)
                    ->where('status', 1)
                    ->get();
                $panditId = $purohits->count() > 0 ? $purohits->random()->id : null;
            }
            $timeSlotID = $request->type == 'darshan' || $request->type == 'bhojan' ? $request->time_slot_id : null;

            $typeOrderId = ($request->type == 'puja' ? 'PJ' : ($request->type == 'darshan' ? 'DS' : ($request->type == 'bhojan' ? 'BJ' : 'LK'))) . $lastId;
            \App\Models\TempleLeadDetail::create([
                'package_id'   => $request->package_id,
                'amount'       => $totalPrice,
                'booking_date' => $request->date,
                'order_id'     => $orderId,
                'type'         => $request->type,
                'type_order_id' => $typeOrderId,
                'customer_qty'  => $request->customer_qty,
                'customers'    => json_encode(json_decode($request->customers??"[]",true)),
                'pandit_id'    => $panditId,
                'time_slot_id' => $timeSlotID,
                'locker_items' => $request->locker_items ? json_encode($request->locker_items) : null,
            ]);
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error']);
    }
    public function PujaGetBookingAll(Request $request){
        $validator = Validator::make($request->all(), [
            'payment_mode' => 'required|in:free,online,cash',
            "temple_id"=>"required",
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'errors' => $validator->errors()], 200);
        }

        
        $lead = \App\Models\TempleLeadMaster::where('user_id', $request->user()->id)->where('temple_id', $request->temple_id)->where('status', 0)->with(['details', 'temple'])->first();

        if (!$lead) {
             return response()->json(['status' => 0, "message" => "Pending lead not found!", 'data' => []], 200);
        }
        // Cash\online payment flow            
                $lead->update([
                    'payment_mode'   => $request->payment_mode,
                    'payment_status' => 1,
                    'status'         => 1,
                ]);           

            $order = \App\Models\TempleOrderMaster::updateOrCreate(
                ['order_id' => $lead->order_id ?? ('ORD' . time())],
                [
                    'lead_id'            => $lead->id,
                    'user_id'            => $lead->user_id,
                    'temple_id'          => $lead->temple_id,
                    'trust_id'           => $lead->trust_id,
                    'total_people_count' => $lead->customer_qty,
                    'total_amount'       => $lead->amount,
                    'transaction_id'     => $lead->payment_mode,
                    'booking_status'     => (($request->payment_mode == 'online' || $request->payment_mode == 'free')? 'confirmed'  :'pending'),
                    'platform'           => 'qr',
                    'payment_mode'       => $request->payment_mode,
                    'status'             => 1,
                    'payment_status'     => (($request->payment_mode == 'online' || $request->payment_mode == 'free')? 1  : 0 ),
                ]
            );
            $whatsapp_message_data = [];
            $whatsapp_message_data['type'] = 'text-with-media';
            $whatsapp_message_data['temple_name'] = $lead['temple']['name'];

            $leadDetails = \App\Models\TempleLeadDetail::where('order_id', $lead->order_id)->where('status', 1)->with('package')->get();
            foreach ($leadDetails as $detail) {
                $customers = json_decode($detail->customers, true) ?? [];
                $basePrice  = $detail->package->base_price ?? 0;
                $gstRate    = $detail->type == 'locker' || $detail->type == 'bhojan' ? 0 : $detail->package->gst_rate;
                $platformFeePercent = $detail->package->platform_fee_percentage ?? 0;
                $receiptFeePercent  = $detail->package->receipt_fee_percentage ?? 0;

                $gstAmount = ($gstRate > 0) ? (($basePrice * $gstRate) / 100) : 0;
                $timeSlot = TempleServiceSlot::where('id', $detail->time_slot_id)->where('temple_service_prices_id', $detail->package_id)->first();

                $orderGet = \App\Models\TempleOrderDetails::updateOrCreate(
                    [
                        'order_id' => $detail->order_id,
                        'type'     => $detail->type,
                    ],
                    [
                        'package_id'     => $detail->package_id,
                        'temple_id'      => $lead->temple_id,
                        'trust_id'       => $lead->trust_id,
                        'people_count'   => $detail->customer_qty,
                        'gst'            => $gstAmount,
                        'base_price'     => $basePrice,
                        'platform_fee'   => $platformFeePercent,
                        'receipt_fee'    => $receiptFeePercent,
                        'final_amount'   => $detail->amount,
                        'booking_date'   => $detail->booking_date,
                        'customers'      => $detail->customers,
                        'type_order_id'  => $detail->type_order_id,
                        'time_slot'      => $timeSlot ? ($timeSlot->start_time . ' - ' . $timeSlot->end_time) : 'pending',
                        'booking_status' => (($request->payment_mode == 'online' || $request->payment_mode == 'free')? 'confirmed'  :'pending'),
                        'locker_items' => $request->locker_items ? json_encode($request->locker_items) : ($detail->locker_items ?? '{}'),
                        'purohit_id'     => $detail->pandit_id,
                        'status'         => 1,
                        'payment_status' => (($request->payment_mode == 'online' || $request->payment_mode == 'free')? 1  : 0),
                    ]
                );
                $getTypes = \App\Models\TempleOrderDetails::where('order_id', $detail->order_id)->first();

                if ($getTypes && strtolower($getTypes->type ?? '') === 'puja') {
                    $templeId = $detail->temple_id ?? $getTypes->temple_id ?? 0;
                    \App\Models\TrustPanditTransection::create([
                        'order_id'       => $detail->order_id,
                        'type_order_id'  => $detail->type_order_id ?? null,
                        'temple_id'      => $templeId,
                        'trust_id'       => $detail->trust_id ?? $getTypes->trust_id ?? 0,
                        'pandit_id'      => $detail->pandit_id ?? 0,
                        'package_id'     => $detail->package_id ?? 0,
                        'package_price' => (($basePrice ?? 0) * ($detail->customer_qty ?? 0)),
                        'payment_method' => $request->payment_mode,
                        'payment_status' => (($request->payment_mode == 'online' || $request->payment_mode == 'free')? 'confirmed'  :'pending'),
                    ]);
                }

                $order->load('temple');
                $orderGet->load('package');

                // Toastr::success(translate('Cash payment recorded successfully!'));
                $memberNames = collect($customers)->pluck('name')->filter()->implode(', ');

                $serviceType = $detail->type == 'puja' ? 'Pooja Booking' : ($detail->type == 'darshan' ? 'Darshan Booking' : ($detail->type == 'bhojan' ? 'Bhojan Booking' : 'Locker Booking'));

                $whatsapp_message_data[$detail->type] = [
                    'Service' => $serviceType,
                    'Package Name' => $detail['package']['varient_name'],
                    'Booking Date' => date('d-m-Y', strtotime($detail->booking_date)),
                    'Amount' => webCurrencyConverter($detail['amount']),
                ];
                if (!empty($detail['time_slot_id'])) {
                    $whatsapp_message_data[$detail->type]['Time Slot'] =
                        $detail['timeslot']['start_time'] . '-' . $detail['timeslot']['end_time'];
                }
                $customers = json_decode($detail['customers'], true);
                $lockerItems = json_decode($detail['locker_items'], true);
                if (!empty($customers)) {
                    $whatsapp_message_data[$detail->type]['Customers'] =
                        collect($customers)->pluck('name')->implode(', ');
                }
                if (!empty($lockerItems)) {
                    $whatsapp_message_data[$detail->type]['Locker Items'] =
                        collect($lockerItems)->map(fn($v, $k) => "$k($v)")->implode(', ');
                }
            }

            // email
            $userInfo = User::where('id', ($lead->user_id ?? ""))->first();
            // $service_name = TempleServicePrice::where('temple_id', $lead->temple_id)->where('package_id', $detail->package_id)->where('status', 1)->first();

            // qr
            $url = route('temple.show-qr-detail', ['order_id' => $lead->order_id]);
            $qrCode = new \Endroid\QrCode\QrCode($url);
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);
            $folder = storage_path('app/public/temple/qrcodes');
            if (!\Illuminate\Support\Facades\File::exists($folder)) {
                \Illuminate\Support\Facades\File::makeDirectory($folder, 0777, true);
            }
            $filePath = $folder . "/" . $lead->order_id . ".png";
            $result->saveToFile($filePath);
            $imageData = "<img class='upload-img-view' src='" . getValidImage(path: 'storage/app/public/temple/qrcodes/' . $lead->order_id . '.png', type: 'backend-product') . "' alt='' style='width:130px'>";
            $encodedQr = base64_encode($imageData);

            // invoice
            $mpdf_view = \View::make('web-views.temple.invoice', compact('userInfo', 'lead', 'leadDetails','imageData'));
            Helpers::gen_mpdf_temple_Pdf($mpdf_view, 'temple_order_', $lead['order_id']);
            $whatsapp_message_data['attachment'] = asset('storage/app/public/temple/invoice/temple_order_' . $lead['order_id'] . '.pdf');

            // whatsapp msg  
            if($userInfo){
                $whatsapp_message_data['orderId'] = $lead->order_id;
                $whatsapp_message_data['final_amount'] = $lead->amount;
                $whatsapp_message_data['customer_id'] = $userInfo->id;
                $messages =  Helpers::whatsappMessage('temple', 'Service Booking', $whatsapp_message_data);
            }              

            // email
            if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                $data['type'] = 'pooja';
                $data['email'] = $userInfo['email'];
                $data['subject'] = 'Confirmation of Your Order #' . $lead->order_id;
                $data['htmlContent'] = \Illuminate\Support\Facades\View::make(
                    'admin-views.email.email-template.temple-template',
                    compact('userInfo', 'lead', 'leadDetails')
                )->render();
                Helpers::emailSendMessage($data);
            }
             return response()->json(['status' => 1, "message" => "Temple Booking Success", 'data' => ['order_id' => $lead->order_id]], 200);
        
        // Toastr::error(translate('Invalid_payment_mode!'));
        return response()->json(['status' => 1, "message" => translate('Invalid_payment_mode!'), 'data' => []], 200);

    }
}
