<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Cities;
use App\Models\Hotels;
use App\Models\Temple;
use App\Models\Restaurant;
use App\Models\TempleCategory;
use App\Models\TempleReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use App\Utils\Helpers;
use App\Utils\ImageManager;
use Illuminate\Support\Facades\Validator;

class TempleController extends Controller
{
    public function temple()
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $trustIds = Temple::whereNotNull('trust_id')
            ->distinct()
            ->pluck('trust_id')
            ->toArray();
        $fieldList = !empty($trustIds) ? implode(',', $trustIds) : null;
        $temple = Temple::where('status', 1)->with(['country', 'cities', 'states', 'galleries', 'category'])->orderByRaw("FIELD(trust_id, $fieldList) = 0, FIELD(trust_id, $fieldList), trust_id IS NULL, id DESC")->get();
        if ($temple) {
            $temple_translation = [];
            foreach ($temple as $key => $value) {
                $translationKeys = ['name', 'short_description', 'facilities', 'tips_restrictions', 'details', 'more_details', 'temple_known', 'expect_details', 'tips_details', 'temple_services', 'temple_aarti',  'tourist_place', 'temple_local_food'];
                $translate = $value ? $value->translations()->pluck('value', 'key')->toArray() : [];
                foreach ($translationKeys as $translationKey) {
                    $temple_translation[$key]["en_{$translationKey}"] = ($value[$translationKey] ?? '');
                    $temple_translation[$key]["hi_{$translationKey}"] = ($translate[$translationKey] ?? '');
                }
                $temple_translation[$key]['id'] =  $value['id'];
                $temple_translation[$key]['slug'] =  $value['slug'];
                $temple_translation[$key]['latitude'] =  $value['latitude'];
                $temple_translation[$key]['longitude'] =  $value['longitude'];
                $temple_translation[$key]['category_id'] =  $value['category_id'];
                $temple_translation[$key]['category'] =  $value['category'];
                $temple_translation[$key]['cities'] =  $value['cities'];
                $temple_translation[$key]['states'] =  $value['states'];
                $temple_translation[$key]['country'] =  $value['country'];
                $temple_translation[$key]['video_url'] =  $value['video_url'];
                $temple_translation[$key]['video_provider'] =  $value['video_provider'];
                $temple_translation[$key]['entry_fee'] =  $value['entry_fee'];
                $temple_translation[$key]['opening_time'] =  $value['opening_time'];
                $temple_translation[$key]['closeing_time'] =  $value['closeing_time'];
                $temple_translation[$key]['require_time'] =  $value['require_time'];
                $temple_translation[$key]['vip_darshan_status'] =  (($value['trust_id']) ? 1 : 0);

                $temple_translation[$key]['image'] = getValidImage(path: 'storage/app/public/temple/thumbnail/' . $value['thumbnail'], type: 'backend-product');
            }
            return response()->json(['status' => 1, 'message' => 'Get Data Successfully', 'recode' => count($temple), 'data' => $temple_translation], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Not Found Temple', 'recode' => 0, 'data' => []], 400);
    }


    public function category_list()
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $category_list = TempleCategory::where('status', 1)->with('translations')->get();

        if ($category_list) {
            $category_list1 = [];

            foreach ($category_list as $key => $val) {
                $translationKeys = ['name', 'short_description'];

                $translate = $val ? $val->translations()->pluck('value', 'key')->toArray() : [];
                foreach ($translationKeys as $translationKey) {
                    $category_list1[$key]["en_{$translationKey}"] = ($val[$translationKey] ?? '');
                    $category_list1[$key]["hi_{$translationKey}"] = ($translate[$translationKey] ?? '');
                }
                $category_list1[$key]['image'] = getValidImage(path: 'storage/app/public/temple/category/' . $val['image'], type: 'backend-product');
                $category_list1[$key]['id'] =  $val['id'];
            }

            return response()->json(['status' => 1, 'message' => 'Get Data Successfully', 'recode' => count($category_list), 'data' => $category_list1], 200);
        }
        return response()->json(['status' => 0, 'recode' => 0, 'data' => [], 'message' => 'Data Not Found'], 400);
    }


    public function getTemple(Request $request)
    {
        try {
            $trustIds = Temple::whereNotNull('trust_id')->distinct()->pluck('trust_id')->toArray();
            $fieldList = !empty($trustIds) ? implode(',', $trustIds) : null;

            if (isset($request['category_id']) && !empty($request['category_id']) && $request['category_id'] != 'all') {
                $templeData = Temple::where(['status' => 1, 'category_id' => $request['category_id']])->with(['country', 'cities', 'states', 'galleries2', 'translations', 'category'])->orderByRaw("FIELD(trust_id, $fieldList) = 0, FIELD(trust_id, $fieldList), trust_id IS NULL, id DESC")->get();
            } elseif (isset($request['latitude']) && !empty($request['latitude']) && isset($request['longitude']) && !empty($request['longitude'])) {
                $radius = 20;
                $templeData = Temple::withinRadius($request['latitude'], $request['longitude'], $radius)->with(['country', 'cities', 'states', 'galleries2', 'translations', 'category'])->where('status', 1)->orderByRaw("FIELD(trust_id, $fieldList) = 0, FIELD(trust_id, $fieldList), trust_id IS NULL, id DESC")->get();
            } else {
                $templeData = Temple::where('status', 1)->with(['country', 'cities', 'states', 'galleries2', 'translations', 'category'])->orderByRaw("FIELD(trust_id, $fieldList) = 0, FIELD(trust_id, $fieldList), trust_id IS NULL, id DESC")->get();
            }
            if ($templeData) {
                $temple_translation = [];
                foreach ($templeData as $key => $value) {
                    $translationKeys = ['name', 'short_description', 'facilities', 'tips_restrictions', 'details', 'more_details', 'temple_known', 'expect_details', 'tips_details', 'temple_services', 'temple_aarti',  'tourist_place', 'temple_local_food'];
                    $translate = $value ? $value->translations()->pluck('value', 'key')->toArray() : [];
                    foreach ($translationKeys as $translationKey) {
                        $temple_translation[$key]["en_{$translationKey}"] = ($value[$translationKey] ?? '');
                        $temple_translation[$key]["hi_{$translationKey}"] = ($translate[$translationKey] ?? '');
                    }
                    $temple_translation[$key]['id'] =  $value['id'];
                    $temple_translation[$key]['slug'] =  ($value['slug']??"");
                    $temple_translation[$key]['latitude'] =  $value['latitude'];
                    $temple_translation[$key]['longitude'] =  $value['longitude'];
                    $temple_translation[$key]['category_id'] =  $value['category_id'];
                    $temple_translation[$key]['category'] =  $value['category'];
                    $temple_translation[$key]['cities'] =  $value['cities'];
                    $temple_translation[$key]['states'] =  $value['states'];
                    $temple_translation[$key]['country'] =  $value['country'];
                    $temple_translation[$key]['video_url'] =  $value['video_url'];
                    $temple_translation[$key]['video_provider'] =  $value['video_provider'];
                    $temple_translation[$key]['entry_fee'] =  $value['entry_fee'];
                    $temple_translation[$key]['opening_time'] =  $value['opening_time'];
                    $temple_translation[$key]['closeing_time'] =  $value['closeing_time'];
                    $temple_translation[$key]['require_time'] =  $value['require_time'];
                    $temple_translation[$key]['vip_darshan_status'] =  (($value['trust_id']) ? 1 : 0);
                    // $imagesIN = '';
                    // if (!empty($value['galleries2']) && isset($value['galleries2']['images']) && json_decode($value['galleries2']['images'])) {
                    //     $imagesIN =  Arr::random(json_decode($value['galleries2']['images']));
                    // }
                    $temple_translation[$key]['image'] = getValidImage(path: 'storage/app/public/temple/thumbnail/' . $value['thumbnail'], type: 'backend-product');
                }
                return response()->json(['status' => 1, 'message' => 'Get Data Successfully', 'recode' => count($temple_translation), 'data' => $temple_translation], 200);
            }
            return response()->json(['status' => 0, 'recode' => 0, 'data' => [], 'message' => 'Data Not Found'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'recode' => 0, 'data' => [], 'message' => 'Something went wrong', 'error' => $e->getMessage()], 200);
        }
    }


    public function gettemplebyid(Request $request)
    {
        $languages = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $languages[0];
        $templeData = Temple::where(function ($query) use ($request) {
            $query->where('id', $request['temple_id'])
                ->orWhere('slug', $request['temple_id']);
        })->where(['status' => 1])->with(['country', 'cities', 'states', 'galleries2', 'translations', 'category'])->first();
        $temple_translation = [];
        if (!empty($templeData)) {
            $translationKeys = ['name', 'short_description', 'facilities', 'tips_restrictions', 'details', 'more_details', 'temple_known', 'expect_details', 'tips_details', 'temple_services', 'temple_aarti',  'tourist_place', 'temple_local_food'];
            $translate = $templeData ? $templeData->translations()->pluck('value', 'key')->toArray() : [];
            foreach ($translationKeys as $translationKey) {
                $temple_translation["en_{$translationKey}"] = ($templeData[$translationKey] ?? '');
                $temple_translation["hi_{$translationKey}"] = ($translate[$translationKey] ?? '');
            }
            $temple_translation['id'] =  $templeData['id'];
            $temple_translation['latitude'] =  $templeData['latitude'];
            $temple_translation['longitude'] =  $templeData['longitude'];
            $temple_translation['category_id'] =  $templeData['category_id'];
            $temple_translation['category'] =  $templeData['category'];
            $temple_translation['cities'] =  $templeData['cities'];
            $temple_translation['states'] =  $templeData['states'];
            $temple_translation['country'] =  $templeData['country'];
            $temple_translation['video_url'] =  $templeData['video_url'];
            $temple_translation['video_provider'] =  $templeData['video_provider'];
            $temple_translation['entry_fee'] =  $templeData['entry_fee'];
            $temple_translation['opening_time'] =  $templeData['opening_time'];
            $temple_translation['closeing_time'] =  $templeData['closeing_time'];
            $temple_translation['require_time'] =  $templeData['require_time'];
            $trustId = $templeData['trust_id'] ?? 0;
            $temple_translation_vip_darshan_status =  ((\App\Models\DonateTrust::where('id', $trustId)->exists()) ? 1 : 0);
            $temple_translation['trust_id'] = ((\App\Models\DonateTrust::where('id', $trustId)->exists()) ? $trustId : 0);
            $temple_translation['vip_booking_package'] = [];
            $vipPlan_check = 0;
            if (\App\Models\DonateTrust::where('id', $trustId)->exists()) {
                if ($templeData['vip_plans'] && json_decode($templeData['vip_plans'], true)) {
                    $pq = 0;
                    foreach (json_decode($templeData['vip_plans'], true) as $pass) {
                        $temple_translation['vip_booking_package'][$pq]['id'] = $pass['id'];
                        $temple_translation['vip_booking_package'][$pq]['price'] = $pass['package'][0]['price'] ?? '';
                        $temple_translation['vip_booking_package'][$pq]['package'] = $pass['package'][0]['name'] ?? '';
                        $temple_translation['vip_booking_package'][$pq]['name'] = $pass['name'] ?? '';
                        $vipPlan_check = 1;
                        $pq++;
                    }
                }
            }
            if (($temple_translation_vip_darshan_status == 1) && ($vipPlan_check == 1)) {
                $temple_translation['vip_darshan_status'] = 1;
            } else {
                $temple_translation['vip_darshan_status'] = 0;
            }

            $temple_translation['image_list'] = [];
            if (!empty($templeData['galleries2']) && isset($templeData['galleries2']['images']) && json_decode($templeData['galleries2']['images'])) {
                $list_imagearray = [];
                foreach (json_decode($templeData['galleries2']['images']) as $image) {
                    $list_imagearray[] = getValidImage(path: 'storage/app/public/temple/gallery/' . $image, type: 'backend-product');
                }
                $temple_translation['image_list'] = $list_imagearray;
            }
            return response()->json(['status' => 1, 'message' => 'Get Data Successfully', 'recode' => 1, 'data' => $temple_translation], 200);
        }
        return response()->json(['status' => 0, 'recode' => 0, 'data' => [], 'message' => 'Data Not Found'], 400);
    }



    public function templeaddcomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'temple_id' => ['required', function ($attribute, $value, $fail) {
                if (!Temple::where('id', $value)->where('status', 1)->exists()) {
                    $fail('Temple ID does not exist.');
                }
            },],
            'star' => 'required|numeric|between:1,5',
            'comment' => 'required',
        ], [
            'user_id.required' => 'User Id is Empty!',
            'temple_id.required' => 'Temple Id is Empty!',
            'star.required' => 'star is Empty!',
            'comment.required' => 'Comment is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        $images = '';

        $contact = TempleReview::where('user_id', $request->user_id)
            ->where('temple_id', $request->temple_id)
            ->first();

        if (!$contact) {
            $contact = new TempleReview();
            if ($request->file('image')) {
                $images = ImageManager::upload('temple/review/', 'webp', $request->file('image'));
            }
            $contact->user_id = $request->user_id;
            $contact->temple_id = $request->temple_id;
            $contact->comment = $request->comment;
            $contact->star = $request->star;
            $contact->image = $images;
            $contact->save();
        } else {
            // ImageManager::delete('temple/review/' . $contact['image']);
            return response()->json(['status' => 0, 'message' => 'You have Already added Comment', 'recode' => 0, 'data' => [], 'errors' => []], 200);
        }

        return response()->json(['status' => 1, 'message' => 'User Add Comment Successfully', 'recode' => 0, 'data' => [], 'errors' => []], 200);
    }


    public function gettemplecomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'temple_id' => ['required', function ($attribute, $value, $fail) {
                if (!Temple::where('id', $value)->where('status', 1)->exists()) {
                    $fail('Temple ID does not exist.');
                }
            },],
        ], [
            'cities_id.required' => 'Cities Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        $getData = TempleReview::where(['status' => 1, 'temple_id' => $request->temple_id])->with(['userData'])->orderBy('id', 'desc')->get();
        $getData_stars = TempleReview::where(['status' => 1, 'temple_id' => $request->temple_id])->groupBy('temple_id')->avg('star');

        if (!empty($getData) && count($getData) > 0) {
            $newData = [];
            foreach ($getData as $key => $value) {
                if (!empty($value['image'])) {
                    $newData[$key]['image'] =  getValidImage(path: 'storage/app/public/temple/review/' . $value['image'], type: 'backend-product');
                }
                $newData[$key]['user_name'] = $value['userData']['name'];
                $newData[$key]['user_id'] = $value['userData']['id'];
                $newData[$key]['user_image'] = getValidImage(path: 'storage/app/public/profile/' . $value['userData']['image'], type: 'backend-product');
                $newData[$key]['comment'] = $value['comment'];
                $newData[$key]['star'] = $value['star'];
                $newData[$key]['created_at'] = $value['created_at'];
            }
            return response()->json(['status' => 1, 'message' => 'get Temple Comments', 'temple_star' => $getData_stars, 'recode' => count($getData), 'data' => $newData], 200);
        }
        return response()->json(['status' => 0, 'message' => 'No Comment', 'recode' => 0, 'data' => []], 400);
    }

    public function SearchTemple(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'search' => 'required',
            ], [
                'search.required' => 'Search by temple, hotel, restaurant, location!',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => []], 403);
            }
            $search = $request->input('search');

            $temples = Temple::with(['country', 'cities', 'states'])
                ->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhereHas('country', fn($q) => $q->where('name', 'LIKE', "%$search%"))
                        ->orWhereHas('cities', fn($q) => $q->where('city', 'LIKE', "%$search%"))
                        ->orWhereHas('states', fn($q) => $q->where('name', 'LIKE', "%$search%"));
                })
                ->where('status', 1)
                ->take(10)
                ->get();
            $temple_translation = [];
            if (!empty($temples)  && count($temples) > 0) {
                foreach ($temples as $key => $value) {
                    $translate = $value ? $value->translations()->pluck('value', 'key')->toArray() : [];
                    $temple_translation[$key]["en_name"] = $value['name'] ?? '';
                    $temple_translation[$key]["hi_name"] = $translate['name'] ?? '';
                    $temple_translation[$key]['id'] = $value['id'];
                    $temple_translation[$key]['slug'] = ($value['slug'] ?? "");
                }
            }

            $restaurants = Restaurant::with(['country', 'cities', 'states'])
                ->where(function ($query) use ($search) {
                    $query->where('restaurant_name', 'LIKE', "%$search%")
                        ->orWhereHas('country', fn($q) => $q->where('name', 'LIKE', "%$search%"))
                        ->orWhereHas('cities', fn($q) => $q->where('city', 'LIKE', "%$search%"))
                        ->orWhereHas('states', fn($q) => $q->where('name', 'LIKE', "%$search%"));
                })
                ->where('status', 1)
                ->take(10)
                ->get();

            $restorant_translation = [];
            if (!empty($restaurants) && count($restaurants) > 0) {
                foreach ($restaurants as $key => $value) {
                    $translate = $value ? $value->translations()->pluck('value', 'key')->toArray() : [];
                    $restorant_translation[$key]["en_restaurant_name"] = $value['restaurant_name'] ?? '';
                    $restorant_translation[$key]["hi_restaurant_name"] = $translate['restaurant_name'] ?? '';
                    $restorant_translation[$key]['id'] = $value['id'];
                    $restorant_translation[$key]['slug'] = ($value['slug'] ?? "");
                }
            }

            $hotels = Hotels::with(['country', 'cities', 'states'])
                ->where(function ($query) use ($search) {
                    $query->where('hotel_name', 'LIKE', "%$search%")
                        ->orWhereHas('country', fn($q) => $q->where('name', 'LIKE', "%$search%"))
                        ->orWhereHas('cities', fn($q) => $q->where('city', 'LIKE', "%$search%"))
                        ->orWhereHas('states', fn($q) => $q->where('name', 'LIKE', "%$search%"));
                })
                ->where('status', 1)
                ->take(10)
                ->get();

            $hotel_translation = [];
            if (!empty($hotels) && count($hotels) > 0) {
                foreach ($hotels as $key => $value) {
                    $translate = $value ? $value->translations()->pluck('value', 'key')->toArray() : [];
                    $hotel_translation[$key]["en_hotel_name"] = $value['hotel_name'] ?? '';
                    $hotel_translation[$key]["hi_hotel_name"] = $translate['hotel_name'] ?? '';
                    $hotel_translation[$key]['id'] = $value['id'];
                    $hotel_translation[$key]['slug'] = ($value['slug'] ?? "");
                }
            }
            $cities = Cities::with(['country', 'states'])
                ->where(function ($query) use ($search) {
                    $query->where('city', 'LIKE', "%$search%")
                        ->orWhereHas('country', fn($q) => $q->where('name', 'LIKE', "%$search%"))
                        ->orWhereHas('states', fn($q) => $q->where('name', 'LIKE', "%$search%"));
                })
                ->where('status', 1)
                ->take(10)
                ->get();
            $cities_translation = [];
            if (!empty($cities)  && count($cities) > 0) {
                foreach ($cities as $key => $value) {
                    $translate = $value ? $value->translations()->pluck('value', 'key')->toArray() : [];
                    $cities_translation[$key]["en_city"] = $value['city'] ?? '';
                    $cities_translation[$key]["hi_city"] = $translate['city'] ?? '';
                    $cities_translation[$key]['id'] = $value['id'];
                    $cities_translation[$key]['slug'] = ($value['slug'] ?? "");
                }
            }
            $data = [
                'temples' => $temple_translation,
                'restaurants' => $restorant_translation,
                'hotels' => $hotel_translation,
                'cities' => $cities_translation,
            ];
            return response()->json(['status' => 1, 'message' => 'Search results fetched successfully!', 'recode' => 1, 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Something went wrong during search!', 'recode' => 0, 'data' => [], 'error' => $e->getMessage(),], 200);
        }
    }
}
