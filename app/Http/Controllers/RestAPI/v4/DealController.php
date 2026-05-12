<?php

namespace App\Http\Controllers\RestAPI\v4;

use App\Http\Controllers\Controller;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Utils\Helpers;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function get_featured_deal(Request $request)
    {
        $user = Helpers::get_customer($request);
        $featured_deal = FlashDeal::where(['status' => 1])
            ->where(['deal_type' => 'feature_deal'])
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->whereDate('end_date', '>=', date('Y-m-d'))
            ->first();

        $p_ids = array();
        if ($featured_deal) {
            $p_ids = FlashDealProduct::with(['product'])
                ->whereHas('product', function ($q) {
                    $q->active();
                })
                ->where(['flash_deal_id' => $featured_deal->id])
                ->pluck('product_id')->toArray();
        }

        $products = Product::with(['rating','tags'])
            ->withCount(['wishList' => function($query) use($user){
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }])
            ->withCount('reviews')
            ->whereIn('id', $p_ids)
            ->get();

        return response()->json(Helpers::product_data_formatting($products, true), 200);
    }

}
