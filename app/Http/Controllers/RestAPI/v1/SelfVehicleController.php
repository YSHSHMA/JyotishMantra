<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\SelfVehicleLeads;
use App\Models\SelfVehicleOrder;
use App\Models\User;
use App\Traits\FileManagerTrait;
use App\Utils\ImageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Utils\Helpers;
use Illuminate\Support\Facades\DB;


class SelfVehicleController extends Controller
{
    use FileManagerTrait;
    public function __construct(
        private readonly TranslationRepositoryInterface     $translationRepo,
    ) {}

    public function CouponApply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'coupon_code' => ['required', function ($attribute, $value, $fail) {
                if (!Coupon::where('code', $value)->where('coupon_type', 'self_driving')->where('status', 1)->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('expire_date', '>=', date('Y-m-d'))->exists()) {
                    $fail('Invalid Coupon Code.');
                }
            }],
            'lead_id' => ['required', function ($attribute, $value, $fail) {
                if (!SelfVehicleLeads::where('id', $value)->where('status', 0)->exists()) {
                    $fail('The selected Lead id invalid.');
                }
            }],
        ], [
            'user_id.required' => 'User Id is Empty!',
            'coupon_code.required' => 'Coupon Code is Empty!',
        ]);
        if ($validator->fails()) {
            SelfVehicleLeads::where("id", $request['lead_id'])->update(['coupan_id' => 0, 'coupan_amount' => 0]);
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $couponData = Coupon::where('code', $request->get('coupon_code'))->where('coupon_type', 'self_driving')->where('status', 1)->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('expire_date', '>=', date('Y-m-d'))->first();

        $checkCoupon = SelfVehicleOrder::where('coupan_id', ($couponData['id'] ?? ""))->where('status', 1)->where('refund_status', 1)->where('user_id', $request->get('user_id'))->count();
        if (($couponData['limit'] ?? 0) <= $checkCoupon) {
            SelfVehicleLeads::where("id", $request['lead_id'])->update(['coupan_id' => 0, 'coupan_amount' => 0]);
            return response()->json(['status' => 0, 'message' => 'The coupon code has already been used', 'recode' => 0, 'data' => []], 200);
        }
        if ($couponData['customer_id'] != 0 && $couponData['customer_id'] != $request->user_id) {
            SelfVehicleLeads::where("id", $request['lead_id'])->update(['coupan_id' => 0, 'coupan_amount' => 0]);
            return response()->json(['status' => 0, 'message' => 'Invalid Coupon Code', 'recode' => 0, 'data' => []], 200);
        }
       $getDataLeads = SelfVehicleLeads::where("id", $request['lead_id'])->first();
        if (($couponData['min_purchase'] > ($getDataLeads['price'] + $getDataLeads['security_amount'] + $getDataLeads['tax_amount']))) {
            SelfVehicleLeads::where("id", $request['lead_id'])->update(['coupan_id' => 0, 'coupan_amount' => 0]);
            return response()->json(['status' => 0, 'message' => 'Minimum amount Rs ' . ($couponData['min_purchase']) . ' This coupon is applicable', 'recode' => 0, 'data' => []], 200);
        }
        $coupon_amount = 0;
        $final_amount = ($getDataLeads['price'] + $getDataLeads['security_amount'] + $getDataLeads['tax_amount']);
        if ($couponData['discount_type'] == 'amount') {
            $coupon_amount = $couponData['discount'];
            $final_amount = ($final_amount - ($couponData['discount'] ?? 0));
        }
        if ($couponData['discount_type'] == 'percentage') {
            $coupon_amount =  round((($final_amount * ($couponData['discount'] ?? 0)) / 100), 2);
            if ($couponData['max_discount'] < $coupon_amount) {
                $coupon_amount =  $couponData['max_discount'];
            }
            $final_amount =  ($final_amount - $coupon_amount);
        }

        SelfVehicleLeads::where("id", $request['lead_id'])->update(['coupan_id' => $couponData['id'], 'coupan_amount' => $coupon_amount]);
        return response()->json(['status' => 1, 'message' => 'Successfully Coupon Apply', 'recode' => 1, 'data' => ['coupon_id' => $couponData['id'], 'coupon_amount' => $coupon_amount, 'final_amount' => $final_amount]], 200);
    }

    public function SelfVehicleInvoice(Request $request){
         $getData = SelfVehicleOrder::where('id', $request['id'])->with(['userData', 'SelfCabData'])->first();
        if ($getData) {
            $mpdf_view = \Illuminate\Support\Facades\View::make('web-views/self-vehicle/order-invoice', compact('getData'));
            Helpers::gen_mpdf($mpdf_view, 'order_invoice_', str_replace(' ', '_', $getData['order_id']));
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
}
