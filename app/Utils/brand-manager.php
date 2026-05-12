<?php

namespace App\Utils;

use App\Utils\Helpers;
use App\Models\Brand;
use App\Models\Product;

class BrandManager
{
    public static function get_brands()
    {
        return Brand::withCount('brandProducts')->latest()->get();
    }

    public static function get_products($brand_id, $request=null)
    {
        $user = Helpers::get_customer($request);

        $products = Product::active()
            ->withCount(['reviews','wishList' => function($query) use($user){
                $query->where('customer_id', $user != 'offline' ? $user->id : '0');
            }])
            ->where(['brand_id' => $brand_id])
            ->get();

        return Helpers::product_data_formatting($products, true);
    }

    public static function get_active_brands(){
        return Brand::active()->withCount('brandProducts')->latest()->get();
    }
}
