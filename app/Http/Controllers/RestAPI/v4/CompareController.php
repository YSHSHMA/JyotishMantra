<?php

namespace App\Http\Controllers\RestAPI\v4;

use App\Http\Controllers\Controller;
use App\Models\ProductCompare;
use App\Utils\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function __construct(
        private ProductCompare $product_compare,
    ) {

    }

    public function list(Request $request){
        $compare_lists = $this->product_compare->with('product.rating')
            ->whereHas('product')
            ->where('user_id', $request->user()->id)
            ->get();

        $compare_lists?->map(function($compare){
            $compare->product = Helpers::product_data_formatting($compare->product);
            return $compare;
        });

        return response()->json($compare_lists, 200);
    }

    public function compare_product_store(Request $request)
    {
        $compare_list = $this->product_compare->where(['user_id'=> $request->user()->id, 'product_id'=> $request->product_id])->first();
        if ($compare_list) {
            $compare_list->delete();
            $count_compare_list = $this->product_compare->whereHas('product',function($q){
                return $q;
            })->where('user_id', $request->user()->id);

            $product_count = $this->product_compare->where(['product_id' => $request->product_id])->count();

            return response()->json([
                'error' => translate("compare_list_Removed"),
                'value' => 2,
                'count' => $count_compare_list,
                'product_count' => $product_count
            ]);


        } else {
            $count_compare_list_exist = $this->product_compare->where('user_id', $request->user()->id)->count();

            if ($count_compare_list_exist == 3){
                $this->product_compare->where('user_id', $request->user()->id)->orderBY('id')->first()->delete();
            }

            $compare_list = new ProductCompare;
            $compare_list->user_id = $request->user()->id;
            $compare_list->product_id = $request->product_id;
            $compare_list->save();

            $count_compare_list = $this->product_compare->whereHas('product',function($q){
                return $q;
            })->where('user_id', $request->user()->id)->count();

            $product_count = $this->product_compare->where(['product_id' => $request->product_id])->count();

            return response()->json([
                'message' => 'successfully added',
                'status' => 1,
                'count' => $count_compare_list,
                'id' => $request->product_id,
                'product_count' => $product_count
            ], 200);
        }
    }

    public function clear_all(Request $request){
        $this->product_compare->where('user_id', $request->user()->id)->delete();

        return response()->json([
            'message' => 'successfully clear',
            'status' => 1,
        ], 200);
    }

    public function compareProductReplace(Request $request): JsonResponse
    {
        $isCompareProductExist = $this->product_compare->where(['user_id'=> $request->user()->id, 'product_id'=> $request['product_id']])->first();
        if($isCompareProductExist){
            return response()->json(['message'=>'Product already added'],403);
        }

        $newCompareList = $this->product_compare->find($request['compare_id']);
        if ($newCompareList) {
            $newCompareList->product_id = $request['product_id'];
            $newCompareList->save();
        }else{
            $this->product_compare->insert([
                'user_id'=> $request->user()->id,
                'product_id'=> $request['product_id']
            ]);
        }
        return response()->json(['message'=>'Successfully added'],200);
    }
}
