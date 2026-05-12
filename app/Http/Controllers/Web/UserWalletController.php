<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use App\Utils\Helpers;
use App\Models\AddFundBonusCategories;
use App\Models\User;
use App\Models\UserWithdrawBalance;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use function App\Utils\payment_gateways;

class UserWalletController extends Controller
{

    public function index(Request $request)
    {
        $wallet_status = Helpers::get_business_settings('wallet_status');
        if ($wallet_status == 1) {
            $total_wallet_balance = auth('customer')->user()->wallet_balance;
            $wallet_transactio_list = WalletTransaction::where('user_id', auth('customer')->id())
                ->when($request->has('type'), function ($query) use ($request) {
                    $query->when($request->type == 'order_transactions', function ($query) {
                        $query->where('transaction_type', 'order_place');
                    })->when($request->type == 'converted_from_loyalty_point', function ($query) {
                        $query->where('transaction_type', 'loyalty_point');
                    })->when($request->type == 'added_via_payment_method', function ($query) {
                        $query->where(['transaction_type' => 'add_fund', 'reference' => 'add_funds_to_wallet']);
                    })->when($request->type == 'add_fund_by_admin', function ($query) {
                        $query->where(['transaction_type' => 'add_fund_by_admin']);
                    })->when($request->type == 'order_refund', function ($query) {
                        $query->where(['transaction_type' => 'order_refund']);
                    });
                })->orderBy('id', 'desc')->paginate(10);

            $payment_gateways = payment_gateways();

            $add_fund_bonus_list = AddFundBonusCategories::where('is_active', 1)
                ->whereDate('start_date_time', '<=', date('Y-m-d'))
                ->whereDate('end_date_time', '>=', date('Y-m-d'))
                ->get();

            if ($request->has('flag') && $request->flag == 'success') {
                Toastr::success(translate('add_fund_to_wallet_success'));
                return redirect()->route('wallet');
            } else if ($request->has('flag') && $request->flag == 'fail') {
                Toastr::error(translate('add_fund_to_wallet_unsuccessful'));
                return redirect()->route('wallet');
            }

            return view(VIEW_FILE_NAMES['user_wallet'], compact('total_wallet_balance', 'wallet_transactio_list', 'payment_gateways', 'add_fund_bonus_list'));
        } else {
            Toastr::warning(translate('access_denied!'));
            return redirect()->route('home');
        }
    }

    public function my_wallet_account()
    {
        return view(VIEW_FILE_NAMES['wallet_account']);
    }

    public function get_bank_detail($phone)
    {
        $requestExists = UserWithdrawBalance::where('phone', $phone)->where('status', 0)->exists();
        if ($requestExists) {
            return response()->json(['status' => false, 'message' => 'You have a pending request. Please wait till it verify!'], 200);
        }
        $walletBalance = User::select('wallet_balance')->where('phone', $phone)->first();
        $bankDetail = UserWithdrawBalance::where('phone', $phone)->latest()->first();
        if ($bankDetail || empty($bankDetail)) {
            return response()->json(['status' => true, 'walletBalance' => $walletBalance['wallet_balance'], 'bankDetail' => $bankDetail], 200);
        }
        return response()->json(['status' => false, 'message' => 'bank detail not found'], 400);
    }

    public function store_bank_detail(Request $request)
    {
        $store = new UserWithdrawBalance();
        $store->phone = $request->phone;
        $store->holder = $request->holder;
        $store->bank = $request->bank;
        $store->branch = $request->branch;
        $store->ifsc = $request->ifsc;
        $store->account_no = $request->account_no;
        $store->current_balance = $request->wallet_balance;
        $store->request_balance = $request->request_balance;
        if ($store->save()) {
            Toastr::success(translate('withdraw_amount_requested'));
            return redirect()->back();
        }
        Toastr::error(translate('an_error_occurred'));
        return redirect()->back();
    }
}