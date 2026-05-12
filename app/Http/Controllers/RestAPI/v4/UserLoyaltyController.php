<?php

namespace App\Http\Controllers\RestAPI\v4;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyPointTransaction;
use App\Utils\CustomerManager;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserLoyaltyController extends Controller
{
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $loyalty_point_status = Helpers::get_business_settings('loyalty_point_status');
        if ($loyalty_point_status == 1) {
            $user = $request->user();
            $total_loyalty_point = $user->loyalty_point;

            $loyalty_point_list = LoyaltyPointTransaction::where('user_id', $user->id)
                ->when($request->has('type'), function ($query) use ($request) {
                    $query->when($request->type == 'order_place', function ($query) {
                        $query->where('transaction_type', 'order_place');
                    })->when($request->type == 'point_to_wallet', function ($query) {
                        $query->where('transaction_type', 'point_to_wallet');
                    })->when($request->type == 'refund_order', function ($query) {
                        $query->where(['transaction_type' => 'refund_order']);
                    });
                })
                ->latest()
                ->paginate($request['limit'], ['*'], 'page', $request['offset']);

            return response()->json([
                'limit' => (integer)$request->limit,
                'offset' => (integer)$request->offset,
                'total_loyalty_point' => $total_loyalty_point,
                'total_loyalty_point_count' => $loyalty_point_list->total(),
                'loyalty_point_list' => $loyalty_point_list->items()
            ], 200);
        } else {
            return response()->json(['message' => translate('access_denied!')], 422);
        }
    }

    public function loyalty_exchange_currency(Request $request)
    {
        $wallet_status = Helpers::get_business_settings('wallet_status');
        $loyalty_point_status = Helpers::get_business_settings('loyalty_point_status');

        if ($wallet_status != 1 || $loyalty_point_status != 1) {
            return response()->json([
                'message' => 'Transfer loyalty point to currency is not possible at this moment'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'point' => 'required|integer|min:1'
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $user = $request->user();
        if ($request->point < (int)Helpers::get_business_settings('loyalty_point_minimum_point')
            || $request->point > $user->loyalty_point) {
            return response()->json([
                'message' => 'Insufficient point'
            ], 403);
        }

        $wallet_transaction = CustomerManager::create_wallet_transaction($user->id, $request->point, 'loyalty_point', 'point_to_wallet');
        CustomerManager::create_loyalty_point_transaction($user->id, $wallet_transaction->transaction_id, $request->point, 'point_to_wallet');

        try {
            Mail::to($user['email'])->send(new \App\Mail\AddFundToWallet($wallet_transaction));


        } catch (\Exception $ex) {

        }

        return response()->json([
            'message' => 'Point to wallet transfer successfully'
        ], 200);
    }
}
