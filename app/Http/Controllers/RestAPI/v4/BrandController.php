<?php

namespace App\Http\Controllers\RestAPI\v4;

use App\Http\Controllers\Controller;
use App\Utils\BrandManager;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function get_brands()
    {
        try {
            $brands = BrandManager::get_active_brands();
        } catch (\Exception $e) {
        }

        return response()->json($brands,200);
    }

    public function get_products(Request $request, $brand_id)
    {
        try {
            $products = BrandManager::get_products($brand_id, $request);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }

        return response()->json($products,200);
    }
}
