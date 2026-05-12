<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\PaymentController;
use Illuminate\Http\Request;
use App\Models\{Temple, TempleTimeSlot, DarshanOrder, DarshanOrderMembers, UserAadhaarKyc, Purohit};
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
// use App\Helpers\Helpers;
use App\Utils\Helpers;
// use DNS1D;
use Milon\Barcode\DNS1D;

class DarshanController extends Controller
{
    public function showForm($slug)
    {
        $temple = Temple::with(['Trust','cities','states'])->where('slug', $slug)->first();
        if (!$temple) 
        abort(404, 'Temple not found');
        $vipPlans = json_decode($temple->vip_plans, true);  
        $purohits = Purohit::where('temple_id', $temple->id) ->where('status', 1) ->get();
        return view('web-views.darshan.individual-darshan.vip-darshan-form', [ 'temple' => $temple, 'plans' => $vipPlans, 'purohits' => $purohits ]);
    }
    
    public function cashsubmitForm(Request $request)
    {
        DB::beginTransaction();
        try {
            $personData = json_decode($request['people'], true) ?? [];
           
            if (empty($personData)) {
                return response()->json(['status' => 0, 'message' => 'No people data found'], 400);
            }
            $firstPerson = $personData[0];
            $user = \App\Models\User::where('phone', $firstPerson['mobile'] ?? '')->first();
            if (!$user) {
                $user = new \App\Models\User();
                $user->phone = $firstPerson['mobile'] ?? '';
                $user->name = $firstPerson['name'] ?? '';
                $nameParts = explode(' ', $firstPerson['name'] ?? '');
                $user->f_name = $nameParts[0] ?? '';
                $user->l_name = $nameParts[1] ?? '';
                $user->email = '';
                $user->password = bcrypt('12345678');
                $user->verify_otp = $request->input('verify_otp', 1);
                $user->save();
                Helpers::whatsappMessage('whatsapp', 'Welcome Message', ['customer_id' => $user->id]);
            }
            \Illuminate\Support\Facades\Auth::guard('customer')->loginUsingId($user->id);

            if ($request->payment_mode === 'cash') {
                $getTemples = Temple::where('id', $request['temple_id'])->first()['vip_plans'] ?? "[]";
                $vipPlans = json_decode($getTemples, true);
                $matched = collect($vipPlans)->firstWhere('id', $request['package']);
        
                if (!$matched) {
                    return response()->json(['status' => 0, 'message' => 'Invalid package selected'], 400);
                }
                $peopleQty = count($personData);
                $darshanOrder = new \App\Models\DarshanOrder();
                $darshanOrder->package_name   = $matched['name'] ?? '';
                $darshanOrder->temple_id      = $request->temple_id;
                $darshanOrder->title          = $matched['package'][0]['name'] ?? '';
                $darshanOrder->package_id     = $request['package'] ?? null;
                $darshanOrder->purohit_id     = $request->purohit_id ?? null;
                $darshanOrder->date           = $request->date;
                $darshanOrder->time           = $request->time;
                $darshanOrder->transaction_id = 'Cash';
                $darshanOrder->payment_method = 'Offline';
                $darshanOrder->payment_mode   = 'pending';
                $darshanOrder->platform       = 'qr';
                $darshanOrder->people_qty     = $peopleQty;
                $darshanOrder->price          = ($request->price ?? 0) * $peopleQty;
                $darshanOrder->receipt_price  = ($request->receipt_price ?? 0) * $peopleQty;
                $darshanOrder->platform_fee   = ($request->platform_fee ?? 0) * $peopleQty;
                $darshanOrder->final_amount   = $darshanOrder->price + $darshanOrder->receipt_price + $darshanOrder->platform_fee;
                $darshanOrder->status         = 0;
                $darshanOrder->user_id        = $user->id;
                $darshanOrder->save();

                foreach ($personData as $memberData) {
                    \App\Models\DarshanOrderMembers::create([
                        'darshan_id' => $darshanOrder->id,
                        'name' => $memberData['fullName'] ?? '',
                        'address' => $memberData['address'] ?? '',
                        'phone' => $memberData['phone'] ?? '',
                        'aadhar' => $memberData['aadhar'] ?? '',
                        'image' => $memberData['image'] ?? '',
                        'aadhar_verify_status' => $memberData['aadhar_verify_status'] ?? 0
                    ]);
                }
                $getTemple = \App\Models\Temple::find($request->temple_id);
                \App\Models\TrustPanditTransection::create([
                    'order_id'       => $darshanOrder->order_id,
                    'temple_id'      => $darshanOrder->temple_id,
                    'trust_id'       => $getTemple->trust_id ?? null,
                    'pandit_id'      => $darshanOrder->purohit_id,
                    'package_id'     => $matched['id'],
                    'package_price'  => $matched['price'] ?? 0,
                    'payment_method' => 'cash',
                    'payment_status' => 'pending'
                ]);

                DB::commit();

                return response()->json([
                    'status' => 1,
                    'message' => 'Cash booking successful',
                    'url' => route('vip-darshan-success', [$getTemple->slug ?? ''])
                ], 200);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 0, 'message' => $e->getMessage()], 500);
        }
    }

    public function submitForm(Request $request)
    {
        $request->validate([
            'time' => 'required|string',
            "people" => "required",
            'price' => "required",
        ]);

        DB::beginTransaction();
        try {
            if ($request['people'] && json_decode($request['people'], true)) {
                $getUsers = json_decode($request['people'], true)[0];
              
                $userfind = \App\Models\User::where('phone', $getUsers['phone'] ?? '')->first();
                if ($userfind) {
                    \Illuminate\Support\Facades\Auth::guard('customer')->loginUsingId($userfind['id']);
                } else {
                    $user = new \App\Models\User();
                    $user->phone = $getUsers['mobile'] ?? '';
                    $user->name = $getUsers['name'] ?? '';
                    $user->f_name = (explode(" ", $getUsers['name'] ?? '')[0] ?? "");
                    $user->l_name = (explode(" ", $getUsers['name'] ?? '')[1] ?? "");
                    $user->email = '';
                    $user->password =  bcrypt('12345678');
                    $user->verify_otp = $request->input('verify_otp') ?? 1;
                    $user->save();
                    \Illuminate\Support\Facades\Auth::guard('customer')->loginUsingId($user->id);
                    $data = [
                        'customer_id' => ($user->id ?? "")
                    ];
                    Helpers::whatsappMessage('whatsapp', 'Welcome Message', $data);
                }
                $getTemple = new \App\Models\TempleDarshanLead();
                $personData = json_decode($request['people'], true);
                $getTemple->name = $getUsers['fullName'] ?? '';
                $getTemple->phone = $getUsers['phone'] ?? '';
                $getTemples = Temple::where('id', $request['temple_id'])->first()['vip_plans'] ?? "[]";
                $vipPlans = json_decode($getTemples, true);
                $matched = collect($vipPlans)->firstWhere('id', $request['package']);
                $getTemple->title = $matched['name'] ?? '';
                $getTemple->package_name = $matched['package'][0]['name'] ?? '';;
                $getTemple->date = $request['date'];
                $getTemple->time = $request['time'];
                $getTemple->price = $request['price'];
                $getTemple->purohit_id = $request['purohit_id'];
                $getTemple->receipt_price = $request['receipt_price'];
                $getTemple->platform_fee = $request['platform_fee'];
                $getTemple->platform_gst = $request['platform_gst'];
                $getTemple->platform_base_price = $request['platform_base_price'];
                $getTemple->user_id = auth('customer')->id();
                $getTemple->temple_id = $request['temple_id'];
                $getTemple->package_id = $request['package'];
                $getTemple->people_qty = count($personData);
                $getTemple->people_info = json_encode($personData);
                $getTemple->save();
                $urls = PaymentController::withoutLoginTempleDarshanBookingPay($getTemple->id);
                if ($urls == 1) {
                    DB::commit();
                    return response()->json(['status' => 1, 'message' => 'Booking Success', 'url' => route('vip-darshan-success', [Temple::where('id', $request['temple_id'])->first()['slug'] ?? ''])], 200);
                } else if ($urls) {
                    DB::commit();
                    return response()->json(['status' => 1, 'message' => 'Pay Now', 'url' => $urls], 200);
                } else {
                    DB::rollback();
                    return response()->json(['status' => 0, 'message' => 'Failed'], 200);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 0, 'message' => 'Failed to process booking. Try again'], 200);
        }
    }
    // public function vipDarshanSuccess($slug)
    // {
    //     $temple = Temple::with(['Trust'])->where('slug', $slug)->first();

    //     if (!$temple) abort(404, 'Temple not found');
    //     $vipPlans = json_decode($temple->vip_plans, true);
    //     return view('web-views.darshan.individual-darshan.vip-darshan-pay-success', [
    //         'temple' => $temple,
    //         'plans' => $vipPlans,
    //     ]);
    // }

    public function vipDarshanSuccess($slug)
    {
        $temple = Temple::with(['Trust'])->where('slug', $slug)->first();

        if (!$temple) abort(404, 'Temple not found');

        $vipPlans = json_decode($temple->vip_plans, true);

        $planName = null;
        if (request()->has('plan_id')) {
            $planId = request('plan_id');
            foreach ($vipPlans as $plan) {
                if ($plan['id'] == $planId) {
                    $planName = $plan['name'];
                    break;
                }
            }
        }

        return view('web-views.darshan.individual-darshan.vip-darshan-pay-success', [
            'temple' => $temple,
            'plans' => $vipPlans,
            'planName' => $planName,
        ]);
    }

    public function razorpayCallback(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'order_id' => 'required|string',
        ]);

        $order = DarshanOrder::where('order_id', $request->order_id)->first();
        if (!$order) {
            return response()->json(['status' => 0, 'message' => 'Invalid Order ID']);
        }

        $order->update([
            'payment_id' => $request->razorpay_payment_id,
            'status' => 'paid',
        ]);

        return response()->json(['status' => 1, 'message' => 'Payment successful.']);
    }

    public function AadharSendOtp(Request $request)
    {
        $request->validate([
            'aadhaar_number' => ['required', 'digits:12'],
        ]);

        try {
            $key_check = UserAadhaarKyc::where('aadhaar_number', $request['aadhaar_number'])->first();
            if ($key_check) {
                $newRe['name'] = $key_check['full_name'];
                $newRe['phone'] = $key_check['phone_no'];
                $newRe['aadhar'] = $key_check['aadhaar_number'];
                $newRe['verify'] = 1;
                return response()->json([
                    'status' => 2,
                    'message' => 'Already KYC Completed',
                    'data' => $newRe,
                ], 200);
            }

            $response = Http::withHeaders([
                'Accept' => 'application/json'
            ])->post('https://quickekyc.com/api/v1/aadhaar-v2/generate-otp', [
                'key' => getWebConfig(name: 'aadhaar_verification_key'),
                'id_number' => $request->aadhaar_number,
            ]);

            if ($response->successful()) {
                $data_message = $response->json();
                return response()->json([
                    'status' => $data_message['status'] === 'success' ? 1 : 0,
                    'message' => $data_message['message'] ?? '',
                    'data' => $data_message,
                    'request_id' => $data_message['request_id'] ?? '',
                ], 200);
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
        ]);

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json'
            ])->post('https://quickekyc.com/api/v1/aadhaar-v2/submit-otp', [
                'key' => getWebConfig(name: 'aadhaar_verification_key'),
                'request_id' => $request->request_id,
                'otp' => $request->otp,
            ]);

            if ($response->successful()) {
                $message_data = $response->json();
                if ($message_data['status'] == 'error') {
                    return response()->json([
                        'status' => 0,
                        'message' => $message_data['message'] ?? '',
                        'data' => $message_data,
                    ], 200);
                }

                $message_data1 = [
                    'full_name' => $message_data['data']['full_name'] ?? '',
                    'aadhaar_number' => $message_data['data']['aadhaar_number'] ?? '',
                    'dob' => $message_data['data']['dob'] ?? '',
                    'gender' => $message_data['data']['gender'] ?? '',
                    'aadhaar_pdf' => $message_data['data']['aadhaar_pdf'] ?? '',
                    'mobile_verified' => $message_data['data']['mobile_verified'] ?? '',
                    'zip' => $message_data['data']['zip'] ?? '',
                    'mobile_hash' => $message_data['data']['mobile_hash'] ?? '',
                    'address' => json_encode($message_data['data']['address'] ?? []),
                    'phone_no' => $request['phone_no'] ?? '',
                ];

                UserAadhaarKyc::insert($message_data1);

                $newRe = [
                    'name' => $message_data1['full_name'],
                    'phone' => $message_data1['phone_no'],
                    'aadhar' => $message_data1['aadhaar_number'],
                    'verify' => 1,
                ];

                return response()->json([
                    'status' => 1,
                    'message' => 'Verified successfully.',
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
                    'message' => 'Get Aadhaar information successfully.',
                    'data' => $key_check,
                ], 200);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Not found',
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

    public function CreateVIPPass(Request $request)
    {
        $getData = DarshanOrderMembers::where('barcode', base64_decode(urldecode($request['barcode'])))->with(['darshanOrder'])->first();
        if ($getData) {
            $barcodes = DNS1D::getBarcodePNG(base64_decode(urldecode($request['barcode'])), 'C128', '3', '80');
            $mpdf_view = View::make('web-views.darshan.booking.entrypass', compact('getData', 'barcodes'));
            Helpers::gen_mpdf($mpdf_view, 'darshan_', str_replace(' ', '_', $getData['name']));
            return $request->wantsJson() || $request->ajax()
                ? response()->json(["status" => 1, "message" => "Entry Pass generated successfully."])
                : back();
        } else {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['status' => 0, 'message' => 'Not Found Barcode', 'recode' => 0, 'data' => []], 200)
                : back();
        }
    }

    public function DarshanOrderInvoice(Request $request)
    {
        $getData = DarshanOrder::where('id', $request['id'])->with(['userData', 'Temple'])->first();
        if ($getData) {
            $mpdf_view = View::make('web-views.darshan.booking.invoice', compact('getData'));
            Helpers::gen_mpdf($mpdf_view, 'darshan_invoice_', str_replace(' ', '_', $getData['order_id']));
            return $request->wantsJson() || $request->ajax()
                ? response()->json(["status" => 1, "message" => "Invoice generated successfully."])
                : back();
        } else {
            return $request->wantsJson() || $request->ajax()
                ? response()->json(['status' => 0, 'message' => 'Not Found Barcode', 'recode' => 0, 'data' => []], 200)
                : back();
        }
    }

}