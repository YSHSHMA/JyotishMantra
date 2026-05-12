<?php

namespace App\Http\Controllers\RestAPI\v3\seller\auth;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\SellerWallet;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $data = [
            'phone' => $request->phone
        ];

        $seller = Seller::where(['phone' => $request['phone']])->first();
        if (!$seller) {
            $vendoremployee = \App\Models\VendorEmployees::with(['seller', 'tour', 'trust', 'event'])->where('phone', $request['phone'])->where('status', 1)->first();
            $vendorPurohit = \App\Models\Purohit::with(['temple'])->where('mobile', $request['phone'])->where('status', 1)->first();
            if (!empty($vendoremployee) && $vendoremployee) {
                // $passwordCheck = \Illuminate\Support\Facades\Hash::check($request['password'], $vendoremployee['password']);
                // if (!$passwordCheck) {
                //     return response()->json(['error' => "credentials doesn't match" . '!']);
                // }
                $token = $vendoremployee->createToken('employee')->accessToken;
                $vendoremployee->update(['auth_token' => $token]);

                if ($vendoremployee['type'] == 'tour' || $vendoremployee['type'] == 'trust' || $vendoremployee['type'] == 'event') {
                    $getstatus =  Seller::where(['relation_id' => $vendoremployee['vendors']['id']])->where('type', $vendoremployee['type'])->first();
                } else {
                    $getstatus =  Seller::where(['id' => $vendoremployee['vendors']['id']])->where('type', 'seller')->first();
                }
                if($vendoremployee['purohit_id'] == 0 && $vendoremployee['type'] == 'trust'){
                    return response()->json(['token' => $token, 'type' => $vendoremployee['type'],'purohit_id' => $vendoremployee['purohit_id'], 'emp_id' => $vendoremployee['id'], 'id' => $vendoremployee['vendors']['id'], 'status' => $getstatus['status']], 200);
                }
                return response()->json(['token' => $token, 'type' => $vendoremployee['type'] . "_employee",'purohit_id' => $vendoremployee['purohit_id'], 'emp_id' => $vendoremployee['id'], 'id' => $vendoremployee['vendors']['id'], 'status' => $getstatus['status']], 200);
            } elseif ($vendorPurohit) {
                // $passwordCheck = \Illuminate\Support\Facades\Hash::check($request['password'], $vendorPurohit['password']);
                // if (!$passwordCheck) {
                //     return response()->json(['error' => "credentials doesn't match" . '!']);
                // }
                $token = $vendorPurohit->createToken('employee')->accessToken;
                $vendorPurohit->update(['auth_token' => $token]);

                $getstatus =  Seller::where(['relation_id' => $vendorPurohit['temple']['trust_id']])->where('type', 'trust')->first();
                return response()->json(['token' => $token, 'type' => "purohit", 'purohit_id' => $vendorPurohit['id'],'emp_id' =>0, 'id' => $vendorPurohit['temple']['trust_id'], 'status' => ($getstatus['status']??"pending")], 200);
            }
        }
        if (isset($seller) && ($seller['status'] == 'approved' || $seller['status'] == 'hold' || $seller['status'] == 'pending')) {
            // $token = Str::random(50);
            // $token = auth('seller')->user()->createToken('LaravelAuthApp')->accessToken;
            $token = $seller->createToken('LaravelAuthApp')->accessToken;
            Seller::where(['id' => $seller['id']])->update(['auth_token' => $token]);
            if ($seller['type'] == 'seller') {
                if (SellerWallet::where('seller_id', $seller['id'])->first() == false) {
                    DB::table('seller_wallets')->insert([
                        'seller_id' => $seller['id'],
                        'withdrawn' => 0,
                        'commission_given' => 0,
                        'total_earning' => 0,
                        'pending_withdraw' => 0,
                        'delivery_charge_earned' => 0,
                        'collected_cash' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else if ($seller['type'] == 'tour') {
            } else if ($seller['type'] == 'event') {
            } else if ($seller['type'] == 'trust') {
            }
            return response()->json(['token' => $token, 'type' =>$seller['type'],'purohit_id' => 0,'emp_id' =>0, 'id' =>$seller['relation_id'], 'status' =>$seller['status']], 200);
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => translate('Invalid credential or account no verified yet')]);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
    }
}
