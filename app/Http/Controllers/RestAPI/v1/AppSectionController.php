<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\AppSection;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppSectionController extends Controller
{

    public function appSection(Request $request)
    {
        $sections = AppSection::all();
        $details = [];
        $pro_ids = [];
        $cat_ids = [];

        foreach ($sections as $section) {
            $banners = Banner::where('resource_id', $section->id)->where('published', 1)->get();

            $section_details = [
                'section' => $section,
                'banners' => []
            ];

            foreach ($banners as $banner) {
                $resourceType = $banner->app_section_resource_type;
                $resourceId = $banner->app_section_resource_id;

                $banner->photo = url('storage/app/public/banner/' . $banner->photo);

                if ($banner->image_type) {
                    if ($banner->image_type === 'right-top' || $banner->image_type === 'right-bottom') {

                        $section_details[$banner->image_type] = $banner;
                    } else {

                        $section_details['banners'][] = $banner;
                    }

                    if ($resourceType === 'product' && !in_array($resourceId, $pro_ids)) {
                        $pro_ids[] = $resourceId;
                        $product = Product::find($resourceId);
                        if ($product) {
                            $product->thumbnail = url('storage/app/public/product/thumbnail/' . $product->thumbnail);
                            $banner['product'] = Helpers::product_data_formatting($product);
                        }
                    } elseif ($resourceType === 'category' && !in_array($resourceId, $cat_ids)) {
                        $cat_ids[] = $resourceId;
                        $category = Category::find($resourceId);
                        if ($category) {
                            $category->icon = url('storage/app/public/category/' . $category->icon);
                            $categories = Category::with(['childes' => function ($query) {
                                $query->with(['childes' => function ($query) {
                                    $query->withCount(['subSubCategoryProduct'])->where('position', 2);
                                }])->withCount(['subCategoryProduct'])->where('position', 1);
                            }])->where('position', 0)->where('id', $resourceId)->priority()->get();

                            $banner['category_details'] = $categories;
                        }
                    }
                }
            }

            $details[] = $section_details;
        }

        return empty($details)
            ? response()->json(['status' => 400, 'message' => 'Data not found'])
            : response()->json(['status' => 200, 'data' => $details]);
    }
}
