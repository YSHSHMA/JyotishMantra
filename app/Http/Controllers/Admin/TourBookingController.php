<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\SettingRepositoryInterface;
use App\Contracts\Repositories\TourAndTravelRepositoryInterface;
use App\Contracts\Repositories\TourOrderRepositoryInterface;
use App\Enums\ViewPaths\Admin\TourBookingPath;
use App\Http\Controllers\Controller;
use App\Models\TourCancelTicket;
use App\Models\TourOrder;
use App\Models\User;
use App\Models\WalletTransaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class TourBookingController extends Controller
{
    public function __construct(
        private readonly TourOrderRepositoryInterface  $tourorder,
        private readonly TourAndTravelRepositoryInterface  $tourandtravel,
        private readonly SettingRepositoryInterface         $settingRepo,
    ) {}

    public function BookingList(Request $request)
    {
        $getData = $this->tourorder->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), relations: ['userData', 'company', 'Tour'], filters: ['amount_status' => 1], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourBookingPath::ALL[VIEW], compact('getData'));
    }

    public function BookingPending(Request $request)
    {
        $getData = $this->tourorder->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), relations: ['userData', 'company', 'Tour'], filters: ['amount_status' => 1, 'status' => [1, 0],'cab_assign_not'=>1], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourBookingPath::PENDING[VIEW], compact('getData'));
    }
    public function BookingConfirm(Request $request)
    {
        $filters = [];
        $filters['amount_status'] = 1;
        $filters['status'] = 1;
        $filters['drop_status'] = 0;
        $filters['refund_status'] = 0;
        $filters['cab_assign'] = 1;
        if (!empty($request['pickup_status']) && ($request['pickup_status'] == 'one' || $request['pickup_status'] == 'two')) {
            $filters['pickup_status'] = (($request['pickup_status'] == 'one') ? 0 : 1);
        }
        $getData = $this->tourorder->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), relations: ['userData', 'company', 'Tour'], filters: $filters, dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourBookingPath::CONFIRM[VIEW], compact('getData'));
    }
    public function BookingCompleted(Request $request)
    {
        $getData = $this->tourorder->getListWhere(orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), relations: ['userData', 'company', 'Tour'], filters: ['amount_status' => 1, 'status' => 1, 'pickup_status' => 1, 'drop_status' => 1], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourBookingPath::COMPLETED[VIEW], compact('getData'));
    }
    public function BookingCancel(Request $request)
    {
        $getData = $this->tourorder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('type') == 'pending') ? $request->get('searchValue') : ''), relations: ['userData', 'company', 'Tour'], filters: ['amount_status' => 1, 'status' => 2, 'refund_status' => 3], dataLimit: getWebConfig(name: 'pagination_limit'));
        $refund_approve = $this->tourorder->getListWhere(orderBy: ['id' => 'desc'], searchValue: (($request->get('type') == 'approval') ? $request->get('searchValue') : ''), relations: ['userData', 'company', 'Tour'], filters: ['amount_status' => 1, 'status' => 2, 'refund_status' => 1], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(TourBookingPath::CANCEL[VIEW], compact('getData', 'refund_approve'));
    }

    public function BookingDetails(Request $request, $id)
    {
        $getData = $this->tourorder->getFirstWhere(params: ['id' => $id], relations: ['userData', 'company', 'Tour']);
        $company_list = $this->tourandtravel->getListWhere(filters: ['status' => 1, 'is_approve' => 1]);
        $order = [];
        return view(TourBookingPath::DETAILS[VIEW], compact('getData', 'order', 'company_list'));
    }

    public function AssignedCab(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'cab_id' => 'required',
        ]);
        $this->tourorder->update(id: $request->id, data: ['status' => 1, 'pickup_status' => 0, 'drop_status' => 0, 'traveller_id' => $request->cab_id, 'cab_assign' => $request->cab_id]);
        Toastr::success('Assign Cab Successfully');
        return back();
    }

    public function BookingDateUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'date' => 'required',
            'time' => 'required',
        ]);
        $this->tourorder->update(id: $request->id, data: ['pickup_date' => $request->date, 'pickup_time' => $request->time]);
        Toastr::success('Booking Date Update Successfully');
        return back();
    }

    public function BookingRefund(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'payment_method' => 'required',
            'transaction_id' => 'required',
            'amount' => 'required',
            'refund_amount' => 'required',
            'type' => 'required',
        ]);

        if (!empty($request->refund_id)) {
            Toastr::error('Aleady Refund Successfully');
            return back();
        }
        if ($request->type == 'approve') {
            $orderData = TourOrder::where('id', $request->id)->first();
            TourOrder::where('id', $request->id)->update(['refound_id' => 'wallet', 'refund_status' => 1, 'status' => 2, 'refund_amount' => $request->refund_amount, 'refund_date' => date('Y-m-d H:i:s')]);
            TourCancelTicket::where('id', $orderData['refund_query_id'] ?? '')->update(['status' => 1]);
            User::where('id', $orderData['user_id'])->update(['wallet_balance' => \Illuminate\Support\Facades\DB::raw('wallet_balance + ' . $request->refund_amount)]);
            $wallet_transaction = new WalletTransaction();
            $wallet_transaction->user_id = $orderData['user_id'];
            $wallet_transaction->transaction_id = \Illuminate\Support\Str::uuid();
            $wallet_transaction->reference = 'Tour Refund';
            $wallet_transaction->transaction_type = 'tour_refund';
            $wallet_transaction->balance = User::where('id', $orderData['user_id'])->first()['wallet_balance'];
            $wallet_transaction->credit = $request->refund_amount;
            $wallet_transaction->save();
        } else {
            $orderData = TourOrder::where('id', $request->id)->first();
            TourOrder::where('id', $request->id)->update(['refund_status' => 2, 'status' => 2, 'refund_date' => date('Y-m-d H:i:s')]);
            TourCancelTicket::where('id', $orderData['refund_query_id'] ?? '')->update(['status' => 2]);
        }
        return back();
    }

    public function WithdrawalList()
    {
        $withdrawRequests = \App\Models\WithdrawalAmountHistory::where(['type' => "tour"])->with(['Tour', 'TourVisit'])->orderBy('id', 'desc')->paginate(10, ['*'], 'page');
        return view("admin-views.tour_and_travels.withdrawal.index", compact('withdrawRequests'));
    }

    public function WithdrawalReqView(Request $request)
    {
        $withdrawRequests = \App\Models\WithdrawalAmountHistory::where(['type' => "tour"])->with(['Tour', 'TourVisit'])->where('id', $request['id'])->first();
        return view('admin-views.tour_and_travels.withdrawal.view', compact('withdrawRequests'));
    }

    public function WithdrawalReqReject(Request $request)
    {
        $withdrawRequests = \App\Models\WithdrawalAmountHistory::where(['type' => "tour"])->with(['Tour', 'TourVisit'])->where('id', $request['id'])->first();
        if ($withdrawRequests) {
            if ($withdrawRequests['ex_id'] == 0) {
                \App\Models\TourAndTravel::where('id', $withdrawRequests['vendor_id'])->update(['withdrawal_pending_amount' => 0]);
             } else {
                \App\Models\TourOrder::where('id', $withdrawRequests['ex_id'])->update(['advance_withdrawal_amount' => 0]);
            }
            \App\Models\WithdrawalAmountHistory::where('id', $request['id'])->update(['status' => 2]);
            Toastr::success('pay_Request_Reject Successfully');
            return back();
        }
        Toastr::success('pay_Request_Reject Failed');
        return back();
    }

    public function RazorpaycreateContact($id, $type)
    {
        $get_Razorpay = $this->settingRepo->getFirstWhere(params: ['key_name' => 'razor_pay']);
        $RAZORPAY_KEY_ID = '';
        $RAZORPAY_KEY_SECRET = '';
        $RAZORPAY_ACCOUNT_NUMBER = '';
        if ($get_Razorpay['mode'] == 'live') {
            $RAZORPAY_KEY_ID = $get_Razorpay['live_values']['api_key'];
            $RAZORPAY_KEY_SECRET = $get_Razorpay['live_values']['api_secret'];
            $RAZORPAY_ACCOUNT_NUMBER = $get_Razorpay['live_values']['account_number'] ?? '';
        } else {
            $RAZORPAY_KEY_ID = $get_Razorpay['live_values']['api_key'];
            $RAZORPAY_KEY_SECRET = $get_Razorpay['live_values']['api_secret'];
            $RAZORPAY_ACCOUNT_NUMBER = $get_Razorpay['live_values']['account_number'] ?? '';
        }
        $api = new Api($RAZORPAY_KEY_ID, $RAZORPAY_KEY_SECRET);
        $getWithdrawal_recode = \App\Models\WithdrawalAmountHistory::where(['type' => "tour"])->with(['Tour', 'TourVisit'])->where('id', $id)->first();
        $email = $getWithdrawal_recode['Tour']['person_email'];
        $contact = $getWithdrawal_recode['Tour']['person_phone'];
        $url = "https://api.razorpay.com/v1/contacts";
        $data = [
            "name" => $getWithdrawal_recode['Tour']['person_name'],
            "email" => $email,
            "contact" => $contact,
            "type" => "vendor"
        ];
        $headers = [
            "Content-Type: application/json",
            "Authorization: Basic " . base64_encode("$RAZORPAY_KEY_ID:$RAZORPAY_KEY_SECRET")
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode == 200 || $httpCode == 201) {
            $contact_data = json_decode($response, true);
        } else {
            if ($type != 'manual') {
                return ["error" => "Failed to create contact", "response" => json_decode($response, true)];
            }
        }
        if ($type == 'bank') {
            $fundAccount = $api->fundAccount->create([
                "account_type" => "bank_account",
                "contact_id" => $contact_data['id'],
                "bank_account" => [
                    "name" => $getWithdrawal_recode['holder_name'],
                    "ifsc" => $getWithdrawal_recode['ifsc_code'],
                    "account_number" => $getWithdrawal_recode['account_number']
                ]
            ]);
        } elseif ($type == 'manual') {
            if ($getWithdrawal_recode['ex_id'] == 0) {
                \App\Models\TourAndTravel::where('id', $getWithdrawal_recode['vendor_id'])->update(['withdrawal_pending_amount' => 0, 'withdrawal_amount' => \Illuminate\Support\Facades\DB::raw('withdrawal_amount + ' . $getWithdrawal_recode['req_amount']),"wallet_amount"=> \Illuminate\Support\Facades\DB::raw('wallet_amount - ' . $getWithdrawal_recode['req_amount'])]);
            } else {
                \App\Models\TourOrder::where('id', $getWithdrawal_recode['ex_id'])->update(['advance_withdrawal_amount' => $getWithdrawal_recode['req_amount']]);
            }
            \App\Models\WithdrawalAmountHistory::where('id', $id)->update([
                'status' => 1,
                'transcation_id' => $request['transcation_id'] ?? '',
                'approval_amount' => $getWithdrawal_recode['req_amount'],
                'payment_method' => 'manual'
            ]);
            Toastr::success('Payment transferred successfully');
            return back();
        } else {
            $fundAccount = $api->fundAccount->create([
                "account_type" => "vpa", // Use "vpa" for UPI instead of "bank_account"
                "contact_id" => $contact_data['id'],
                "vpa" => [
                    "address" => $getWithdrawal_recode['upi_code'] // Replace with a valid UPI ID
                ]
            ]);
        }

        $fund_account_id = $fundAccount['id'];
        $data_fund_tans = [
            'account_number' => $RAZORPAY_ACCOUNT_NUMBER,
            'fund_account_id' => $fund_account_id,
            'amount' => 100,
            'currency' => 'INR',
            'mode' => (($type == 'upi') ? 'UPI' : 'IMPS'), // Can be NEFT, IMPS, UPI
            'purpose' => 'payout',
            'queue_if_low_balance' => true,
            'reference_id' => 'Payout123',
            'narration' => 'Payment for service',
            "notes" => [
                "notes_key_1" => "Tea, Earl Grey, Hot",
                "notes_key_2" => "Tea, Earl Grey… decaf."
            ]
        ];

        $headers_fund_tans = [
            "Content-Type: application/json",
            "Authorization: Basic " . base64_encode("$RAZORPAY_KEY_ID:$RAZORPAY_KEY_SECRET")
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/payouts");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_fund_tans));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_fund_tans);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
        if ($httpCode == 200 || $httpCode == 201) {
            // return  json_decode($response, true);
            if ($getWithdrawal_recode['ex_id'] == 0) {
                \App\Models\TourAndTravel::where('id', $getWithdrawal_recode['vendor_id'])->update(['withdrawal_pending_amount' => 0, 'withdrawal_amount' => \Illuminate\Support\Facades\DB::raw('withdrawal_amount + ' . $getWithdrawal_recode['req_amount']),"wallet_amount"=> \Illuminate\Support\Facades\DB::raw('wallet_amount - ' . $getWithdrawal_recode['req_amount'])]);
            } else {
                \App\Models\TourOrder::where('id', $getWithdrawal_recode['ex_id'])->update(['advance_withdrawal_amount' => $getWithdrawal_recode['req_amount']]);
            }
            \App\Models\WithdrawalAmountHistory::where('id', $id)->update(['status' => 1]);
            Toastr::success('Payment transferred successfully');
            return back();
        } else {
            if ($getWithdrawal_recode['ex_id'] == 0) {
                \App\Models\TourAndTravel::where('id', $getWithdrawal_recode['vendor_id'])->update(['withdrawal_pending_amount' => 0]);
            } else {
                \App\Models\TourOrder::where('id', $getWithdrawal_recode['ex_id'])->update(['advance_withdrawal_amount' => 0]);
            }
            \App\Models\WithdrawalAmountHistory::where('id', $id)->update(['status' => 2]);
            Toastr::error('Failed to payouts');
            return back();
        }
    }
}
