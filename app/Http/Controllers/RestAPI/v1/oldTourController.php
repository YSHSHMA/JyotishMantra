<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\TourAndTravel;
use App\Models\TourLeads;
use App\Models\TourOrder;
use App\Models\TourReviews;
use App\Models\TourType;
use App\Models\TourVisits;
use App\Models\User;
use App\Utils\ImageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Utils\Helpers;
use SimplePie\Cache\Redis;

class TourController extends Controller
{
    public function AllCategory(){
        $cities_tour = TourType::where('status',1)->orderBy('id','desc')->get();
        if (!empty($cities_tour) && count($cities_tour) > 0) {
            $translation = [];
            foreach ($cities_tour as $key => $value) {
                $hindi_tour = $value->translations()->pluck('value', 'key')->toArray();
                $translation[$key]['slug'] = $value['slug'];
                $translation[$key]['en_name'] = ($value['name']??"");
                $translation[$key]['hi_name'] = ($hindi_tour['name']??"");
            }
            return response()->json(['status' => 1, 'count' => count($translation), 'data' => $translation], 200);
        } else {
            return response()->json(['status' => 0, 'count' => 0, 'data' => []], 200);
        }
    }

    public function AllTour(Request $request)
    {
        $request->validate([
            'special_type' => ['nullable', function ($attribute, $value, $fail) {
                if (!TourType::where('slug', $value)->where('status', 1)->exists()) {
                    $fail('The selected tour type is invalid or inactive.');
                }
            },],
        ], [
            'special_type' => 'special Tour type is required!',
        ]);
        if (!empty($request->special_type)) {
            $special_tour = TourVisits::where('tour_type', $request->special_type)->where('status', 1)
                ->where(function ($query) {
                    $query->where('use_date', 0)->orWhere(function ($query) {
                        $query->where('use_date', 1)
                            ->whereNotNull('startandend_date')
                            ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                    });
                })->get();
            if (!empty($special_tour) && count($special_tour) > 0) {
                foreach ($special_tour as $key => $val) {
                    $hindi_tour = $val->translations()->pluck('value', 'key')->toArray();
                    $cities_tour[$key]['id'] = $val['id'];
                    $cities_tour[$key]['hi_tour_name'] = $hindi_tour['tour_name'];
                    $cities_tour[$key]['en_tour_name'] = $val['tour_name'];
                    // $cities_tour[$key]['package_list'] = json_decode($val['package_list'], true);
                    $cabs_lists = [];
                    if (!empty($val['cab_list_price']) && json_decode($val['cab_list_price'], true)) {
                        foreach (json_decode($val['cab_list_price'], true) as $kk => $val) {
                            $cabs_lists[$kk]['price'] = $val['price'];
                            $cabs_lists[$kk]['cab_id'] = $val['id'];
                            $getCabs = \App\Models\TourCab::where('id', $val['id'])->first();
                            $cab_name = ucwords($getCabs['name'] ?? '');
                            $cabs_lists[$kk]['cab_name'] = $cab_name;
                            $cabs_lists[$kk]['seats'] = ($getCabs['seats'] ?? '');
                        }
                    }
                    $package_lists = [];
                    if (!empty($val['package_list_price']) && json_decode($val['package_list_price'], true)) {
                        foreach (json_decode($val['package_list_price'], true) as $kk => $val) {
                            $package_lists[$kk]['price'] = $val['pprice'];
                            $package_lists[$kk]['package_id'] = $val['id'];
                            $getpackage = \App\Models\TourPackage::where('id', $val['id'])->first();
                            $package_lists[$kk]['package_name'] = ucwords($getpackage['name'] ?? '');
                            $package_lists[$kk]['seats'] = ($getpackage['seats'] ?? '');
                            $package_lists[$kk]['type'] = ($getpackage['type'] ?? '');
                            $package_lists[$kk]['title'] = ($getpackage['title'] ?? '');
                        }
                    }
                    $cities_tour[$key]['cab_list'] = $cabs_lists;
                    $cities_tour[$key]['package_list'] = $package_lists;
                    $cities_tour[$key]['use_date'] = ($val['use_date'] ?? '');
                    $cities_tour[$key]['date'] = ($val['startandend_date'] ?? '');
                    $cities_tour[$key]['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $val['tour_image'], type: 'backend-product');
                }
            }
        } else {
            $cities_tour = [];
            $special_tour = TourVisits::where('tour_type', 'cities_tour')->where('status', 1)->groupBy('cities_name')->get()->groupBy('state_name');
            if (!empty($special_tour) && count($special_tour) > 0) {
                $p = 0;
                foreach ($special_tour as $key => $val) {
                    if (!empty($val) && count($val) > 0) {
                        $q = 0;
                        foreach ($val as $kay => $state) {
                            $hindi_tour = (TourVisits::find($state['id']))->translations()->pluck('value', 'key')->toArray();
                            if ($q == 0) {
                                $cities_tour[$p]['en_state_name'] = $state['state_name'] ?? '';
                                $cities_tour[$p]['hi_state_name'] = $hindi_tour['state_name'] ?? "";
                            }
                            $cities_tour[$p]['list'][$q]['id'] = $state['id'] ?? '';
                            $cities_tour[$p]['list'][$q]['en_cities_name'] = $state['cities_name'];
                            $cities_tour[$p]['list'][$q]['hi_cities_name'] = $hindi_tour['cities_name'] ?? "";
                            $cities_tour[$p]['list'][$q]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $state['tour_image'], type: 'backend-product');

                            $q++;
                        }
                    }
                    $p++;
                }
            }
        }
        if (!empty($cities_tour) && count($cities_tour) > 0) {
            return response()->json(['status' => 1, 'count' => count($cities_tour), 'data' => $cities_tour], 200);
        } else {
            return response()->json(['status' => 0, 'count' => 0, 'data' => []], 200);
        }
    }
    public function CitiesTour(Request $request)
    {
        $request->validate([
            'special_type' => 'required|in:0,1',
            "cities_name" => "required",
        ], [
            'special_type.required' => 'special Tour type is required!',
            "cities_name.required" => "Cities Name is required!",
        ]);
        $cities_tour = [];
        if ($request->special_type == 1) {
            $special_tour = TourVisits::where('tour_type', 'special_tour')->where('status', 1)->where('cities_name', $request->cities_name)
                ->where(function ($query) {
                    $query->where('use_date', 0)->orWhere(function ($query) {
                        $query->where('use_date', 1)
                            ->whereNotNull('startandend_date')
                            ->whereRaw('? < STR_TO_DATE(SUBSTRING_INDEX(startandend_date, " - ", 1), "%Y-%m-%d")', [date('Y-m-d')]);
                    });
                })->get();
            if (!empty($special_tour) && count($special_tour) > 0) {
                foreach ($special_tour as $key => $val) {
                    $hindi_tour = $val->translations()->pluck('value', 'key')->toArray();
                    $cities_tour[$key]['id'] = $val['id'];
                    $cities_tour[$key]['hi_tour_name'] = $hindi_tour['tour_name'];
                    $cities_tour[$key]['en_tour_name'] = $val['tour_name'];
                    // $cities_tour[$key]['package_list'] = json_decode($val['package_list'], true);
                    $cabs_lists = [];
                    if (!empty($val['cab_list_price']) && json_decode($val['cab_list_price'], true)) {
                        foreach (json_decode($val['cab_list_price'], true) as $kk => $val) {
                            $cabs_lists[$kk]['price'] = $val['price'];
                            $cabs_lists[$kk]['cab_id'] = $val['id'];
                            $getCabs = \App\Models\TourCab::where('id', $val['id'])->first();
                            $cab_name = ucwords($getCabs['name'] ?? '');
                            $cabs_lists[$kk]['cab_name'] = $cab_name;
                            $cabs_lists[$kk]['seats'] = ($getCabs['seats'] ?? '');
                        }
                    }
                    $package_lists = [];
                    if (!empty($val['package_list_price']) && json_decode($val['package_list_price'], true)) {
                        foreach (json_decode($val['package_list_price'], true) as $kk => $val) {
                            $package_lists[$kk]['price'] = $val['pprice'];
                            $package_lists[$kk]['package_id'] = $val['id'];
                            $getpackage = \App\Models\TourPackage::where('id', $val['id'])->first();
                            $package_lists[$kk]['package_name'] = ucwords($getpackage['name'] ?? '');
                            $package_lists[$kk]['seats'] = ($getpackage['seats'] ?? '');
                            $package_lists[$kk]['type'] = ($getpackage['type'] ?? '');
                            $package_lists[$kk]['title'] = ($getpackage['title'] ?? '');
                        }
                    }
                    $cities_tour[$key]['cab_list'] = $cabs_lists;
                    $cities_tour[$key]['package_list'] = $package_lists;
                    $cities_tour[$key]['use_date'] = ($val['use_date'] ?? '');
                    $cities_tour[$key]['date'] = ($val['startandend_date'] ?? '');
                    $cities_tour[$key]['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $val['tour_image'], type: 'backend-product');
                }
            }
        } else {
            $special_tour = TourVisits::where('tour_type', 'cities_tour')->where('cities_name', $request->cities_name)->where('status', 1)->get();
            if (!empty($special_tour) && count($special_tour) > 0) {
                $p = 0;
                foreach ($special_tour as $key => $val) {
                    $hindi_tour = $val->translations()->pluck('value', 'key')->toArray();
                    $cities_tour[$p]['en_tour_name'] = $val['tour_name'] ?? "";
                    $cities_tour[$p]['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                    $cities_tour[$p]['use_date'] = ($val['use_date'] ?? '');
                    $cities_tour[$p]['date'] = ($val['startandend_date'] ?? '');
                    // $cities_tour[$p]['package_list'] = json_decode($val['package_list'], true);
                    $cabs_lists = [];
                    if (!empty($val['cab_list_price']) && json_decode($val['cab_list_price'], true)) {
                        foreach (json_decode($val['cab_list_price'], true) as $kk => $val) {
                            $cabs_lists[$kk]['price'] = $val['price'];
                            $cabs_lists[$kk]['cab_id'] = $val['id'];
                            $getCabs = \App\Models\TourCab::where('id', $val['id'])->first();
                            $cab_name = ucwords($getCabs['name'] ?? '');
                            $cabs_lists[$kk]['cab_name'] = $cab_name;
                            $cabs_lists[$kk]['seats'] = ($getCabs['seats'] ?? '');
                        }
                    }
                    $package_lists = [];
                    if (!empty($val['package_list_price']) && json_decode($val['package_list_price'], true)) {
                        foreach (json_decode($val['package_list_price'], true) as $kk => $val) {
                            $package_lists[$kk]['price'] = $val['pprice'];
                            $package_lists[$kk]['package_id'] = $val['id'];
                            $getpackage = \App\Models\TourPackage::where('id', $val['id'])->first();
                            $package_lists[$kk]['package_name'] = ucwords($getpackage['name'] ?? '');
                            $package_lists[$kk]['seats'] = ($getpackage['seats'] ?? '');
                            $package_lists[$kk]['type'] = ($getpackage['type'] ?? '');
                            $package_lists[$kk]['title'] = ($getpackage['title'] ?? '');
                        }
                    }
                    $cities_tour[$p]['cab_list'] = $cabs_lists;
                    $cities_tour[$p]['package_list'] = $package_lists;
                    $cities_tour[$p]['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $val['tour_image'], type: 'backend-product');
                    $p++;
                }
            }
        }
        if (!empty($cities_tour) && count($cities_tour) > 0) {
            return response()->json(['status' => 1, 'count' => count($cities_tour), 'data' => $cities_tour], 200);
        } else {
            return response()->json(['status' => 0, 'count' => 0, 'data' => []], 200);
        }
    }

    public function TourLeads(Request $request)
    {
        $request->validate([
            'tour_id' =>  ['required', function ($attribute, $value, $fail) {
                if (!TourVisits::where('id', $value)->where('status', 1)->exists()) {
                    $fail('The selected tour is invalid or inactive.');
                }
            },],
            'package_id' => 'required',
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'amount' => 'required|numeric|min:1',
        ], [
            'tour_id.required' => "tour is required!",
            'package_id.required' => "package is required!",
            'user_id.required' => "user Id is required!",
            'amount.required' => "amount is required!",
        ]);

        $leads = new TourLeads();
        $leads->tour_id = $request->tour_id ?? 0;
        $leads->package_id = $request->package_id;
        $leads->user_id = $request->user_id;
        $leads->amount = $request->amount;
        $leads->status = 1;
        $leads->save();

        return response()->json(['status' => 1, 'data' => ['insert_id' => $leads->id]], 200);
    }

    public function TourById(Request $request)
    {
        $request->validate([
            'tour_id' =>  ['required', function ($attribute, $value, $fail) {
                if (!TourVisits::where('id', $value)->where('status', 1)->exists()) {
                    $fail('The selected tour is invalid or inactive.');
                }
            },],
        ], [
            'tour_id.required' => "tour is required!",
        ]);
        $cities_tour = [];
        $special_tour = TourVisits::where('id', $request->tour_id)->where('status', 1)->first();
        if (!empty($special_tour)) {
            $hindi_tour = $special_tour->translations()->pluck('value', 'key')->toArray();
            $getRecode = ['tour_name', 'description', 'highlights', 'inclusion', "exclusion", 'terms_and_conditions', 'cancellation_policy', 'notes'];
            foreach ($getRecode as $name) {
                $cities_tour['en_' . $name] = $special_tour[$name] ?? "";
                $cities_tour['hi_' . $name] = $hindi_tour[$name] ?? "";
            }
            $cities_tour['use_date'] = ($special_tour['use_date'] ?? '');
            $cities_tour['date'] = ($special_tour['startandend_date'] ?? '');

            $cities_tour['pickup_time'] = ($special_tour['pickup_time'] ?? '');
            $cities_tour['pickup_location'] = ($special_tour['pickup_location'] ?? '');
            $cities_tour['pickup_lat'] = ($special_tour['pickup_lat'] ?? '');
            $cities_tour['pickup_long'] = ($special_tour['pickup_long'] ?? '');
            $cities_tour['cities_name'] = ($special_tour['cities_name'] ?? '');
            $cities_tour['country_name'] = ($special_tour['country_name'] ?? '');
            $cities_tour['state_name'] = ($special_tour['state_name'] ?? '');

            // $cities_tour['package_list'] = json_decode($special_tour['package_list'], true);
            $cabs_lists = [];
                    if (!empty($special_tour['cab_list_price']) && json_decode($special_tour['cab_list_price'], true)) {
                        foreach (json_decode($special_tour['cab_list_price'], true) as $kk => $val) {
                            $cabs_lists[$kk]['price'] = $val['price'];
                            $cabs_lists[$kk]['cab_id'] = $val['id'];
                            $getCabs = \App\Models\TourCab::where('id', $val['id'])->first();
                            $cab_name = ucwords($getCabs['name'] ?? '');
                            $cabs_lists[$kk]['cab_name'] = $cab_name;
                            $cabs_lists[$kk]['seats'] = ($getCabs['seats'] ?? '');
                        }
                    }
                    $package_lists = [];
                    if (!empty($special_tour['package_list_price']) && json_decode($special_tour['package_list_price'], true)) {
                        foreach (json_decode($special_tour['package_list_price'], true) as $kk => $val) {
                            $package_lists[$kk]['price'] = $val['pprice'];
                            $package_lists[$kk]['package_id'] = $val['id'];
                            $getpackage = \App\Models\TourPackage::where('id', $val['id'])->first();
                            $package_lists[$kk]['package_name'] = ucwords($getpackage['name'] ?? '');
                            $package_lists[$kk]['seats'] = ($getpackage['seats'] ?? '');
                            $package_lists[$kk]['type'] = ($getpackage['type'] ?? '');
                            $package_lists[$kk]['title'] = ($getpackage['title'] ?? '');
                        }
                    }
                    $cities_tour['cab_list'] = $cabs_lists;
                    $cities_tour['package_list'] = $package_lists;
            $cities_tour['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $special_tour['tour_image'], type: 'backend-product');
            $image_list = [];
            if (!empty($special_tour['image']) && json_decode($special_tour['image'], true)) {
                foreach (json_decode($special_tour['image'], true) as $value) {
                    $image_list[] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $value, type: 'backend-product');
                }
            }
            $cities_tour['image_list'] = $image_list;
        }
        if (!empty($cities_tour)) {
            return response()->json(['status' => 1, 'count' => 1, 'data' => $cities_tour], 200);
        }
        return response()->json(['status' => 0, 'count' => 0, 'data' => []], 200);
    }

    public function BookingList(Request $request)
    {
        $request->validate([
            'user_id' =>  ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
        ], [
            'user_id.required' => 'User Id is required!',
        ]);
        $bookingList = [];
        if (!empty($request->order_id)) {
            $all_booking = TourOrder::where('user_id', $request->user_id)->where('id', $request->order_id)->with(['Tour'])->first();
            if (!empty($all_booking)) {
                $bookingList['id'] = $all_booking['id'];
                $bookingList['order_id'] = $all_booking['order_id'];
                $bookingList['qty'] = $all_booking['qty'];
                $bookingList['en_tour_name'] = $all_booking['Tour']['tour_name'];
                $hindi_tour = (TourVisits::find($all_booking['tour_id']))->translations()->pluck('value', 'key')->toArray();
                $bookingList['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                $bookingList['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $all_booking['Tour']['tour_image'], type: 'backend-product');
                $bookingList['order_id'] = $all_booking['order_id'];
                $bookingList['amount'] = (($all_booking['amount'] ?? 0) + ($all_booking['coupon_amount'] ?? 0));
                $bookingList['coupon_amount'] = $all_booking['coupon_amount'];
                $bookingList['pay_amount'] = $all_booking['amount'];
                $bookingList['amount_status'] = $all_booking['amount_status'];
                $bookingList['transaction_id'] = $all_booking['transaction_id'];
                $bookingList['refund_status'] = $all_booking['refund_status'];
                $bookingList['pickup_address'] = $all_booking['pickup_address'];
                $bookingList['pickup_date'] = $all_booking['pickup_date'];
                $bookingList['pickup_time'] = $all_booking['pickup_time'];
                $bookingList['pickup_otp'] = $all_booking['pickup_otp'];
                $bookingList['pickup_status'] = $all_booking['pickup_status'];
                $bookingList['drop_opt'] = $all_booking['drop_opt'];
                $bookingList['drop_status'] = $all_booking['drop_status'];
                $bookingList['booking_time'] = $all_booking['created_at'];
            }
        } else {
            $all_booking = TourOrder::where('user_id', $request->user_id)->with(['Tour'])->get();
            if (!empty($all_booking) && count($all_booking) > 0) {
                foreach ($all_booking as $key => $value) {
                    $bookingList[$key]['id'] = $value['id'];
                    $bookingList[$key]['order_id'] = $value['order_id'];
                    $bookingList[$key]['qty'] = $value['qty'];
                    $bookingList[$key]['en_tour_name'] = $value['Tour']['tour_name'];
                    $hindi_tour = (TourVisits::find($value['tour_id']))->translations()->pluck('value', 'key')->toArray();
                    $bookingList[$key]['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                    $bookingList[$key]['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $value['Tour']['tour_image'], type: 'backend-product');
                    $bookingList[$key]['order_id'] = $value['order_id'];
                    $bookingList[$key]['amount'] = (($value['amount'] ?? 0) + ($value['coupon_amount'] ?? 0));
                    $bookingList[$key]['coupon_amount'] = $value['coupon_amount'];
                    $bookingList[$key]['pay_amount'] = $value['amount'];
                    $bookingList[$key]['amount_status'] = $value['amount_status'];
                    $bookingList[$key]['transaction_id'] = $value['transaction_id'];
                    $bookingList[$key]['refund_status'] = $value['refund_status'];
                    $bookingList[$key]['pickup_address'] = $value['pickup_address'];
                    $bookingList[$key]['pickup_date'] = $value['pickup_date'];
                    $bookingList[$key]['pickup_time'] = $value['pickup_time'];
                    $bookingList[$key]['pickup_otp'] = $value['pickup_otp'];
                    $bookingList[$key]['pickup_status'] = $value['pickup_status'];
                    $bookingList[$key]['drop_opt'] = $value['drop_opt'];
                    $bookingList[$key]['drop_status'] = $value['drop_status'];
                    $bookingList[$key]['booking_time'] = $value['created_at'];
                }
            }
        }
        return response()->json(['status' => 0, 'count' => 0, 'data' => $bookingList], 200);
    }

    public function touraddcomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'tour_id' => ['required', function ($attribute, $value, $fail) {
                if (!TourVisits::where('id', $value)->where('status', 1)->exists()) {
                    $fail('Tour ID does not exist.');
                }
            },],
            'star' => 'required|numeric|between:1,5',
            'comment' => 'required',
            'order_id' => 'required',
        ], [
            'user_id.required' => 'User Id is Empty!',
            'tour_id.required' => 'Tour Id is Empty!',
            'star.required' => 'star is Empty!',
            'comment.required' => 'Comment is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        $images = '';
        if ($request->file('image')) {
            $images = ImageManager::upload('tour_and_travels/review/', 'webp', $request->file('image'));
        }

        $contact = new TourReviews();
        $contact->order_id = $request->order_id;
        $contact->user_id = $request->user_id;
        $contact->tour_id = $request->tour_id;
        $contact->comment = $request->comment;
        $contact->star = $request->star;
        $contact->image = $images;
        $contact->status = 1;
        $contact->save();
        return response()->json(['status' => 1, 'message' => 'User Add Comment Successfully', 'recode' => 0, 'data' => [], 'errors' => []], 200);
    }


    public function gettourcomment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tour_id' => ['required', function ($attribute, $value, $fail) {
                if (!TourVisits::where('id', $value)->where('status', 1)->exists()) {
                    $fail('Tour ID does not exist.');
                }
            },],
        ], [
            'tour_id.required' => 'Tour Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 403);
        }
        $getData = TourReviews::where(['status' => 1, 'tour_id' => $request->tour_id])->with(['userData'])->orderBy('id', 'desc')->get();
        $getData_stars = TourReviews::where(['status' => 1, 'tour_id' => $request->tour_id])->groupBy('tour_id')->avg('star');
        $getList = [];
        if (!empty($getData) && count($getData) > 0) {
            foreach ($getData as $key => $value) {
                $getList[$key]['star'] = $value['star'];
                $getList[$key]['comment'] = $value['comment'];
                $getList[$key]['user_name'] = $value['userData']['name'];
                $getList[$key]['user_image'] = getValidImage(path: 'storage/app/public/profile/' . $value['userData']['image'], type: 'backend-product');
                if (!empty($value['image'])) {
                    $getList[$key]['image'] =  getValidImage(path: 'storage/app/public/tour_and_travels/review/' . $value['image'], type: 'backend-product');
                }
            }
            return response()->json(['status' => 1, 'message' => 'get Tour Comments', 'tour_star' => $getData_stars, 'recode' => count($getData), 'data' => $getList], 200);
        }
        return response()->json(['status' => 0, 'message' => 'No Comment', 'recode' => 0, 'data' => []], 400);
    }

    public function SearchName(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Product name is required!',
        ]);

        $sellers = TourVisits::where(function ($q) use ($request) {
            $q->orWhere('tour_name', 'like', "%{$request['name']}%");
            $q->orWhere('cities_name', 'like', "%{$request['name']}%");
            $q->orWhere('country_name', 'like', "%{$request['name']}%");
            $q->orWhere('state_name', 'like', "%{$request['name']}%");
        })->where('status', 1)->get();
        if ($request->role == 'web') {
            $recodes = '';
            foreach ($sellers as $product) {
                $recodes .= '<li class="list-group-item px-0 overflow-hidden">
                                <button type="submit" class="search-result-product btn p-0 m-0 search-result-product-button align-items-baseline text-start" 
                                        data-product-name="' . $product['tour_name'] . '" onclick="return $(`.search_ids`).val(`' . base64_encode($product['id']) . '`)">
                                    <span><i class="czi-search"></i></span>
                                    <div class="text-truncate">' . $product['tour_name'] . '</div>
                                    <span class="px-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-left" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M2 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1H3.707l10.147 10.146a.5.5 0 0 1-.708.708L3 3.707V8.5a.5.5 0 0 1-1 0z"/>
                                        </svg>
                                    </span>
                                </button>
                                
                            </li>';
            }
        } else {
            $recodes = [];
            foreach ($sellers as $ky => $product) {
                $recodes[$ky] = ['id' => $product['id'], 'name' => $product['tour_name']];
            }
        }
        if (!empty($sellers) && count($sellers) > 0) {
            return response()->json(['status' => 1, 'count' => count($sellers), 'data' => $recodes], 200);
        } else {
            return response()->json(['status' => 0, 'count' => 0, 'data' => (($request->role == 'web') ? '' : [])], 400);
        }
    }

    public function TourCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', function ($attribute, $value, $fail) {
                if (!User::where('id', $value)->where('is_active', 1)->exists()) {
                    $fail('The selected user is invalid or inactive.');
                }
            },],
            'coupon_code' => ['required', function ($attribute, $value, $fail) {
                if (!Coupon::where('code', $value)->where('coupon_type', 'tour_order')->where('status', 1)->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('expire_date', '>=', date('Y-m-d'))->exists()) {
                    $fail('Invalid Coupon Code.');
                }
            }],

            'amount' => 'required|numeric|min:1',
        ], [
            'user_id.required' => 'User Id is Empty!',
            'coupon_code.required' => 'Coupon Code is Empty!',
            'amount.required' => 'Amount is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $couponData = Coupon::where('code', $request->get('coupon_code'))->where('coupon_type', 'tour_order')->where('status', 1)->whereDate('start_date', '<=', date('Y-m-d'))->whereDate('expire_date', '>=', date('Y-m-d'))->first();
        $checkCoupon = TourOrder::where('coupon_id', ($couponData['id'] ?? ""))->count();
        if (($couponData['limit'] ?? 0) < $checkCoupon) {
            return response()->json(['status' => 0, 'message' => 'Coupon code is available for limited users which is not available at this time', 'recode' => 0, 'data' => []], 200);
        }
        $TourCoupon = TourOrder::where('coupon_id', ($couponData['id'] ?? ""))->where('user_id', $request->get('user_id'))->first();
        if (!empty($TourCoupon)) {
            return response()->json(['status' => 0, 'message' => 'The coupon code has already been used', 'recode' => 0, 'data' => []], 200);
        }
        //      400                   200                                 700                           200
        if (($couponData['min_purchase'] > $request->get('amount')) || ($couponData['max_discount'] < $request->get('amount'))) {
            return response()->json(['status' => 0, 'message' => 'Minimum and maximum coupon limits do not apply', 'recode' => 0, 'data' => []], 200);
        }

        $coupon_amount = 0;
        $final_amount = $request->get('amount');
        if ($couponData['discount_type'] == 'amount') {
            $coupon_amount = $couponData['discount'];
            $final_amount = ($final_amount - ($couponData['discount'] ?? 0));
        }
        if ($couponData['discount_type'] == 'percentage') {
            $coupon_amount =  round((($final_amount * ($couponData['discount'] ?? 0)) / 100), 2);
            $final_amount =  ($final_amount - $coupon_amount);
        }
        return response()->json(['status' => 1, 'message' => 'Successfully Coupon Apply', 'recode' => 1, 'data' => ['coupon_id' => $couponData['id'], 'coupon_amount' => $coupon_amount, 'final_amount' => $final_amount]], 200);
    }

    public function TourPending(Request $request)
    {
        $all_booking = TourOrder::where('cab_assign', 0)->where('amount_status', 1)->whereIn('refund_status', [0, 3])->where('pickup_date', '>', \Carbon\Carbon::today()->toDateString())->with(['Tour', "userData"])->get();
        if (!empty($all_booking) && count($all_booking) > 0) {
            foreach ($all_booking as $key => $value) {
                $bookingList[$key]['id'] = $value['id'];
                $bookingList[$key]['tour_id'] = $value['tour_id'];
                $bookingList[$key]['order_id'] = $value['order_id'];
                $bookingList[$key]['qty'] = $value['qty'];
                $bookingList[$key]['user_name'] = $value['userData']['name'];
                $bookingList[$key]['user_phone'] = $value['userData']['phone'];
                $bookingList[$key]['user_image'] = getValidImage(path: 'storage/app/public/profile/' . $value['userData']['image'], type: 'backend-product');
                $bookingList[$key]['en_tour_name'] = $value['Tour']['tour_name'];
                $hindi_tour = (TourVisits::find($value['tour_id']))->translations()->pluck('value', 'key')->toArray();
                $bookingList[$key]['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                $bookingList[$key]['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $value['Tour']['tour_image'], type: 'backend-product');
                $bookingList[$key]['order_id'] = $value['order_id'];
                $bookingList[$key]['amount'] = (($value['amount'] ?? 0) + ($value['coupon_amount'] ?? 0));
                $bookingList[$key]['coupon_amount'] = $value['coupon_amount'];
                $bookingList[$key]['pay_amount'] = $value['amount'];
                $bookingList[$key]['amount_status'] = $value['amount_status'];
                $bookingList[$key]['transaction_id'] = $value['transaction_id'];
                $bookingList[$key]['refund_status'] = $value['refund_status'];
                $bookingList[$key]['pickup_address'] = $value['pickup_address'];
                $bookingList[$key]['pickup_date'] = $value['pickup_date'];
                $bookingList[$key]['pickup_time'] = $value['pickup_time'];
                $bookingList[$key]['booking_time'] = $value['created_at'];
            }
            return response()->json(['status' => 1, 'count' => count($bookingList), 'data' => $bookingList], 200);
        } else {
            return response()->json(['status' => 0, 'count' => '0', 'data' => []], 200);
        }
    }

    public function CabTourOrdercancel(Request $request){
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'order_id' => ['required', function ($attribute, $value, $fail) {
                if (!TourOrder::where('id', $value)->where('refund_status', 0)->whereIn('status', [0, 1])->where('pickup_status', 0)->exists()) {
                    $fail('Order ID does not exist.');
                }
            },],
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'order_id.required' => 'Order Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $getData =  TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->with(['userData'])->first();
       if($getData){
        TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->update(['cab_assign'=>0]);
        TourAndTravel::where('id',$request->cab_assign)->update(["cancel_order"=> DB::raw('cancel_order + ' . 1)]);
           return response()->json(['status' => 1, 'message' => 'Cancel Order', 'data' => []], 200);
       }else{
           return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
       }
    }

    public function TourAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'order_id' => ['required', function ($attribute, $value, $fail) {
                if (!TourOrder::where('id', $value)->where('cab_assign', '=', 0)->exists()) {
                    $fail('The selected Order already assigned.');
                }
            },],
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'order_id.required' => 'Order Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        TourOrder::where('id', $request->order_id)->update(['cab_assign' => $request->cab_assign]);
        return response()->json(['status' => 1, 'message' => 'cab assign Successfully', 'data' => []], 200);
    }

    public function TourAssignConfirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            "type" => "required|in:confirm,one,two",
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'type.required' => 'Please Choose confirm,one,two',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $booking_query = TourOrder::where('cab_assign', $request->cab_assign)->where('amount_status', 1)->whereIn('refund_status', [0, 3]);
        if ($request->type == "confirm") {
            $booking_query->where('pickup_status', 0)->where('drop_status', 0);
        } elseif ($request->type == "one") {
            $booking_query->where('pickup_status', 1)->where('drop_status', 0);
        } elseif ($request->type == "two") {
            $booking_query->where('pickup_status', 1)->where('drop_status', 1);
        }
        $all_booking = $booking_query->with(['Tour', "userData"])->get();
        if (!empty($all_booking) && count($all_booking) > 0) {
            foreach ($all_booking as $key => $value) {
                $bookingList[$key]['id'] = $value['id'];
                $bookingList[$key]['tour_id'] = $value['tour_id'];
                $bookingList[$key]['order_id'] = $value['order_id'];
                $bookingList[$key]['qty'] = $value['qty'];
                $bookingList[$key]['user_name'] = $value['userData']['name'];
                $bookingList[$key]['user_phone'] = $value['userData']['phone'];
                $bookingList[$key]['user_image'] = getValidImage(path: 'storage/app/public/profile/' . $value['userData']['image'], type: 'backend-product');
                $bookingList[$key]['en_tour_name'] = $value['Tour']['tour_name'];
                $hindi_tour = (TourVisits::find($value['tour_id']))->translations()->pluck('value', 'key')->toArray();
                $bookingList[$key]['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
                $bookingList[$key]['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $value['Tour']['tour_image'], type: 'backend-product');
                $bookingList[$key]['order_id'] = $value['order_id'];
                $bookingList[$key]['amount'] = (($value['amount'] ?? 0) + ($value['coupon_amount'] ?? 0));
                $bookingList[$key]['coupon_amount'] = $value['coupon_amount'];
                $bookingList[$key]['pay_amount'] = $value['amount'];
                $bookingList[$key]['amount_status'] = $value['amount_status'];
                $bookingList[$key]['transaction_id'] = $value['transaction_id'];
                $bookingList[$key]['refund_status'] = $value['refund_status'];
                $bookingList[$key]['pickup_address'] = $value['pickup_address'];
                $bookingList[$key]['pickup_date'] = $value['pickup_date'];
                $bookingList[$key]['pickup_time'] = $value['pickup_time'];
                $bookingList[$key]['booking_time'] = $value['created_at'];
            }
            return response()->json(['status' => 1, 'count' => count($bookingList), 'data' => $bookingList], 200);
        } else {
            return response()->json(['status' => 0, 'count' => '0', 'data' => []], 200);
        }
    }

    public function TourCabView(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            "order_id" => "required",
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'order_id.required' => 'Order Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $getData =  TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->with(['userData'])->first();
        $bookingList = [];
        if (!empty($getData)) {
            $bookingList['id'] = $getData['id'];
            $bookingList['tour_id'] = $getData['tour_id'];
            $bookingList['order_id'] = $getData['order_id'];
            $bookingList['qty'] = $getData['qty'];
            $bookingList['user_name'] = $getData['userData']['name'];
            $bookingList['user_phone'] = $getData['userData']['phone'];
            $bookingList['user_image'] = getValidImage(path: 'storage/app/public/profile/' . $getData['userData']['image'], type: 'backend-product');
            $bookingList['en_tour_name'] = $getData['Tour']['tour_name'];
            $hindi_tour = (TourVisits::find($getData['tour_id']))->translations()->pluck('value', 'key')->toArray();
            $bookingList['hi_tour_name'] = $hindi_tour['tour_name'] ?? "";
            $bookingList['tour_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/tour_visit/' . $getData['Tour']['tour_image'], type: 'backend-product');
            $bookingList['order_id'] = $getData['order_id'];
            $bookingList['amount'] = (($getData['amount'] ?? 0) + ($getData['coupon_amount'] ?? 0));
            $bookingList['coupon_amount'] = $getData['coupon_amount'];
            $bookingList['pay_amount'] = $getData['amount'];
            $bookingList['amount_status'] = $getData['amount_status'];
            $bookingList['transaction_id'] = $getData['transaction_id'];
            $bookingList['refund_status'] = $getData['refund_status'];
            $bookingList['pickup_address'] = $getData['pickup_address'];
            $bookingList['pickup_date'] = $getData['pickup_date'];
            $bookingList['pickup_time'] = $getData['pickup_time'];
            $bookingList['booking_time'] = $getData['created_at'];
            return response()->json(['status' => 1, 'message' => 'Get Recode', 'data' => $bookingList], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
        }
    }

    public function TourCabOtpVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            "order_id" => "required",
            "otp" => "required",
            "type" => "required|in:one,two",
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'type.required' => 'Please Choose one,two',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $getData =  TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->with(['userData'])->first();
        if (!empty($getData)) {
            $deviceToken = $getData['userData']['cm_firebase_token'] ?? '';
            if ($request->type == 'one' && $request->otp == $getData['pickup_otp']) {
                TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->update(['pickup_status' => 1]);
                $title = 'Enjoy The journey';
                $message = "This journey changes life and enjoy";
            } elseif ($request->type == 'one' && $request->otp == $getData['drop_opt'] && $getData['pickup_status'] == 1 && $getData['drop_status'] == 0) {
                TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->update(['drop_status' => 1]);
                $title = 'Thank you for visiting';
                $message = "Please add your travel experience.";
                TourAndTravel::where('id', $request->cab_assign)
                    ->update([
                        'wallet_amount' => DB::raw('wallet_amount + ' . $getData['final_amount']),
                        'gst_amount' => DB::raw('gst_amount + ' . $getData['gst_amount']),
                        'admin_commission' => DB::raw('admin_commission + ' . $getData['admin_commission']),
                    ]);
            } else {
                return response()->json(['status' => 0, 'message' => (($getData['drop_status'] == 1) ? "Invalid OTP" : 'Already close Booking'), 'data' => []], 200);
            }
            $data = new \stdClass();
            $data->title = $title;
            $data->description = $message;
            $data->image = 'notification_image.png';

            $data1 = \App\Traits\PushNotificationTrait::sendFirebasePushNotification($data, $deviceToken);
            return response()->json(['status' => 1, 'message' => 'OTP successfully verified', 'data' => $data1], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
    }


    public function CabStoreFcmToken() {}

    public function TourCabOtpSend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            "order_id" => "required",
            "type" => "required|in:one,two",
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'type.required' => 'Please Choose one,two',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }

        $getData =  TourOrder::where('id', $request->order_id)->where('cab_assign', $request->cab_assign)->with(['userData'])->first();
        if (!empty($getData)) {

            $deviceToken = $getData['userData']['cm_firebase_token']??'';
            if($request->type == 'one'){
                $title = 'PICKUP OTP';
                $message =  "Your ride has arrived! 🚗 Share your Pickup OTP ".$getData['pickup_otp']." with the captain to start your journey.Please do not share this OTP with anyone else for your safety, Happy Journey !";
            }else{
                $title = 'DROP OTP';
                $message = "You’ve reached your destination! 🎉 Share your Drop OTP ".$getData['drop_opt']." with the captain to confirm the trip. Thank you for riding with us! Please do not share this OTP with anyone else for your safety.";
            }
            $web_config  = \App\Models\BusinessSetting::where('type','company_fav_icon')->first();
        $data = [
            'title' => $title,
            "description"=>$message,
            "image" => theme_asset(path: 'storage/app/public/company').'/'.$web_config['value'],
            'order_id' => 0,
            "type"=>"order",
        ];
        $response = \App\Utils\Helpers::send_push_notif_to_device1($deviceToken, $data);
            return response()->json(['status' => 0, 'message' => 'Send Otp', 'data' => $data], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
    }


    public function CabProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $user = TourAndTravel::where(['id' => $request->cab_assign])->first();
        if (!empty($user)) {
            $user['gst_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $user['gst_image'], type: 'backend-product');
            $user['pan_card_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $user['pan_card_image'], type: 'backend-product');
            $user['aadhaar_card_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $user['aadhaar_card_image'], type: 'backend-product');
            $user['address_proof_image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $user['address_proof_image'], type: 'backend-product');

            $user['image'] = getValidImage(path: 'storage/app/public/tour_and_travels/doc/' . $user['image'], type: 'backend-product');

            return response()->json(['status' => 1, 'message' => 'Company Information', 'data' => $user], 200);
        }
        return response()->json(['status' => 0, 'message' => 'Not Found Data', 'data' => []], 200);
    }

    public function CabProfileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 1)->where('status', 1)->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            "person_name" => "required",
            "bank_holder_name" => "required",
            "bank_name" => "required",
            "bank_branch" => "required",
            "ifsc_code" => "required",
            "account_number" => "required"
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            "person_name.required" => "Person name filed is Empty",
            "bank_holder_name.required" => "Bank Holder name filed is Empty",
            "bank_name.required" => "Bank name filed is Empty",
            "bank_branch.required" => "Bank branch name filed is Empty",
            "ifsc_code.required" => "ifsc code filed is Empty",
            "account_number.required" => "account number filed is Empty"
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $user = TourAndTravel::where(['id' => $request->cab_assign])->first();
        $user->person_name = $request->person_name;
        $user->bank_holder_name = $request->bank_holder_name;
        $user->bank_name = $request->bank_name;
        $user->bank_branch = $request->bank_branch;
        $user->ifsc_code = $request->ifsc_code;
        $user->account_number = $request->account_number;
        $user->save();
        return response()->json(['status' => 1, 'message' => 'update Successfully', 'data' =>[]], 200);
    }

    public function CabInactiveUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cab_assign' => ['required', function ($attribute, $value, $fail) {
                if (!TourAndTravel::where('id', $value)->where('is_approve', 0)->whereIn('status', [1,0])->exists()) {
                    $fail('The selected cab Id is invalid or inactive.');
                }
            },],
            'owner_name' => 'required',
            'company_name' => 'required',
            'phone_no' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'web_site_link' => 'required',
            'services' => 'required',
            'area_of_operation' => 'required',
            'person_name' => 'required',
            'person_phone' => 'required',
            'person_email' => 'required|email',
            'person_address' => 'required',
            'bank_holder_name' => 'required',
            'bank_name' => 'required',
            'bank_branch' => 'required',
            'ifsc_code' => 'required',
            'account_number' => 'required',
            'gst_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pan_card_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'aadhaar_card_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address_proof_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'cab_assign.required' => 'Cab Id is Empty!',
            'owner_name.required' => 'Owner Name is Empty!',
            'company_name.required' => 'Company Name is Empty!',
            'phone_no.required' => 'phone No is Empty!',
            'email.required' => 'Email is Empty!',
            'address.required' => 'Full Address is Empty!',
            'web_site_link.required' => 'web site Url is Empty!',
            'services.required' => 'services is Empty!',
            'area_of_operation.required' => 'area of operation is Empty!',
            "person_name.required" => "Person name filed is Empty",
            "person_phone.required" => "Person phone filed is Empty",
            "person_email.required" => "Person Email filed is Empty",
            "person_address.required" => "Person Address filed is Empty",
            "bank_holder_name.required" => "Bank Holder name filed is Empty",
            "bank_name.required" => "Bank name filed is Empty",
            "bank_branch.required" => "Bank branch name filed is Empty",
            "ifsc_code.required" => "ifsc code filed is Empty",
            "account_number.required" => "account number filed is Empty"
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => Helpers::error_processor($validator)[0]['message'], 'recode' => 0, 'data' => [], 'errors' => Helpers::error_processor($validator)], 200);
        }
        $user = TourAndTravel::where(['id' => $request->cab_assign])->first();
        $user->id = $request->cab_assign;
        $user->owner_name = $request->owner_name;
        $user->company_name = $request->company_name;
        $user->phone_no = $request->phone_no;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->web_site_link = $request->web_site_link;
        $user->services = $request->services;
        $user->area_of_operation = $request->area_of_operation;
        $user->person_name = $request->person_name;
        $user->person_phone = $request->person_phone;
        $user->person_email = $request->person_email;
        $user->person_address = $request->person_address;
        $user->bank_holder_name = $request->bank_holder_name;
        $user->bank_name = $request->bank_name;
        $user->bank_branch = $request->bank_branch;
        $user->ifsc_code = $request->ifsc_code;
        $user->account_number = $request->account_number;
        $user->gst_image =  (($request->file('gst_image'))? ImageManager::upload('tour_and_travels/doc/','png',$request['gst_image']):"" );
        $user->pan_card_image = (($request->file('pan_card_image'))? ImageManager::upload('tour_and_travels/doc/','png',$request['pan_card_image']) : "" );
        $user->aadhaar_card_image = (($request->file('aadhaar_card_image'))? ImageManager::upload('tour_and_travels/doc/','png',$request['aadhaar_card_image']) : "" );
        $user->address_proof_image = (($request->file('address_proof_image'))? ImageManager::upload('tour_and_travels/doc/','png',$request['address_proof_image']) : "" );
        $user->image = (($request->file('image'))? ImageManager::upload('tour_and_travels/doc/','png',$request['image']) : "" );

        $user->save();
        return response()->json(['status' => 1, 'message' => 'update Successfully', 'data' => []], 200);
    }

}