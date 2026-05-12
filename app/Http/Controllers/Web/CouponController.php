<?php

namespace App\Http\Controllers\Web;

use App\Utils\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Chadhava_orders;
use App\Models\Coupon;
use App\Models\Leads;
use App\Models\OfflineLead;
use App\Models\OfflinePoojaOrder;
use App\Models\ProductLeads;
use App\Models\Service;
use App\Models\Service_order;
use App\Models\Order;
use App\Utils\CartManager;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $couponLimit = Order::where(['customer_id' => auth('customer')->id(), 'coupon_code' => $request['code']])
            ->groupBy('order_group_id')->get()->count();

        $coupon_f = Coupon::where(['code' => $request['code']])
            ->where('status', 1)
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('expire_date', '>=', date('Y-m-d'))->first();

        if (!$coupon_f) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'messages' => ['0' => translate('invalid_coupon')]
                ]);
            }
            Toastr::error(translate('invalid_coupon'));
            return back();
        }
        if ($coupon_f && $coupon_f->coupon_type == 'first_order') {
            $coupon = $coupon_f;
        } else {
            $coupon = $coupon_f->limit > $couponLimit ? $coupon_f : null;
        }

        if ($coupon && $coupon->coupon_type == 'first_order') {
            $orders = Order::where(['customer_id' => auth('customer')->id()])->count();
            if ($orders > 0) {
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 0,
                        'messages' => ['0' => translate('sorry_this_coupon_is_not_valid_for_this_user') . '!']
                    ]);
                }
                Toastr::error(translate('sorry_this_coupon_is_not_valid_for_this_user'));
                return back();
            }
        }

        if ($coupon && (($coupon->coupon_type == 'first_order') || ($coupon->coupon_type == 'discount_on_purchase' && ($coupon->customer_id == '0' || $coupon->customer_id == auth('customer')->id())))) {
            $total = 0;
            foreach (CartManager::get_cart() as $cart) {
                if ($coupon->seller_id == '0' || (is_null($coupon->seller_id) && $cart->seller_is == 'admin') || ($coupon->seller_id == $cart->seller_id && $cart->seller_is == 'seller')) {
                    $product_subtotal = $cart['price'] * $cart['quantity'];
                    $total += $product_subtotal;
                }
            }
            if ($total >= $coupon['min_purchase']) {
                if ($coupon['discount_type'] == 'percentage') {
                    $discount = (($total / 100) * $coupon['discount']) > $coupon['max_discount'] ? $coupon['max_discount'] : (($total / 100) * $coupon['discount']);
                } else {
                    $discount = $coupon['discount'];
                }

                session()->put('coupon_code', $request['code']);
                session()->put('coupon_type', $coupon->coupon_type);
                session()->put('coupon_discount', $discount);
                session()->put('coupon_bearer', $coupon->coupon_bearer);
                session()->put('coupon_seller_id', $coupon->seller_id);

                return response()->json([
                    'status' => 1,
                    'discount' => Helpers::currency_converter($discount),
                    'total' => Helpers::currency_converter($total - $discount),
                    'messages' => ['0' => translate('coupon_applied_successfully') . '!']
                ]);
            }
        } elseif ($coupon && $coupon->coupon_type == 'free_delivery' && ($coupon->customer_id == '0' || $coupon->customer_id == auth('customer')->id())) {
            $total = 0;
            $shipping_fee = 0;
            foreach (CartManager::get_cart() as $cart) {
                if ($coupon->seller_id == '0' || (is_null($coupon->seller_id) && $cart->seller_is == 'admin') || ($coupon->seller_id == $cart->seller_id && $cart->seller_is == 'seller')) {
                    $product_subtotal = $cart['price'] * $cart['quantity'];
                    $total += $product_subtotal;
                    if (is_null($coupon->seller_id) || $coupon->seller_id == '0' || $coupon->seller_id == $cart->seller_id) {
                        $shipping_fee += $cart['shipping_cost'];
                    }
                }
            }

            if ($total >= $coupon['min_purchase']) {
                session()->put('coupon_code', $request['code']);
                session()->put('coupon_type', $coupon->coupon_type);
                session()->put('coupon_discount', $shipping_fee);
                session()->put('coupon_bearer', $coupon->coupon_bearer);
                session()->put('coupon_seller_id', $coupon->seller_id);

                return response()->json([
                    'status' => 1,
                    'discount' => Helpers::currency_converter($shipping_fee),
                    'total' => Helpers::currency_converter($total - $shipping_fee),
                    'messages' => ['0' => translate('coupon_applied_successfully') . '!']
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 0,
                'messages' => ['0' => translate('invalid_coupon')]
            ]);
        }
        Toastr::error(translate('invalid_coupon'));
        return back();
    }

    public function removeCoupon(Request $request): JsonResponse|RedirectResponse
    {
        session()->forget('coupon_code');
        session()->forget('coupon_type');
        session()->forget('coupon_bearer');
        session()->forget('coupon_discount');
        session()->forget('coupon_seller_id');

        if ($request->ajax()) {
            return response()->json(['messages' => translate('coupon_removed')]);
        }
        Toastr::success(translate('coupon_removed'));
        return back();
    }

    // -------------------------------------------------------------POOJA COUPON CODE APPLY------------------------------------------------------------------------------------------------
    public function couponapply(Request $request)
    {

        $couponLimit = Service_order::where(['customer_id' => auth('customer')->id(), 'coupon_code' => $request['code']])->whereHas('offlinepoojaorders')->groupBy('order_id')->get()->count();
        // dd($couponLimit);
        $coupon_f = Coupon::where(['code' => $request['code']])->where('status', 1)->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('expire_date', '>=', date('Y-m-d'))->first();
        // dd($coupon_f);
        if (!$coupon_f) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'messages' => ['0' => translate('invalid_coupons')]
                ]);
            }
            Toastr::error(translate('invalid_coupon'));
            return back();
        }
        if (
            $coupon_f && $coupon_f->coupon_type == 'first_order' || $coupon_f->coupon_type == 'pooja' || $coupon_f->coupon_type == 'vippooja'
            || $coupon_f->coupon_type == 'anushthan' || $coupon_f->coupon_type == 'offlinepooja' || $coupon_f->coupon_type == 'counselling'
        ) {
            $coupon = $coupon_f;
        } else {
            $coupon = $coupon_f->limit > $couponLimit ? $coupon_f : null;
        }
        // dd($coupon);
        if ($coupon && $coupon->coupon_type == 'first_order') {
            $orders = Service_order::where(['customer_id' => auth('customer')->id()])->count();
            if ($orders > 0) {
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 0,
                        'messages' => ['0' => translate('sorry_this_coupon_is_not_valid_for_this_user') . '!']
                    ]);
                }
                Toastr::error(translate('sorry_this_coupon_is_not_valid_for_this_user'));
                return back();
            }
        }
        if ($coupon && (
            ($coupon->coupon_type == 'pooja') ||
            ($coupon->coupon_type == 'vippooja') ||
            ($coupon->coupon_type == 'anushthan') ||
            ($coupon->coupon_type == 'offlinepooja') ||
            ($coupon->coupon_type == 'counselling')
        ) && ($coupon->customer_id == '0' || $coupon->customer_id == auth('customer')->id())) {
            // dd('sdfsd');    
            if ($coupon->coupon_type == 'counselling') {
                $counsellingPrice = Service::where('id', $request['service_id'])->first();
                $total = $counsellingPrice['counselling_selling_price'];
            } elseif ($coupon->coupon_type == 'vippooja') {
                $VipPrice = Leads::where('id', $request['leads_id'])
                    ->where('status', 1)->where('type', 'vip')->first();
                // $productLead = ProductLeads::where('leads_id', $request['leads_id'])->where('status', 1)->sum('final_price');
                $total = $VipPrice['package_price'];
            } elseif ($coupon->coupon_type == 'anushthan') {
                $anushthanPrice = Leads::where('id', $request['leads_id'])
                    ->where('status', 1)->where('type', 'anushthan')->first();
                // $productLead = ProductLeads::where('leads_id', $request['leads_id'])->where('status', 1)->sum('final_price');
                // dd($coupon->coupon_type);
                $total = $anushthanPrice['package_price'];
            } elseif ($coupon->coupon_type == 'pooja') {
                // dd('sdfsd');    
                $poojaPrice = Leads::where('id', $request['leads_id'])
                    ->where('status', 1)->where('type', 'pooja')->first();
                // $productLead = ProductLeads::where('leads_id', $request['leads_id'])->where('status', 1)->sum('final_price');
                $total = $poojaPrice['package_price'];
                // dd($total);     
            } elseif ($coupon->coupon_type == 'offlinepooja') {
                $total = ProductLeads::where('leads_id', $request['leads_id'])->groupBy('leads_id')->sum('final_price');
            } else {
                $total = ProductLeads::where('leads_id', $request['leads_id'])
                    ->where('status', 1)->with('lead')->get()->sum(function ($productLead) {
                        return $productLead->final_price + $productLead->lead->package_price;
                    });
            }

            if ($coupon) {
                if ($total >= $coupon['min_purchase']) {
                    if ($coupon['discount_type'] == 'percentage') {
                        $discount = (($total / 100) * $coupon['discount']) > $coupon['max_discount'] ? $coupon['max_discount'] : (($total / 100) * $coupon['discount']);
                    } else {
                        $discount = $coupon['discount'];
                    }
                    if ($coupon->coupon_type == 'pooja') {
                        session()->put('coupon_code_pooja', $request['code']);
                        session()->put('coupon_type_pooja', $coupon->coupon_type);
                        session()->put('coupon_discount_pooja', $discount);
                        session()->put('coupon_bearer_pooja', $coupon->coupon_bearer);
                        session()->put('coupon_seller_id_pooja', $coupon->seller_id);
                    } elseif ($coupon->coupon_type == 'vippooja') {
                        session()->put('coupon_code_vippooja', $request['code']);
                        session()->put('coupon_type_vippooja', $coupon->coupon_type);
                        session()->put('coupon_discount_vippooja', $discount);
                        session()->put('coupon_bearer_vippooja', $coupon->coupon_bearer);
                        session()->put('coupon_seller_id_vippooja', $coupon->seller_id);
                    } elseif ($coupon->coupon_type == 'anushthan') {
                        session()->put('coupon_code_anushthan', $request['code']);
                        session()->put('coupon_type_anushthan', $coupon->coupon_type);
                        session()->put('coupon_discount_anushthan', $discount);
                        session()->put('coupon_bearer_anushthan', $coupon->coupon_bearer);
                        session()->put('coupon_seller_id_anushthan', $coupon->seller_id);
                    } elseif ($coupon->coupon_type == 'offlinepooja') {
                        session()->put('coupon_code_offlinepooja', $request['code']);
                        session()->put('coupon_type_offlinepooja', $coupon->coupon_type);
                        session()->put('coupon_discount_offlinepooja', $discount);
                        session()->put('coupon_bearer_offlinepooja', $coupon->coupon_bearer);
                        session()->put('coupon_seller_id_offlinepooja', $coupon->seller_id);
                    } elseif ($coupon->coupon_type == 'counselling') {
                        session()->put('coupon_code_counselling', $request['code']);
                        session()->put('coupon_type_counselling', $coupon->coupon_type);
                        session()->put('coupon_discount_counselling', $discount);
                        session()->put('coupon_bearer_counselling', $coupon->coupon_bearer);
                        session()->put('coupon_seller_id_counselling', $coupon->seller_id);
                    }
                    return response()->json([
                        'status' => 1,
                        'discount' => Helpers::currency_converter($discount),
                        'total' => Helpers::currency_converter($total - $discount),
                        'messages' => ['0' => translate('coupon_applied_successfully') . '!']
                    ]);
                } else {

                    return response()->json([
                        'status' => 0,
                        'messages' => ['0' =>  translate('Invalid coupon code.')],
                    ]);
                }
            } else {

                return response()->json([
                    'status' => 0,
                    'messages' => ['0' => translate('Invalid coupon code.')],
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 0,
                'messages' => ['0' => translate('invalid_coupon')]
            ]);
        }
        Toastr::error(translate('invalid_coupon'));
        return back();
    }

    public function removeCouponPooja(Request $request): JsonResponse|RedirectResponse
    {
        $coupon = Coupon::where('code', $request['code'])->where('status', 1)->first(['coupon_type']);
        // dd($coupon);
        if ($coupon->coupon_type == 'pooja') {
            session()->forget('coupon_code_pooja');
            session()->forget('coupon_type_pooja');
            session()->forget('coupon_discount_pooja');
            session()->forget('coupon_bearer_pooja');
            session()->forget('coupon_seller_id_pooja');
        } elseif ($coupon->coupon_type == 'vippooja') {
            session()->forget('coupon_code_vippooja');
            session()->forget('coupon_type_vippooja');
            session()->forget('coupon_discount_vippooja');
            session()->forget('coupon_bearer_vippooja');
            session()->forget('coupon_seller_id_vippooja');
        } elseif ($coupon->coupon_type == 'anushthan') {
            session()->forget('coupon_code_anushthan');
            session()->forget('coupon_type_anushthan');
            session()->forget('coupon_discount_anushthan');
            session()->forget('coupon_bearer_anushthan');
            session()->forget('coupon_seller_id_anushthan');
        } elseif ($coupon->coupon_type == 'offlinepooja') {
            session()->forget('coupon_code_offlinepooja');
            session()->forget('coupon_type_offlinepooja');
            session()->forget('coupon_discount_offlinepooja');
            session()->forget('coupon_bearer_offlinepooja');
            session()->forget('coupon_seller_id_offlinepooja');
        } elseif ($coupon->coupon_type == 'counselling') {
            session()->forget('coupon_code_counselling');
            session()->forget('coupon_type_counselling');
            session()->forget('coupon_discount_counselling');
            session()->forget('coupon_bearer_counselling');
            session()->forget('coupon_seller_id_counselling');
        }
        if ($request->ajax()) {
            return response()->json(['messages' => translate('coupon_removed')]);
        }
        Toastr::success(translate('coupon_removed'));
        return back();
    }
    // -------------------------------------------------------------OFFLINE POOJA COUPON CODE APPLY------------------------------------------------------------------------------------------------
    public function offlinepoojacouponapply(Request $request)
    {
        $couponLimit = OfflinePoojaOrder::where(['customer_id' => auth('customer')->id(), 'coupon_code' => $request['code']])->groupBy('order_id')->get()->count();
        // dd($couponLimit);
        $coupon_f = Coupon::where(['code' => $request['code']])->where('status', 1)->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('expire_date', '>=', date('Y-m-d'))->first();
        // dd($coupon_f);
        if (!$coupon_f) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'messages' => ['0' => translate('invalid_coupons')]
                ]);
            }
            Toastr::error(translate('invalid_coupon'));
            return back();
        }
        if (
            $coupon_f && $coupon_f->coupon_type == 'first_order' || $coupon_f->coupon_type == 'offlinepooja'
        ) {
            $coupon = $coupon_f;
        } else {
            $coupon = $coupon_f->limit > $couponLimit ? $coupon_f : null;
        }
        // dd($coupon);
        if ($coupon && $coupon->coupon_type == 'first_order') {
            $orders = OfflinePoojaOrder::where(['customer_id' => auth('customer')->id()])->count();
            if ($orders > 0) {
                if ($request->ajax()) {
                    return response()->json([
                        'status' => 0,
                        'messages' => ['0' => translate('sorry_this_coupon_is_not_valid_for_this_user') . '!']
                    ]);
                }
                Toastr::error(translate('sorry_this_coupon_is_not_valid_for_this_user'));
                return back();
            }
        }
        if ($coupon && (
            ($coupon->coupon_type == 'offlinepooja')
        ) && ($coupon->customer_id == '0' || $coupon->customer_id == auth('customer')->id())) {
            // dd('sdfsd');    
            if ($coupon->coupon_type == 'offlinepooja') {
                $total = OfflineLead::where('id', $request['leads_id'])->value('package_price');
            }

            if ($coupon) {
                if ($total >= $coupon['min_purchase']) {
                    if ($coupon['discount_type'] == 'percentage') {
                        $discount = (($total / 100) * $coupon['discount']) > $coupon['max_discount'] ? $coupon['max_discount'] : (($total / 100) * $coupon['discount']);
                    } else {
                        $discount = $coupon['discount'];
                    }
                    if ($coupon->coupon_type == 'offlinepooja') {
                        session()->put('coupon_code_offlinepooja', $request['code']);
                        session()->put('coupon_type_offlinepooja', $coupon->coupon_type);
                        session()->put('coupon_discount_offlinepooja', $discount);
                        session()->put('coupon_bearer_offlinepooja', $coupon->coupon_bearer);
                        session()->put('coupon_seller_id_offlinepooja', $coupon->seller_id);
                    }
                    return response()->json([
                        'status' => 1,
                        'discount' => Helpers::currency_converter($discount),
                        'total' => Helpers::currency_converter($total - $discount),
                        'messages' => ['0' => translate('coupon_applied_successfully') . '!']
                    ]);
                } else {

                    return response()->json([
                        'status' => 0,
                        'messages' => ['0' =>  translate('Invalid coupon code.')],
                    ]);
                }
            } else {

                return response()->json([
                    'status' => 0,
                    'messages' => ['0' => translate('Invalid coupon code.')],
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 0,
                'messages' => ['0' => translate('invalid_coupon')]
            ]);
        }
        Toastr::error(translate('invalid_coupon'));
        return back();
    }

    public function offlinepoojaremoveCouponPooja(Request $request): JsonResponse|RedirectResponse
    {
        // $coupon = Coupon::where('code', $request['code'])->where('status', 1)->first(['coupon_type']);
        // dd($request->all());
        // if ($coupon->coupon_type == 'offlinepooja') {
        session()->forget('coupon_code_offlinepooja');
        session()->forget('coupon_type_offlinepooja');
        session()->forget('coupon_discount_offlinepooja');
        session()->forget('coupon_bearer_offlinepooja');
        session()->forget('coupon_seller_id_offlinepooja');
        // }
        if ($request->ajax()) {
            return response()->json(['messages' => translate('coupon_removed')]);
        }
        Toastr::success(translate('coupon_removed'));
        return back();
    }

    public function couponListType(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'type' => 'required|in:event,tour,self-driving',
        ], [
            'type.required' => 'type is provide!',
        ]);
        if ($validator->fails()) {
            return response()->json(["status" => 0, "message" => "", 'errors' => Helpers::error_processor($validator), 'data' => []], 403);
        }

        if ($request->type == 'event' && !empty(auth('customer')->id())) {
            $userId = auth('customer')->id();
            $coupons = Coupon::where('status', 1)
                ->where('coupon_type', 'event_order')
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('expire_date', '>=', date('Y-m-d'))
                ->where(function ($query) use ($userId) {
                    $query->where('customer_id', 0)
                        ->orWhere('customer_id', $userId);
                })
                ->whereRaw('(SELECT COUNT(*) FROM `event_orders` WHERE `event_orders`.`coupon_id` = `coupons`.`id` AND `event_orders`.`user_id` = ?) < `coupons`.`limit`', [$userId])
                ->get();
        } elseif ($request->type == 'self-driving' && !empty(auth('customer')->id())) {
            $userId = auth('customer')->id();
            $coupons = Coupon::where('status', 1)
                ->where('coupon_type', 'self_driving')
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('expire_date', '>=', date('Y-m-d'))
                ->where(function ($query) use ($userId) {
                    $query->where('customer_id', 0)
                        ->orWhere('customer_id', $userId);
                })
                ->whereRaw('(SELECT COUNT(*) FROM `self_vehicle_orders` WHERE `self_vehicle_orders`.`coupan_id` = `coupons`.`id` AND `self_vehicle_orders`.`user_id` = ?) < `coupons`.`limit`', [$userId])
                ->get();
        } else if ($request->type == 'tour' && (!empty(auth('customer')->id()) || !empty($request['user_id']))) {
            $userId = $request['user_id']??"";
            if(empty($userId)){
                $userId = auth('customer')->id();
            }
            $coupons = Coupon::where('status', 1)
                ->where('coupon_type', 'tour_order')
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('expire_date', '>=', date('Y-m-d'))
                ->where(function ($query) use ($userId) {
                    $query->where('customer_id', 0)
                        ->orWhere('customer_id', $userId)->orWhere('customer_id', '');
                })
                // ->whereRaw('`limit` > (SELECT COUNT(*) FROM `tour_order` WHERE `tour_order`.`coupon_id` = `coupons`.`id`)')
                ->whereRaw('(SELECT COUNT(*) FROM `tour_order` WHERE `tour_order`.`coupon_id` = `coupons`.`id` AND `tour_order`.`user_id` = ?) < `coupons`.`limit`', [$userId])
                // ->whereNotExists(function ($query) use ($userId) {
                //     $query->from('tour_order')
                //         ->whereColumn('tour_order.coupon_id', 'coupons.id')
                //         ->where('tour_order.user_id', $userId)
                //         ->where('tour_order.amount_status', 1);
                // })
                ->get();
        }
        if ($coupons && count($coupons) > 0) {
            return response()->json(['status' => 200, 'coupons' => $coupons, 'usre' => $userId]);
        } else {
            return response()->json(['status' => 400, 'coupons' => []]);
        }
    }
}
