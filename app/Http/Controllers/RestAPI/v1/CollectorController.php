<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Collector;
use App\Models\SDM;
use App\Models\SDMEmployee;
use App\Models\Temple;
use App\Models\TempleOrderDetails;
use App\Models\TempleOrderMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Utils\Helpers;
use Illuminate\Support\Facades\DB;

class CollectorController extends Controller
{
    // collector api
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $collector = Collector::where('mobile', $request->mobile)->first();

        if (!$collector) {
            return response()->json([
                'status' => false,
                'message' => 'Mobile number not registered'
            ]);
        }

        if ($collector->status == 0) {
            return response()->json(['status' => false, 'message' => 'Your account is not active yet!', 'token' => null]);
        }

        $token = $collector->createToken('CollectorAuthApp')->accessToken;
        return response()->json(['status' => true, 'message' => 'Login successful', 'token' => $token, 'type' => $collector->type]);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('collector_api')->check()) {
            $revoke = Auth::guard('collector_api')->user()->token()->revoke();
            if ($revoke) {
                return response()->json(['status' => true, 'message' => translate('logged_out_successfully')], 200);
            }
            return response()->json(['status' => false, 'message' => translate('unable_to_logout')], 403);
        }
        return response()->json(['status' => false, 'message' => translate('unauthorized_collector')], 403);
    }

    public function dashboard(Request $request)
    {
        if (Auth::guard('collector_api')->check()) {
            $collector = Auth::guard('collector_api')->user();
            if ($collector) {
                $totalUser = 0;
                $totalAmount = 0;
                $totalTemple = 0;
                $totalSDM = 0;

                $collectorTempleIds = json_decode($collector->temples, true) ?? [];
                $totalTemple = count($collectorTempleIds);

                $temples = Temple::select(
                    'id',
                    'name',
                    'state_id',
                    'city_id',
                    'thumbnail',
                    'opening_time',
                    'closeing_time'
                )
                    ->whereIn('id', $collectorTempleIds)
                    ->with(['cities:id,city'])
                    ->get();

                $temples = $temples->map(function ($temple) use ($collector) {

                    $temple->thumbnail = url('storage/app/public/temple/thumbnail/' . $temple->thumbnail);
                    $sdm = Collector::where('rel_collector_id', $collector->id)
                        ->whereJsonContains('temples', (string) $temple->id)
                        ->first();

                    $temple->sdm = $sdm;

                    return $temple;
                });

                $collector->temples_detail = $temples;

                $orders = TempleOrderMaster::select('id', 'order_id', 'total_people_count', 'total_amount', 'payment_mode')->whereIn('temple_id', $collectorTempleIds)->where('payment_status', 1)->get();

                foreach ($orders as $order) {
                    $totalUser += $order->total_people_count;
                    $totalAmount += $order->total_amount;
                }

                $totalSDM = Collector::where('rel_collector_id', $collector->id)->get()->count();

                $collector->total_temple = $totalTemple;
                $collector->total_user = $totalUser;
                $collector->total_amount = $totalAmount;
                $collector->total_sdm = $totalSDM;

                if ($collector) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Got Data',
                        'collector_detail' => $collector
                    ]);
                }
                return response()->json(['status' => false, 'message' => 'Unable to get collector detail']);
            }
            return response()->json(['status' => false, 'message' => 'Collector Data not found']);
        }
        return response()->json(['status' => false, 'message' => 'Unauthorize collector']);
    }

    public function temple_detail(Request $request)
    {
        if (Auth::guard('collector_api')->check()) {
            $collector = Auth::guard('collector_api')->user();

            if ($collector) {

                $validator = Validator::make($request->all(), [
                    'temple_id' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => Helpers::error_processor($validator)], 403);
                }

                $totalUser = 0;
                $verifiedUser = 0;
                $unVerifiedUser = 0;
                $totalAmount = 0;
                $cashAmount = 0;
                $onlineAmount = 0;
                $pujaAmount = 0;
                $darshanAmount = 0;
                $lockerAmount = 0;
                $bhojanAmount = 0;

                $temple = Temple::select(
                    'id',
                    'name',
                    'short_description',
                    'details',
                    'state_id',
                    'city_id',
                    'thumbnail',
                    'opening_time',
                    'closeing_time'
                )
                    ->where('id', $request->temple_id)
                    ->with(['states', 'cities'])
                    ->first();

                if (!$temple) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Temple not found'
                    ]);
                }
                $temple->thumbnail = url('storage/app/public/temple/thumbnail/' . $temple->thumbnail);

                $orders = TempleOrderMaster::select('id', 'order_id', 'total_people_count', 'total_amount', 'payment_mode')->where('temple_id', $request->temple_id)->where('payment_status', 1)->with('details')->get();

                foreach ($orders as $order) {
                    if ($order->payment_mode === 'cash') {
                        $cashAmount += $order->total_amount;
                    }

                    if ($order->payment_mode === 'online') {
                        $onlineAmount += $order->total_amount;
                    }

                    $totalUser += $order->total_people_count;
                    $totalAmount += $order->total_amount;
                    $pujaAmount += $order->details->where('type', 'puja')->sum('final_amount');
                    $darshanAmount += $order->details->where('type', 'darshan')->sum('final_amount');
                    $lockerAmount += $order->details->where('type', 'locker')->sum('final_amount');
                    $bhojanAmount += $order->details->where('type', 'bhojan')->sum('final_amount');
                }

                $orderDetails = TempleOrderDetails::select('id', 'customers')->where('temple_id', $request->temple_id)->where('payment_status', 1)->get();

                foreach ($orderDetails as $orderDetail) {
                    $customerDetails = $orderDetail['customers']?json_decode($orderDetail['customers']):[];
                    // print_r($customerDetails); die;
                    foreach($customerDetails as $detail){
                        if(isset($detail->verify_status) && $detail->verify_status==1){
                            $verifiedUser ++;
                        } else{
                            $unVerifiedUser ++;
                        }
                    }
                }

                if ($collector) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Got Data',
                        'temple' => $temple,
                        'collection_summary' => [
                            'total_user' => $totalUser,
                            'verified_user' => $verifiedUser,
                            'unverified_user' => $unVerifiedUser,
                            'total_amount' => $totalAmount,
                            'cash_amount' => $cashAmount,
                            'online_amount' => $onlineAmount,
                            'puja_amount' => $pujaAmount,
                            'darshan_amount' => $darshanAmount,
                            'locker_amount' => $lockerAmount,
                            'bhojan_amount' => $bhojanAmount,
                        ]
                    ]);
                }
                return response()->json(['status' => false, 'message' => 'Unable to get collector detail']);
            }
            return response()->json(['status' => false, 'message' => 'Collector Data not found']);
        }
        return response()->json(['status' => false, 'message' => 'Unauthorize collector']);
    }

    public function remaining_temple(Request $request)
    {
        if (Auth::guard('collector_api')->check()) {
            $collectorId = Auth::guard('collector_api')->user()->id;
            if ($collectorId) {

                $collectorTempleIds = Collector::where('id', $collectorId)
                    ->value('temples');

                $collectorTempleIds = json_decode($collectorTempleIds, true) ?? [];

                $sdmTempleIds = Collector::where('rel_collector_id', $collectorId)
                    ->pluck('temples')
                    ->toArray();

                $sdmTempleArray = [];

                foreach ($sdmTempleIds as $templesJson) {
                    $sdmTempleArray = array_merge(
                        $sdmTempleArray,
                        json_decode($templesJson, true) ?? []
                    );
                }

                $sdmTempleArray = array_unique($sdmTempleArray);

                $arrayFilter = array_values(
                    array_diff($collectorTempleIds, $sdmTempleArray)
                );

                $temples = Temple::select('id', 'name')
                    ->where('status', 1)
                    ->whereIn('id', $arrayFilter)
                    ->get();

                if ($temples) {
                    return response()->json(['status' => true, 'message' => 'Got Data', 'temples' => $temples]);
                }
                return response()->json(['status' => false, 'message' => 'Unable to get temples']);
            }
            return response()->json(['status' => false, 'message' => 'Collector Data not found']);
        }
        return response()->json(['status' => false, 'message' => 'Unauthorize collector']);
    }

    public function collectorDatewiseAmount(Request $request)
    {
        if (Auth::guard('collector_api')->check()) {

            $collector = Auth::guard('collector_api')->user();
            if (!$collector) {
                return response()->json(['status' => false, 'message' => 'Collector Data not found']);
            }

            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date'   => 'required|date'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            $totalAmount  = 0;
            $cashAmount   = 0;
            $onlineAmount = 0;

            $collectorTempleIds = json_decode($collector->temples, true) ?? [];

            // ✅ Get order IDs between date range
            $orderIds = TempleOrderDetails::whereIn('temple_id', $collectorTempleIds)
                ->whereBetween('booking_date', [$request->start_date, $request->end_date])
                ->where('payment_status', 1)
                ->where('status', 1)
                ->pluck('order_id')
                ->unique();

            // ✅ Fetch orders
            $orders = TempleOrderMaster::select('order_id', 'total_amount', 'payment_mode')
                ->whereIn('order_id', $orderIds)
                ->where('payment_status', 1)
                ->where('status', 1)
                ->get();

            foreach ($orders as $order) {

                $totalAmount += $order->total_amount;

                if ($order->payment_mode === 'cash') {
                    $cashAmount += $order->total_amount;
                }

                if ($order->payment_mode === 'online') {
                    $onlineAmount += $order->total_amount;
                }
            }

            // Attach calculated values
            $collector->cash_amount   = $cashAmount;
            $collector->online_amount = $onlineAmount;
            $collector->total_amount  = $totalAmount;

            return response()->json([
                'status' => true,
                'message' => 'Got Data',
                'collector_detail' => $collector
            ]);
        }

        return response()->json(['status' => false, 'message' => 'Unauthorize collector']);
    }


    //collector SDM list Start
    public function collectorSDM(Request $request)
    {
        if (!Auth::guard('collector_api')->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized collector'
            ]);
        }
        $collector = Auth::guard('collector_api')->user();
        if (!$collector) {
            return response()->json([
                'status' => false,
                'message' => 'Collector not found'
            ]);
        }
        //  Collector ke under jitne bhi SDM hain
        $sdmList = Collector::where('type', 'sdm')->where('rel_collector_id', $collector->id)->where('status', 1)->select('id', 'name', 'email', 'mobile', 'district', 'temples', 'created_at')->get();
        return response()->json([
            'status' => true,
            'message' => 'SDM list fetched successfully',
            'collector_id' => $collector->id,
            'total_sdm' => $sdmList->count(),
            'sdm_list' => $sdmList
        ]);
    }
    //collector SDM limt End
    public function SDMTempleList(Request $request)
    {
        if (!Auth::guard('collector_api')->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized collector'
            ]);
        }

        if (!$request->collector_id || !$request->sdm_id) {
            return response()->json([
                'status' => false,
                'message' => 'collector_id and sdm_id are required'
            ]);
        }

        $authCollector = Auth::guard('collector_api')->user();

        // Collector ID match check
        if ($authCollector->id != $request->collector_id) {
            return response()->json([
                'status' => false,
                'message' => 'Collector ID mismatch'
            ]);
        }

        // 🔹 SDM verify (isi collector ka ho)
        $sdm = Collector::where('id', $request->sdm_id)
            ->where('type', 'sdm')
            ->where('rel_collector_id', $request->collector_id)
            ->where('status', 1)
            ->first();

        if (!$sdm) {
            return response()->json([
                'status' => false,
                'message' => 'SDM not found under this collector'
            ]);
        }

        // 🔹 SDM ke temple IDs
        $templeIds = json_decode($sdm->temples ?? '[]', true);

        $temples = [];

        if (!empty($templeIds)) {
            $temples = Temple::whereIn('id', $templeIds)->with('translations')
                ->select(
                    'id',
                    'name',
                    'thumbnail',
                    'opening_time',
                    'closeing_time'
                )
                ->get()
                ->map(function ($temple) {
                    $temple->thumbnail = $temple->thumbnail
                        ? url('storage/app/public/temple/thumbnail/' . $temple->thumbnail)
                        : null;
                    return $temple;
                });
        }

        return response()->json([
            'status' => true,
            'message' => 'SDM temple list fetched successfully',
            'collector_id' => (int) $request->collector_id,
            'sdm_id' => $sdm->id,
            'sdm_name' => $sdm->name,
            'total_temples' => count($templeIds),
            'temple_list' => $temples
        ]);
    }

    public function sdm_store(Request $request)
    {
        if (Auth::guard('collector_api')->check()) {
            $collectorId = Auth::guard('collector_api')->user()->id;
            if ($collectorId) {

                $mobile = $request->mobile;
                if (!str_starts_with($mobile, '+91')) {
                    $mobile = '+91' . $mobile;
                }
                $request->merge([
                    'mobile' => $mobile
                ]);

                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:collectors,email',
                    'mobile' => 'required|unique:collectors,mobile',
                    'password' => 'required',
                    'temples'  => 'required|array|min:1',
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => Helpers::error_processor($validator)], 403);
                }

                $sdmStore = new Collector;;
                $sdmStore->name = $request->name;
                $sdmStore->type = 'sdm';
                $sdmStore->rel_collector_id = $collectorId;
                $sdmStore->email = $request->email;
                $sdmStore->mobile = $request->mobile;
                $sdmStore->password = bcrypt($request->password);
                $sdmStore->temples = json_encode($request->temples);
                if ($sdmStore->save()) {
                    return response()->json(['status' => true, 'message' => 'SDM stored succesfully', 'sdm' => $sdmStore]);
                }
                return response()->json(['status' => false, 'message' => 'Unable to store SDM']);
            }
            return response()->json(['status' => false, 'message' => 'Collector Data not found']);
        }
        return response()->json(['status' => false, 'message' => 'Unauthorize collector']);
    }

    // sdm api
    public function sdm_dashboard(Request $request)
    {
        if (Auth::guard('collector_api')->check()) {
            $sdm = Auth::guard('collector_api')->user();
            if ($sdm) {
                $totalUser = 0;
                $totalAmount = 0;
                $totalTemple = 0;
                $totalEmployee = 0;

                $sdmTempleIds = json_decode($sdm->temples, true) ?? [];
                $totalTemple = count($sdmTempleIds);

                $temples = Temple::select(
                    'id',
                    'name',
                    'state_id',
                    'city_id',
                    'thumbnail',
                    'opening_time',
                    'closeing_time'
                )
                    ->whereIn('id', $sdmTempleIds)
                    ->with(['states', 'cities'])
                    ->get();

                $temples = $temples->map(function ($temple) use ($sdm) {

                    $temple->thumbnail = url('storage/app/public/temple/thumbnail/' . $temple->thumbnail);
                    $employee = Collector::where('rel_sdm_id', $sdm->id)
                        ->whereJsonContains('temples', (string) $temple->id)
                        ->first();

                    $temple->employee = $employee;

                    return $temple;
                });

                $sdm->temples_detail = $temples;

                $orders = TempleOrderMaster::select('id', 'order_id', 'total_people_count', 'total_amount', 'payment_mode')->whereIn('temple_id', $sdmTempleIds)->where('payment_status', 1)->get();

                foreach ($orders as $order) {
                    $totalUser += $order->total_people_count;
                    $totalAmount += $order->total_amount;
                }

                $totalEmployee = Collector::where('rel_sdm_id', $sdm->id)->get()->count();

                $sdm->total_temple = $totalTemple;
                $sdm->total_user = $totalUser;
                $sdm->total_amount = $totalAmount;
                $sdm->total_employee = $totalEmployee;

                if ($sdm) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Got Data',
                        'sdm_detail' => $sdm
                    ]);
                }
                return response()->json(['status' => false, 'message' => 'Unable to get sdm detail']);
            }
            return response()->json(['status' => false, 'message' => 'SDM Data not found']);
        }
        return response()->json(['status' => false, 'message' => 'Unauthorize sdm']);
    }

    public function sdmDatewiseAmount(Request $request)
    {
        if (Auth::guard('collector_api')->check()) {

            $sdm = Auth::guard('collector_api')->user();
            if (!$sdm) {
                return response()->json(['status' => false, 'message' => 'SDM Data not found']);
            }

            $validator = Validator::make($request->all(), [
                'temple_id' => 'required',
                'start_date' => 'required|date',
                'end_date'   => 'required|date'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            $totalAmount  = 0;
            $cashAmount   = 0;
            $onlineAmount = 0;

            // $sdmTempleIds = json_decode($sdm->temples, true) ?? [];

            // ✅ Get order IDs between date range
            $orderIds = TempleOrderDetails::where('temple_id', $request->temple_id)
                ->whereBetween('booking_date', [$request->start_date, $request->end_date])
                ->where('payment_status', 1)
                ->where('status', 1)
                ->pluck('order_id')
                ->unique();

            // ✅ Fetch orders
            $orders = TempleOrderMaster::select('order_id', 'total_amount', 'payment_mode')
                ->whereIn('order_id', $orderIds)
                ->where('payment_status', 1)
                ->where('status', 1)
                ->get();

            foreach ($orders as $order) {

                $totalAmount += $order->total_amount;

                if ($order->payment_mode === 'cash') {
                    $cashAmount += $order->total_amount;
                }

                if ($order->payment_mode === 'online') {
                    $onlineAmount += $order->total_amount;
                }
            }

            // Attach calculated values
            $sdm->cash_amount   = $cashAmount;
            $sdm->online_amount = $onlineAmount;
            $sdm->total_amount  = $totalAmount;

            return response()->json([
                'status' => true,
                'message' => 'Got Data',
                'sdm_detail' => $sdm
            ]);
        }

        return response()->json(['status' => false, 'message' => 'Unauthorize SDM']);
    }

    public function sdm_temple_detail(Request $request)
    {
        if (Auth::guard('collector_api')->check()) {
            $sdm = Auth::guard('collector_api')->user();

            if ($sdm) {

                $validator = Validator::make($request->all(), [
                    'temple_id' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => Helpers::error_processor($validator)], 403);
                }

                $totalUser = 0;
                $totalAmount = 0;
                $cashAmount = 0;
                $onlineAmount = 0;
                $pujaAmount = 0;
                $darshanAmount = 0;
                $lockerAmount = 0;
                $bhojanAmount = 0;

                $temple = Temple::select(
                    'id',
                    'name',
                    'short_description',
                    'details',
                    'state_id',
                    'city_id',
                    'thumbnail',
                    'opening_time',
                    'closeing_time'
                )
                    ->where('id', $request->temple_id)
                    ->with(['states', 'cities'])
                    ->first();

                if (!$temple) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Temple not found'
                    ]);
                }
                $temple->thumbnail = url('storage/app/public/temple/thumbnail/' . $temple->thumbnail);

                $orders = TempleOrderMaster::select('id', 'order_id', 'total_people_count', 'total_amount', 'payment_mode')->where('temple_id', $request->temple_id)->where('payment_status', 1)->with('details')->get();

                foreach ($orders as $order) {
                    if ($order->payment_mode === 'cash') {
                        $cashAmount += $order->total_amount;
                    }

                    if ($order->payment_mode === 'online') {
                        $onlineAmount += $order->total_amount;
                    }

                    $totalUser += $order->total_people_count;
                    $totalAmount += $order->total_amount;
                    $pujaAmount += $order->details->where('type', 'puja')->sum('final_amount');
                    $darshanAmount += $order->details->where('type', 'darshan')->sum('final_amount');
                    $lockerAmount += $order->details->where('type', 'locker')->sum('final_amount');
                    $bhojanAmount += $order->details->where('type', 'bhojan')->sum('final_amount');
                }

                if ($sdm) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Got Data',
                        'temple' => $temple,
                        'collection_summary' => [
                            'total_user' => $totalUser,
                            'total_amount' => $totalAmount,
                            'cash_amount' => $cashAmount,
                            'online_amount' => $onlineAmount,
                            'puja_amount' => $pujaAmount,
                            'darshan_amount' => $darshanAmount,
                            'locker_amount' => $lockerAmount,
                            'bhojan_amount' => $bhojanAmount,
                        ]
                    ]);
                }
                return response()->json(['status' => false, 'message' => 'Unable to get sdm detail']);
            }
            return response()->json(['status' => false, 'message' => 'SDM Data not found']);
        }
        return response()->json(['status' => false, 'message' => 'Unauthorize sdm']);
    }

    public function sdm_remaining_temple(Request $request)
    {
        if (Auth::guard('collector_api')->check()) {
            $sdmId = Auth::guard('collector_api')->user()->id;
            if ($sdmId) {

                $sdmTempleIds = Collector::where('id', $sdmId)
                    ->value('temples');

                $sdmTempleIds = json_decode($sdmTempleIds, true) ?? [];

                $employeeTempleIds = Collector::where('rel_sdm_id', $sdmId)
                    ->pluck('temples')
                    ->toArray();

                $employeeTempleArray = [];

                foreach ($employeeTempleIds as $templesJson) {
                    $employeeTempleArray = array_merge(
                        $employeeTempleArray,
                        json_decode($templesJson, true) ?? []
                    );
                }

                $employeeTempleArray = array_unique($employeeTempleArray);

                $arrayFilter = array_values(
                    array_diff($sdmTempleIds, $employeeTempleArray)
                );

                $temples = Temple::select('id', 'name')
                    ->where('status', 1)
                    ->whereIn('id', $arrayFilter)
                    ->get();

                if ($temples) {
                    return response()->json(['status' => true, 'message' => 'Got Data', 'temples' => $temples]);
                }
                return response()->json(['status' => false, 'message' => 'Unable to get temples']);
            }
            return response()->json(['status' => false, 'message' => 'SDM Data not found']);
        }
        return response()->json(['status' => false, 'message' => 'Unauthorize SDM']);
    }

    public function employee_store(Request $request)
    {
        if (Auth::guard('collector_api')->check()) {
            $sdmId = Auth::guard('collector_api')->user()->id;
            if ($sdmId) {

                $mobile = $request->mobile;
                if (!str_starts_with($mobile, '+91')) {
                    $mobile = '+91' . $mobile;
                }
                $request->merge([
                    'mobile' => $mobile
                ]);

                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required|email|unique:collectors,email',
                    'mobile' => 'required|unique:collectors,mobile',
                    'password' => 'required',
                    'temples'  => 'required|array|min:1',
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => Helpers::error_processor($validator)], 403);
                }

                $employeeStore = new Collector;
                $employeeStore->name = $request->name;
                $employeeStore->type = 'sdm-employee';
                $employeeStore->rel_sdm_id = $sdmId;
                $employeeStore->email = $request->email;
                $employeeStore->mobile = $request->mobile;
                $employeeStore->password = bcrypt($request->password);
                $employeeStore->temples = json_encode($request->temples);
                if ($employeeStore->save()) {
                    return response()->json(['status' => true, 'message' => 'SDM employee stored succesfully', 'sdm' => $employeeStore]);
                }
                return response()->json(['status' => false, 'message' => 'Unable to store SDM Employee']);
            }
            return response()->json(['status' => false, 'message' => 'SDM Data not found']);
        }
        return response()->json(['status' => false, 'message' => 'Unauthorize SDM']);
    }

    public function SDMEmployeeList(Request $request) {
        if (!Auth::guard('collector_api')->check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $sdm = Auth::guard('collector_api')->user();

        // Ensure logged-in user is SDM
        if (!$sdm || $sdm->type !== 'sdm') {
            return response()->json([
                'status' => false,
                'message' => 'Only SDM can access employee list'
            ]);
        }

        // SDM ke under jitne bhi employees hain
        $employees = Collector::where('type', 'sdm-employee')->where('rel_sdm_id', $sdm->id)->where('status', 1)
            ->select('id','name','email','mobile','district','temples','created_at')->get();

        return response()->json([
            'status'        => true,
            'message'       => 'SDM employee list fetched successfully',
            'sdm_id'        => $sdm->id,
            'total_employee'=> $employees->count(),
            'employee_list' => $employees
        ]);
    }
    
    // sdm employee api
    public function sdm_employee_dashboard(Request $request)
    {
        if (Auth::guard('collector_api')->check()) {
            $sdmEmployee = Auth::guard('collector_api')->user();
            if ($sdmEmployee) {

                $sdmEmployeeTempleIds = json_decode($sdmEmployee->temples, true) ?? [];

                $temples = Temple::select(
                    'id',
                    'name',
                    'state_id',
                    'city_id',
                    'thumbnail',
                    'opening_time',
                    'closeing_time'
                )
                    ->whereIn('id', $sdmEmployeeTempleIds)
                    ->with(['states', 'cities'])
                    ->get();

                $temples = $temples->map(function ($temple) {
                    $temple->thumbnail = url('storage/app/public/temple/thumbnail/' . $temple->thumbnail);
                    return $temple;
                });

                $sdmEmployee->temples_detail = $temples;

                if ($sdmEmployee) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Got Data',
                        'sdm_employee_detail' => $sdmEmployee
                    ]);
                }
                return response()->json(['status' => false, 'message' => 'Unable to get sdm employee detail']);
            }
            return response()->json(['status' => false, 'message' => 'SDM employee Data not found']);
        }
        return response()->json(['status' => false, 'message' => 'Unauthorize sdm employee']);
    }

    public function sdm_employee_temple_detail(Request $request)
    {
        if (Auth::guard('collector_api')->check()) {
            $sdmEmployee = Auth::guard('collector_api')->user();

            if ($sdmEmployee) {

                $validator = Validator::make($request->all(), [
                    'temple_id' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => Helpers::error_processor($validator)], 403);
                }

                $totalUser = 0;
                $totalAmount = 0;
                $cashAmount = 0;
                $onlineAmount = 0;
                $pujaAmount = 0;
                $darshanAmount = 0;
                $lockerAmount = 0;
                $bhojanAmount = 0;

                $temple = Temple::select(
                    'id',
                    'name',
                    'short_description',
                    'details',
                    'state_id',
                    'city_id',
                    'thumbnail',
                    'opening_time',
                    'closeing_time'
                )
                    ->where('id', $request->temple_id)
                    ->with(['states', 'cities'])
                    ->first();

                if (!$temple) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Temple not found'
                    ]);
                }
                $temple->thumbnail = url('storage/app/public/temple/thumbnail/' . $temple->thumbnail);

                $orders = TempleOrderMaster::select('id', 'order_id', 'total_people_count', 'total_amount', 'payment_mode')->where('temple_id', $request->temple_id)->where('payment_status', 1)->with('details')->get();

                foreach ($orders as $order) {
                    if ($order->payment_mode === 'cash') {
                        $cashAmount += $order->total_amount;
                    }

                    if ($order->payment_mode === 'online') {
                        $onlineAmount += $order->total_amount;
                    }

                    $totalUser += $order->total_people_count;
                    $totalAmount += $order->total_amount;
                    $pujaAmount += $order->details->where('type', 'puja')->sum('final_amount');
                    $darshanAmount += $order->details->where('type', 'darshan')->sum('final_amount');
                    $lockerAmount += $order->details->where('type', 'locker')->sum('final_amount');
                    $bhojanAmount += $order->details->where('type', 'bhojan')->sum('final_amount');
                }

                if ($sdmEmployee) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Got Data',
                        'temple' => $temple,
                        'collection_summary' => [
                            'total_user' => $totalUser,
                            'total_amount' => $totalAmount,
                            'cash_amount' => $cashAmount,
                            'online_amount' => $onlineAmount,
                            'puja_amount' => $pujaAmount,
                            'darshan_amount' => $darshanAmount,
                            'locker_amount' => $lockerAmount,
                            'bhojan_amount' => $bhojanAmount,
                        ]
                    ]);
                }
                return response()->json(['status' => false, 'message' => 'Unable to get sdm detail']);
            }
            return response()->json(['status' => false, 'message' => 'SDM Data not found']);
        }
        return response()->json(['status' => false, 'message' => 'Unauthorize sdm']);
    }
}
