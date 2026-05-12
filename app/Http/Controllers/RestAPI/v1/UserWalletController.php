<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\AddFundBonusCategories;
use App\Models\WalletTransaction;
use App\Models\User;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserWalletController extends Controller
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

        $wallet_status = Helpers::get_business_settings('wallet_status');

        if($wallet_status == 1)
        {
            $user = $request->user();
            $total_wallet_balance = $user->wallet_balance;
            $wallet_transactio_list = WalletTransaction::where(['user_id'=>$user->id])
                ->when($request->transaction_type != 'all', function($query) use($request){
                    return $query->where(['transaction_type'=>$request->transaction_type]);
                })
                ->latest()
                ->paginate($request['limit'], ['*'], 'page', $request['offset']);

            return response()->json([
                'limit'=>(integer)$request->limit,
                'offset'=>(integer)$request->offset,
                'total_wallet_balance'=>$total_wallet_balance,
                'total_wallet_transactio'=>$wallet_transactio_list->total(),
                'wallet_transactio_list'=>$wallet_transactio_list->items()
            ],200);

        }else{

            return response()->json(['message' => translate('access_denied!')], 422);
        }
    }

    public function bonus_list(Request $request)
    {
        $add_fund_bonus_categories = AddFundBonusCategories::active()
            ->whereDate('start_date_time', '<=', now())
            ->whereDate('end_date_time', '>=', now())
            ->get();

        return response()->json(['bonus_list' => $add_fund_bonus_categories], 200);
    }

    public function AstroWalletUpdate(Request $request)
    {
        // Validate required fields
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric'
        ]);

        $userId = $request->user_id;
        $amount = $request->amount;
        $transectionID = $request->transection_id;

        // Get user details
        $user = User::select('id', 'wallet_balance', 'phone')->find($userId);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Update wallet balance
        $newBalance = ($user->wallet_balance ?? 0) + $amount;
        $user->update(['wallet_balance' => $newBalance]);

        WalletTransaction::create([
            'user_id' => $userId,
            'transaction_id' => \Str::uuid(),
            'pay_transaction_id' => $transectionID,
            'reference' => 'astro recharge',
            'transaction_type' => 'wallet_credit',
            'balance' => $newBalance,
            'credit' => $amount,
            'debit' => 0,
            'admin_bonus' => 0,
            'payment_method' => 'wallet'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Wallet updated successfully',
            'data' => [
                'user_id' => $userId,
                'phone' => $user->phone,
                'wallet_balance' => $newBalance
            ]
        ]);
    }



}
