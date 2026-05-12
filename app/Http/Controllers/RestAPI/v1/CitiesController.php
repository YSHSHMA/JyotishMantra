<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\States;
use App\Models\Cities;
use App\Models\CitiesReview;
use App\Models\CitiesVisits;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use App\Utils\Helpers;
use App\Utils\ImageManager;
use Illuminate\Support\Facades\Validator;

class CitiesController extends Controller
{
    public function cities()
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $cities = Cities::with(['states', 'country', 'translations'])->with('visits')->get();

        if ($cities) {
            $temple_translation = [];
            foreach ($cities as $key => $value) {
                $translationKeys = ['city', 'short_desc', 'description', 'famous_for', 'festivals_and_events'];
                $translate = $value->translations()->pluck('value', 'key')->toArray();

                foreach ($translationKeys as $translationKey) {
                    $temple_translation[$key]["en_{$translationKey}"] = ($value[$translationKey] ?? '');
                    $temple_translation[$key]["hi_{$translationKey}"] = ($translate[$translationKey] ?? '');
                }
                $temple_translation[$key]['id'] =  $value['id'];
                $temple_translation[$key]['latitude'] =  $value['latitude'];
                $temple_translation[$key]['longitude'] =  $value['longitude'];
                $temple_translation[$key]['states'] =  $value['states'];
                $temple_translation[$key]['country'] =  $value['country'];
                // $imagesIN = '';
                // if (!empty($value['images']) && isset($value['images']) && json_decode($value['images'])) {
                //     $imagesIN =  Arr::random(json_decode($value['images']));
                // }
                $temple_translation[$key]['image'] = getValidImage(path: 'storage/app/public/cities/citie_image/' . ($value['image'] ?? ""), type: 'backend-product');
            }
            return response()->json(['status' => 1, 'message' => 'get Cities Successfully', 'recode' => count($temple_translation), 'data' => $temple_translation], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Not Found', 'recode' => '0', 'data' => []], 400);
    }

    public function getcities(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        if (!empty($request['latitude']) || !empty($request['longitude'])) {
            $radius = 70;
            $city_list = Cities::withinRadius($request['latitude'], $request['longitude'], $radius)->with(['translations'])->where('status', 1)->get();
            if (!empty($city_list) && count($city_list) > 0) {
                $cities_translation = [];
                foreach ($city_list as $key => $value) {
                    $translationKeys = ['city', 'short_desc', 'description', 'famous_for', 'festivals_and_events'];
                    $translate = $value->translations()->pluck('value', 'key')->toArray();
                    foreach ($translationKeys as $translationKey) {
                        $cities_translation[$key]["en_{$translationKey}"] = ($value[$translationKey] ?? '');
                        $cities_translation[$key]["hi_{$translationKey}"] = ($translate[$translationKey] ?? '');
                    }
                    $cities_translation[$key]['id'] =  $value['id'];
                    $cities_translation[$key]['latitude'] =  $value['latitude'];
                    $cities_translation[$key]['longitude'] =  $value['longitude'];
                    // $imagesIN = '';
                    // if (!empty($value['images']) && isset($value['images']) && json_decode($value['images'])) {
                    //     $imagesIN =  Arr::random(json_decode($value['images']));
                    // }
                    $cities_translation[$key]['image'] = getValidImage(path: 'storage/app/public/cities/citie_image/' . ($value['image'] ?? ""), type: 'backend-product');
                }
                return response()->json(['status' => 1, 'message' => 'Cities available Nearby', 'recode' => count($cities_translation), 'data' => $cities_translation], 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'No Cities available Nearby', 'recode' => 0, 'data' => []], 400);
            }
        }
        return response()->json(['status' => 0, 'recode' => 0, 'data' => [], 'message' => 'Please provide latitude and longitude'], 400);
    }

    public function getcitiesbyid(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];

        $city_list = Cities::with(['translations', 'visits', 'states', 'country'])->find($request['citie_id']);
        if (!empty($city_list)) {
            $translationKeys = ['city', 'short_desc', 'description', 'famous_for', 'festivals_and_events'];
            $translate = $city_list->translations()->pluck('value', 'key')->toArray();
            foreach ($translationKeys as $translationKey) {
                $cities_translation["en_{$translationKey}"] = ($city_list[$translationKey] ?? '');
                $cities_translation["hi_{$translationKey}"] = ($translate[$translationKey] ?? '');
            }
            $cities_translation['id'] =  $city_list['id'];
            $cities_translation['latitude'] =  $city_list['latitude'];
            $cities_translation['longitude'] =  $city_list['longitude'];
            $cities_translation['states'] =  $city_list['states'];
            $cities_translation['country'] =  $city_list['country'];
            $data_citiesvisites = [];
            $citiesvisiteKeys = ['month_name', 'season', 'crowd', 'weather', 'sight'];
            if (!empty($city_list['visits'])) {
                foreach ($city_list['visits'] as $keycitie => $citivalue) {
                    $getCities = CitiesVisits::where('id', $citivalue['id'])->first();
                    $visittranslate = $getCities->translations()->pluck('value', 'key')->toArray();
                    foreach ($citiesvisiteKeys as $translationKey1) {
                        $cities_translation['visits'][$keycitie]["en_{$translationKey1}"] = ($getCities[$translationKey1] ?? '');
                        $cities_translation['visits'][$keycitie]["hi_{$translationKey1}"] = ($visittranslate[$translationKey1] ?? '');
                    }
                    $cities_translation['visits'][$keycitie]["image"] = getValidImage(path: 'storage/app/public/cities/visit/' . $getCities['image'], type: 'backend-product');;
                }
            }
            // gallery
            $cities_translation['image_list'] = [];
            if (!empty($city_list['images']) && isset(json_decode($city_list['images'])[0]) && ($listofimage = json_decode($city_list['images']))) {
                $imagearrays = [];
                foreach ($listofimage as $image) {
                    $imagearrays[] = getValidImage(path: 'storage/app/public/cities/' . $image, type: 'backend-product');
                }
                $cities_translation['image_list'] = $imagearrays;
            }
            //slider
            $cities_translation['slider_list'] = [];
            if (!empty($city_list['slider_image']) && isset(json_decode($city_list['slider_image'])[0]) && ($listofimage = json_decode($city_list['slider_image']))) {
                $imagearrays = [];
                foreach ($listofimage as $image) {
                    $imagearrays[] = getValidImage(path: 'storage/app/public/cities/citie_image/' . $image, type: 'backend-product');
                }
                $cities_translation['slider_list'] = $imagearrays;
            }


            return response()->json(['status' => 1, 'message' => 'Get Cities Successfully', 'recode' => 1, 'data' => $cities_translation], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'No Found', 'recode' => 0, 'data' => []], 400);
        }
    }

    public function citiesaddcomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'cities_id' => ['required', function ($attribute, $value, $fail) {
                if (!Cities::where('id', $value)->exists()) {
                    $fail('City ID does not exist.');
                }
            },],
            'star' => 'required|numeric|between:1,5',
            'comment' => 'required',
        ], [
            'user_id.required' => 'User Id is Empty!',
            'cities_id.required' => 'Cities Id is Empty!',
            'star.required' => 'star is Empty!',
            'comment.required' => 'Comment is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        $images = '';


        $contact = CitiesReview::where('user_id', $request->user_id)
            ->where('cities_id', $request->cities_id)
            ->first();

        if (!$contact) {
            if ($request->file('image')) {
                $images = ImageManager::upload('cities/review/', 'webp', $request->file('image'));
            }
            $contact = new CitiesReview();
            $contact->user_id = $request->user_id;
            $contact->cities_id = $request->cities_id;
            $contact->comment = $request->comment;
            $contact->star = $request->star;
            $contact->image = $images;
            $contact->save();
        } else {
            // ImageManager::delete('cities/review/' . $contact['image']);
            return response()->json(['status' => 0, 'message' => 'You have Already added Comment', 'recode' => 0, 'data' => [], 'errors' => []], 200);
        }

        return response()->json(['status' => 1, 'message' => 'User Add Comment Successfully', 'recode' => 0, 'data' => [], 'errors' => []], 200);
    }


    public function getcitycomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cities_id' => ['required', function ($attribute, $value, $fail) {
                if (!Cities::where('id', $value)->exists()) {
                    $fail('City ID does not exist.');
                }
            },],
        ], [
            'cities_id.required' => 'Cities Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        $getData = CitiesReview::where(['status' => 1, 'cities_id' => $request->cities_id])->with(['userData'])->orderBy('id', 'desc')->get();
        $getData_stars = CitiesReview::where(['status' => 1, 'cities_id' => $request->cities_id])->groupBy('cities_id')->avg('star');

        if (!empty($getData) && count($getData) > 0) {
            $newData = [];
            foreach ($getData as $key => $value) {
                if (!empty($value['image'])) {
                    $newData[$key]['image'] =  getValidImage(path: 'storage/app/public/cities/review/' . $value['image'], type: 'backend-product');
                }
                $newData[$key]['user_name'] = $value['userData']['name'];
                $newData[$key]['user_id'] = $value['userData']['id'];
                $newData[$key]['user_image'] = getValidImage(path: 'storage/app/public/profile/' . $value['userData']['image'], type: 'backend-product');
                $newData[$key]['comment'] = $value['comment'];
                $newData[$key]['star'] = $value['star'];
                $newData[$key]['created_at'] = $value['created_at'];
            }
            return response()->json(['status' => 1, 'message' => 'get Cities Comments', 'cities_star' => $getData_stars, 'recode' => count($getData), 'data' => $newData], 200);
        }
        return response()->json(['status' => 0, 'message' => 'No Comment', 'recode' => 0, 'data' => []], 400);
    }
}