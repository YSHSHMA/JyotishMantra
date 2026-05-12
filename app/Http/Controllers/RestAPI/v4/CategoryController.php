<?php

namespace App\Http\Controllers\RestAPI\v4;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Utils\CategoryManager;
use App\Utils\Helpers;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function get_categories()
    {
        try {
            $categories = Category::with(['childes.childes'])->where(['position' => 0])->priority()->get();
            return response()->json($categories, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function get_products(Request $request, $id)
    {
        return response()->json(Helpers::product_data_formatting(CategoryManager::products($id, $request), true), 200);
    }

    public function popular_categories(){
        $categories = Category::withCount(['product' => function($query){
                $query->whereHas('orderDetails', function($query){
                    $query->where('delivery_status', 'delivered');
                })->active();
            }])
            ->orderBy('product_count', 'DESC')
            ->take(9)->get();

        return response()->json($categories, 200);
    }
}
