<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Utils\Helpers;
use App\Utils\ImageManager;
use Illuminate\Support\Facades\Validator;

class RestaurantController extends Controller
{
    public function getrestaurant(Request $request)
    {

        if (!empty($request['latitude']) || !empty($request['longitude'])) {
            $radius = 10;
            $restaurant = Restaurant::withinRadius($request['latitude'], $request['longitude'], $radius)->with(['translations'])->where('status', 1)->get();
            if (!empty($restaurant) && count($restaurant) > 0) {
                $restorant_translation = [];
                foreach ($restaurant as $key => $value) {
                    $translationKeys = ['restaurant_name', 'description', 'menu_highlights', 'more_details'];
                    $translate = $value->translations()->pluck('value', 'key')->toArray();
                    foreach ($translationKeys as $translationKey) {
                        $restorant_translation[$key]["en_{$translationKey}"] = ($value[$translationKey] ?? '');
                        $restorant_translation[$key]["hi_{$translationKey}"] = ($translate[$translationKey] ?? '');
                    }
                    $restorant_translation[$key]['id'] =  $value['id'];
                    $restorant_translation[$key]['latitude'] =  $value['latitude'];
                    $restorant_translation[$key]['longitude'] =  $value['longitude'];
                    $restorant_translation[$key]['open_time'] =  $value['open_time'];
                    $restorant_translation[$key]['close_time'] =  $value['close_time'];
                    $restorant_translation[$key]['zipcode'] =  $value['zipcode'];
                    $restorant_translation[$key]['phone_no'] =  $value['phone_no'];
                    $restorant_translation[$key]['youtube_video'] =  $value['youtube_video'];
                    $restorant_translation[$key]['email_id'] =  $value['email_id'];
                    $restorant_translation[$key]['website_link'] =  $value['website_link'];
                    // $imagesIN = '';
                    // if(!empty($value['images']) && isset(json_decode($value['images'])[0])){
                    //     $imagesIN = Arr::random(json_decode($value['images']));
                    // }
                    $restorant_translation[$key]['image'] = getValidImage(path: 'storage/app/public/temple/restaurant/' . $value['image'], type: 'backend-product');
                }
                return response()->json(['status' => 1, 'message' => 'Restaurant available Nearby', 'recode' => count($restorant_translation), 'data' => $restorant_translation], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'No Restaurant available Nearby', 'recode' => 0, 'data' => []], 400);
            }
        }
        return response()->json(['status' => 0, 'recode' => 0, 'data' => [], 'message' => 'Please provide latitude and longitude'], 400);
    }

    public function getrestaurantbyid(Request $request)
    {

        $restaurant = Restaurant::where(['status' => 1])->with(['country', 'states', 'cities'])->find($request['restaurant_id']);
        if (!empty($restaurant)) {
            $translationKeys = ['restaurant_name', 'description', 'menu_highlights', 'more_details'];
            $translate = $restaurant->translations()->pluck('value', 'key')->toArray();

            foreach ($translationKeys as $translationKey) {
                $restorant_translation["en_{$translationKey}"] = ($restaurant[$translationKey] ?? '');
                $restorant_translation["hi_{$translationKey}"] = ($translate[$translationKey] ?? '');
            }
            $restorant_translation['id'] =  $restaurant['id'];
            $restorant_translation['latitude'] =  $restaurant['latitude'];
            $restorant_translation['longitude'] =  $restaurant['longitude'];
            $restorant_translation['open_time'] =  $restaurant['open_time'];
            $restorant_translation['close_time'] =  $restaurant['close_time'];
            $restorant_translation['zipcode'] =  $restaurant['zipcode'];
            $restorant_translation['phone_no'] =  $restaurant['phone_no'];
            $restorant_translation['youtube_video'] =  $restaurant['youtube_video'];
            $restorant_translation['email_id'] =  $restaurant['email_id'];
            $restorant_translation['website_link'] =  $restaurant['website_link'];
            $restorant_translation['country'] =  $restaurant['country'];
            $restorant_translation['states'] =  $restaurant['states'];
            $restorant_translation['cities'] =  $restaurant['cities'];
            $restorant_translation['image_list'] = [];
            if (!empty($restaurant['images']) && isset(json_decode($restaurant['images'])[0])) {
                $list_imagearray = [];
                foreach (json_decode($restaurant['images']) as $image) {
                    $list_imagearray[] = getValidImage(path: 'storage/app/public/temple/restaurant/' . $image, type: 'backend-product');
                }
                $restorant_translation['image_list'] = $list_imagearray;
            }

            return response()->json(['status' => 1, 'message' => 'get Restaurant', 'recode' => 1, 'data' => $restorant_translation], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'No Found', 'recode' => 0, 'data' => []], 400);
        }
    }



    public function restaurantaddcomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'restaurant_id' => ['required', function ($attribute, $value, $fail) {
                if (!Restaurant::where('id', $value)->where('status', 1)->exists()) {
                    $fail('restaurant ID does not exist.');
                }
            },],
            'star' => 'required|numeric|between:1,5',
            'comment' => 'required',
        ], [
            'user_id.required' => 'User Id is Empty!',
            'restaurant_id.required' => 'Restaurant Id is Empty!',
            'star.required' => 'star is Empty!',
            'comment.required' => 'Comment is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        $images = '';

        $contact = RestaurantReview::where('user_id', $request->user_id)
            ->where('restaurant_id', $request->restaurant_id)
            ->first();

        if (!$contact) {
            if ($request->file('image')) {
                $images = ImageManager::upload('temple/restaurant/review/', 'webp', $request->file('image'));
            }
            $contact = new RestaurantReview();
            $contact->user_id = $request->user_id;
            $contact->restaurant_id = $request->restaurant_id;
            $contact->comment = $request->comment;
            $contact->star = $request->star;
            $contact->image = $images;
            $contact->save();
        } else {
            // ImageManager::delete('temple/restaurant/review/' . $contact['image']);
            return response()->json(['status' => 0, 'message' => 'You have Already added Comment', 'recode' => 0, 'data' => [], 'errors' => []], 200);
        }

        return response()->json(['status' => 1, 'message' => 'User Add Comment Successfully', 'recode' => 0, 'data' => [], 'errors' => []], 200);
    }


    public function getrestaurantcomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_id' => ['required', function ($attribute, $value, $fail) {
                if (!Restaurant::where('id', $value)->where('status', 1)->exists()) {
                    $fail('Restaurant ID does not exist.');
                }
            },],
        ], [
            'restaurant_id.required' => 'Restaurant Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        $getData = RestaurantReview::where(['status' => 1, 'restaurant_id' => $request->restaurant_id])->with(['userData'])->orderBy('id', 'desc')->get();
        $getData_stars = RestaurantReview::where(['status' => 1, 'restaurant_id' => $request->restaurant_id])->groupBy('restaurant_id')->avg('star');

        if (!empty($getData) && count($getData) > 0) {
            $newData = [];
            foreach ($getData as $key => $value) {
                if (!empty($value['image'])) {
                    $newData[$key]['image'] =  getValidImage(path: 'storage/app/public/temple/restaurant/review/' . $value['image'], type: 'backend-product');
                }
                $newData[$key]['user_name'] = $value['userData']['name'];
                $newData[$key]['user_id'] = $value['userData']['id'];
                $newData[$key]['user_image'] = getValidImage(path: 'storage/app/public/profile/' . $value['userData']['image'], type: 'backend-product');
                $newData[$key]['comment'] = $value['comment'];
                $newData[$key]['star'] = $value['star'];
                $newData[$key]['created_at'] = $value['created_at'];
            }
            return response()->json(['status' => 1, 'message' => 'get Restaurant Comments', 'restaurant_star' => $getData_stars, 'recode' => count($getData), 'data' => $newData], 200);
        }
        return response()->json(['status' => 0, 'message' => 'No Comment', 'recode' => 0, 'data' => []], 400);
    }
}