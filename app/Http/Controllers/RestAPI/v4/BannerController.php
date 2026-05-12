<?php

namespace App\Http\Controllers\RestAPI\v4;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product;
use App\Utils\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    public function get_banners(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'banner_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request['banner_type'] == 'all') {
            $banners = Banner::where(['theme'=>'default', 'published' => 1])->get();
        } elseif ($request['banner_type'] == 'main_banner') {
            $banners = Banner::where(['theme'=>'default', 'published' => 1, 'banner_type' => 'Main Banner'])->get();
        } elseif ($request['banner_type'] == 'main_section_banner') {
            $banners = Banner::where(['theme'=>'default', 'published' => 1, 'banner_type' => 'Main Section Banner'])->get();
        } elseif ($request['banner_type'] == 'top_side_banner') {
            $banners = Banner::where(['theme'=>'default', 'published' => 1, 'banner_type' => 'Top Side Banner'])->get();
        } elseif ($request['banner_type'] == 'popup_banner') {
            $banners = Banner::where(['theme'=>'default', 'published' => 1, 'banner_type' => 'Popup Banner'])->get();
        }else {
            $banners = Banner::where(['theme'=>'default', 'published' => 1, 'banner_type' => 'Footer Banner'])->get();
        }
        $productIds = [];
        $data = [];
        foreach ($banners as $banner) {
            if ($banner['resource_type'] == 'product' && !in_array($banner['resource_id'], $productIds)) {
                $productIds[] = $banner['resource_id'];
                $product = Product::withCount('reviews')->find($banner['resource_id']);
                $banner['product'] = Helpers::product_data_formatting($product);
            }
            $data[] = $banner;
        }

        return response()->json($data, 200);

    }
}
