<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\HotelReview;
use App\Models\Hotels;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Utils\Helpers;
use App\Utils\ImageManager;
use Illuminate\Support\Facades\Validator;

class HotelsController extends Controller
{

    public function gethotels(Request $request)
    {

        if (!empty($request['latitude']) || !empty($request['longitude'])) {
            $radius = 10;
            $hotels = Hotels::withinRadius($request['latitude'], $request['longitude'], $radius)->where('status', 1)->get();
            if (!empty($hotels) && count($hotels) > 0) {
                $hotel_translation = [];
                foreach ($hotels as $key => $value) {
                    $translationKeys = ['hotel_name', 'description', 'amenities', 'room_amenities', 'room_types', 'booking_information'];
                    $translate = $value->translations()->pluck('value', 'key')->toArray();
                    foreach ($translationKeys as $translationKey) {
                        $hotel_translation[$key]["en_{$translationKey}"] = ($value[$translationKey] ?? '');
                        $hotel_translation[$key]["hi_{$translationKey}"] = ($translate[$translationKey] ?? '');
                    }
                    $hotel_translation[$key]['id'] =  $value['id'];
                    $hotel_translation[$key]['latitude'] =  $value['latitude'];
                    $hotel_translation[$key]['longitude'] =  $value['longitude'];
                    $hotel_translation[$key]['zipcode'] =  $value['zipcode'];
                    $hotel_translation[$key]['phone_no'] =  $value['phone_no'];
                    $hotel_translation[$key]['email_id'] =  $value['email_id'];
                    $hotel_translation[$key]['website_link'] =  $value['website_link'];
                    // $imagesIN = '';
                    // if(!empty($value['images']) && isset(json_decode($value['images'])[0]) ){
                    //     $imagesIN = Arr::random(json_decode($value['images']));
                    // }
                    $hotel_translation[$key]['image'] = getValidImage(path: 'storage/app/public/temple/hotel/' . $value['image'], type: 'backend-product');
                }
                return response()->json(['status' => 1, 'message' => 'Hotels available Nearby', 'recode' => count($hotel_translation), 'data' => $hotel_translation], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'No hotels available Nearby', 'recode' => 0, 'data' => []], 400);
            }
        }
        return response()->json(['status' => 0, 'recode' => 0, 'data' => [], 'message' => 'Please provide latitude and longitude'], 400);
    }


    public function gethotelbyid(Request $request)
    {

        $hotels = Hotels::where(['status' => 1])->with(['translations', 'states', 'cities'])->find($request['hotel_id']);
        if (!empty($hotels)) {
            $translationKeys = ['hotel_name', 'description', 'amenities', 'room_amenities', 'room_types', 'booking_information'];
            $translate = $hotels->translations()->pluck('value', 'key')->toArray();
            foreach ($translationKeys as $translationKey) {
                $hotel_translation["en_{$translationKey}"] = ($hotels[$translationKey] ?? '');
                $hotel_translation["hi_{$translationKey}"] = ($translate[$translationKey] ?? '');
            }
            $hotel_translation['id'] =  $hotels['id'];
            $hotel_translation['latitude'] =  $hotels['latitude'];
            $hotel_translation['longitude'] =  $hotels['longitude'];
            $hotel_translation['zipcode'] =  $hotels['zipcode'];
            $hotel_translation['phone_no'] =  $hotels['phone_no'];
            $hotel_translation['email_id'] =  $hotels['email_id'];
            $hotel_translation['website_link'] =  $hotels['website_link'];
            $hotel_translation['states'] =  $hotels['states'];
            $hotel_translation['cities'] =  $hotels['cities'];

            $hotel_translation['image_list'] = [];
            if (!empty($hotels['images']) && isset(json_decode($hotels['images'])[0])) {
                $list_imagearray = [];
                foreach (json_decode($hotels['images']) as $image) {
                    $list_imagearray[] = getValidImage(path: 'storage/app/public/temple/hotel/' . $image, type: 'backend-product');
                }
                $hotel_translation['image_list'] = $list_imagearray;
            }

            return response()->json(['status' => 1, 'message' => 'get Hotels', 'recode' => 1, 'data' => $hotel_translation], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'No Found', 'recode' => 0, 'data' => []], 400);
        }
    }




    public function hoteladdcomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'hotel_id' => ['required', function ($attribute, $value, $fail) {
                if (!Hotels::where('id', $value)->where('status', 1)->exists()) {
                    $fail('Hotel ID does not exist.');
                }
            },],
            'star' => 'required|numeric|between:1,5',
            'comment' => 'required',
        ], [
            'user_id.required' => 'User Id is Empty!',
            'hotel_id.required' => 'Hotel Id is Empty!',
            'star.required' => 'star is Empty!',
            'comment.required' => 'Comment is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        $images = '';

        $contact = HotelReview::where('user_id', $request->user_id)
            ->where('hotel_id', $request->hotel_id)
            ->first();

        if (!$contact) {
            if ($request->file('image')) {
                $images = ImageManager::upload('temple/hotel/review/', 'webp', $request->file('image'));
            }
            $contact = new HotelReview();
            $contact->user_id = $request->user_id;
            $contact->hotel_id = $request->hotel_id;
            $contact->comment = $request->comment;
            $contact->star = $request->star;
            $contact->image = $images;
            $contact->save();
        } else {
            // ImageManager::delete('temple/hotel/review/' . $contact['image']);
            return response()->json(['status' => 0, 'message' => 'You have Already added Comment', 'recode' => 0, 'data' => [], 'errors' => []], 200);
        }

        return response()->json(['status' => 1, 'message' => 'User Add Comment Successfully', 'recode' => 0, 'data' => [], 'errors' => []], 200);
    }


    public function gethotelcomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_id' => ['required', function ($attribute, $value, $fail) {
                if (!Hotels::where('id', $value)->where('status', 1)->exists()) {
                    $fail('Hotel ID does not exist.');
                }
            },],
        ], [
            'hotel_id.required' => 'Hotel Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        $getData = HotelReview::where(['status' => 1, 'hotel_id' => $request->hotel_id])->with(['userData'])->orderBy('id', 'desc')->get();
        $getData_stars = HotelReview::where(['status' => 1, 'hotel_id' => $request->hotel_id])->groupBy('hotel_id')->avg('star');

        if (!empty($getData) && count($getData) > 0) {
            $newData = [];
            foreach ($getData as $key => $value) {
                if (!empty($value['image'])) {
                    $newData[$key]['image'] =  getValidImage(path: 'storage/app/public/temple/hotel/review/' . $value['image'], type: 'backend-product');
                }
                $newData[$key]['user_name'] = $value['userData']['name'];
                $newData[$key]['user_id'] = $value['userData']['id'];
                $newData[$key]['user_image'] = getValidImage(path: 'storage/app/public/profile/' . $value['userData']['image'], type: 'backend-product');
                $newData[$key]['comment'] = $value['comment'];
                $newData[$key]['star'] = $value['star'];
                $newData[$key]['created_at'] = $value['created_at'];
            }
            return response()->json(['status' => 1, 'message' => 'get Hotel Comments', 'hotel_star' => $getData_stars, 'recode' => count($getData), 'data' => $newData], 200);
        }
        return response()->json(['status' => 0, 'message' => 'No Comment', 'recode' => 0, 'data' => []], 400);
    }
}