<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\AccountNumberVerified;
use App\Models\GstNumberVerified;
use Illuminate\Http\Request;

class DocumentVerifyController extends Controller
{

    public function GstNumberVerify(Request $request)
    {
        $request->validate([
            'gst_number' => ['required', 'string'],
        ]);
        try {
            $checkData = GstNumberVerified::where('gstin', $request['gst_number'])->first();
            if ($checkData) {
                return response()->json([
                    'status' => 1,
                    'message' => 'success.',
                    'data' => [],
                ], 200);
            }
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Accept' => 'application/json'
            ])->post('https://quickekyc.com/api/v1/corporate/gstin', [
                'key' => getWebConfig(name: 'aadhaar_verification_key'),
                'id_number' => $request->gst_number,
                "filing_status_get" => true
            ]);
            $message_data = $response->json();
            if ($response->successful()) {
                if ($message_data['status'] == 'success') {
                    $message_data1 = [
                        "gstin" => $message_data['data']['gstin'] ?? "",
                        "pan_number" => $message_data['data']['pan_number'] ?? "",
                        "business_name" => $message_data['data']['business_name'] ?? "",
                        "legal_name" => $message_data['data']['legal_name'] ?? "",
                        "center_jurisdiction" => $message_data['data']['center_jurisdiction'] ?? "",
                        "state_jurisdiction" => $message_data['data']['state_jurisdiction'] ?? "",
                        "date_of_registration" => $message_data['data']['date_of_registration'] ?? "",
                        "constitution_of_business" => $message_data['data']['constitution_of_business'] ?? "",
                        "taxpayer_type" => $message_data['data']['taxpayer_type'] ?? "",
                        "gstin_status" => $message_data['data']['gstin_status'] ?? "",
                        "date_of_cancellation" => $message_data['data']['date_of_cancellation'] ?? "",
                        "field_visit_conducted" => $message_data['data']['field_visit_conducted'] ?? "",
                        "nature_bus_activities" => isset($message_data['data']['nature_bus_activities']) ? implode(',', $message_data['data']['nature_bus_activities']) : null,
                        "nature_of_core_business_activity_code" => $message_data['data']['nature_of_core_business_activity_code'] ?? "",
                        "nature_of_core_business_activity_description" => $message_data['data']['nature_of_core_business_activity_description'] ?? "",
                        "filing_status" => isset($message_data['data']['filing_status']) ? json_encode($message_data['data']['filing_status']) : null,
                        "address" => $message_data['data']['address'] ?? "",
                        "hsn_info" => isset($message_data['data']['hsn_info']) ? json_encode($message_data['data']['hsn_info']) : null,
                    ];
                    GstNumberVerified::insert($message_data1);
                    return response()->json([
                        'status' => 1,
                        'message' => $message_data['message'] ?? 'successfully.',
                        'data' => $message_data,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => $message_data['message'],
                        'data' => $message_data,
                    ], 200);
                }
            }
            return response()->json([
                'status' => 0,
                'message' => $message_data['message'] ?? 'Failed.',
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

    public function BankAccountVerify(Request $request)
    {
        $request->validate([
            'account_number' => ['required', 'numeric'],
            'ifsc' => ['required'],
        ]);
        try {
            $checkData = AccountNumberVerified::where('account_number', $request['account_number'])->first();
            if ($checkData) {
                return response()->json([
                    'status' => 1,
                    'message' => 'success.',
                    'data' => [],
                ], 200);
            }
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                // 'Accept' => 'application/json',
                "Content-Type"=> "application/json"
            ])->post('https://quickekyc.com/api/v1/bank-verification', [
                'key' => getWebConfig(name: 'aadhaar_verification_key'),
                'id_number' => $request->account_number,
                "ifsc"=> $request->ifsc
            ]);
            $message_data = $response->json();
            if ($response->successful()) {
                if ($message_data['status'] == 'success') {
                    $message_data1 = [
                        "account_number" => $request->account_number ?? "",
                        "ifsc" => $request->ifsc ?? "",
                        "account_exists" => $message_data['data']['account_exists'] ?? "",
                        "upi_id" => $message_data['data']['upi_id'] ?? "",
                        "remarks" => $message_data['data']['remarks'] ?? "",
                        "ifsc_details" => isset($message_data['data']['ifsc_details']) ? json_encode($message_data['data']['ifsc_details']) : null,
                    ];
                    AccountNumberVerified::insert($message_data1);
                    return response()->json([
                        'status' => 1,
                        'message' => $message_data['message'] ?? 'successfully.',
                        'data' => $message_data,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => $message_data['message'],
                        'data' => $message_data,
                    ], 200);
                }
            }
            return response()->json([
                'status' => 0,
                'message' => $message_data['message'] ?? 'Failed.',
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
}
