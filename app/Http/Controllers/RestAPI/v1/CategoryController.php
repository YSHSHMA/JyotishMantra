<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Utils\CategoryManager;
use App\Utils\Helpers;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function get_categories(Request $request)
    {
        $categories_id = [];
        if ($request->has('seller_id') && !empty($request->seller_id)) {
            // Finding category ids
            $categories_id = Product::active()
                ->when($request->has('seller_id') && !empty($request->seller_id), function ($query) use ($request) {
                    return $query->where(['added_by' => 'seller'])
                        ->where('user_id', $request->seller_id);
                })->pluck('category_id');
        }

        $categories = Category::when($request->has('seller_id') && !empty($request->seller_id), function ($query) use ($categories_id) {
            $query->whereIn('id', $categories_id);
        })
            ->withCount(['product' => function ($query) use ($request) {
                $query->when($request->has('seller_id') && !empty($request->seller_id), function ($query) use ($request) {
                    $query->where(['added_by' => 'seller', 'user_id' => $request->seller_id, 'status' => '1']);
                });
            }])->with(['childes' => function ($query) {
                $query->with(['childes' => function ($query) {
                    $query->withCount(['subSubCategoryProduct'])->where('position', 2);
                }])->withCount(['subCategoryProduct'])->where('position', 1);
            }, 'childes.childes'])
            ->where('position', 0)
            ->where('home_status', 1)
            ->whereNotIn('id', [33, 39])  // Exclude categories with IDs 33 and 39
            ->priority()->get();

        return response()->json($categories, 200);
    }


    public function get_products(Request $request, $id)
    {
        return response()->json(Helpers::product_data_formatting(CategoryManager::products($id, $request), true), 200);
    }

    public function find_what_you_need()
    {
        $find_what_you_need_categories = Category::where('parent_id', 0)
            ->with(['childes' => function ($query) {
                $query->withCount(['subCategoryProduct' => function ($query) {
                    return $query->active();
                }]);
            }])
            ->withCount(['product' => function ($query) {
                return $query->active();
            }])
            ->get()->toArray();

        $get_categories = [];
        foreach ($find_what_you_need_categories as $category) {
            $slice = array_slice($category['childes'], 0, 4);
            $category['childes'] = $slice;
            $get_categories[] = $category;
        }

        $final_category = [];
        foreach ($get_categories as $category) {
            if (count($category['childes']) > 0) {
                $final_category[] = $category;
            }
        }

        return response()->json(['find_what_you_need' => $final_category], 200);
    }
}