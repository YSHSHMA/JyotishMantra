<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Utils\Helpers;
use Illuminate\Http\Request;

class FlashDealController extends Controller
{
    public function get_flash_deal()
    {
        try {
            $flash_deals = FlashDeal::where('deal_type','flash_deal')
                ->where(['status' => 1])
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('end_date', '>=', date('Y-m-d'))->first();
            return response()->json($flash_deals, 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }

    }

    public function get_products(Request $request, $deal_id)
    {
        $user = Helpers::get_customer($request);
        $p_ids = FlashDealProduct::with(['product'])
                ->whereHas('product',function($q){
                    $q->active();
                })
                ->where(['flash_deal_id' => $deal_id])
                ->pluck('product_id')->toArray();

        if (count($p_ids) > 0) {
            $products = Product::with(['rating', 'reviews'])
                ->withCount(['wishList' => function($query) use($user){
                    $query->where('customer_id', $user != 'offline' ? $user->id : '0');
                }])
                ->whereIn('id', $p_ids)
                ->get();

            $products?->map(function ($product) {
                $product['reviews_count'] = $product->reviews->count();
                unset($product->reviews);
                return $product;
            });
            return response()->json(Helpers::product_data_formatting($products, true), 200);
        }

        return response()->json([], 200);
    }
}
