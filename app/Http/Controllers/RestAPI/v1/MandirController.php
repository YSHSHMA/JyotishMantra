<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\Purohit;
use App\Models\Temple;
use App\Models\TempleOrderDetails;
use App\Models\TempleServicePrice;
use App\Models\TempleServiceSlot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Utils\Helpers;
use Illuminate\Support\Facades\Hash;
use function App\Utils\payment_gateways;
use Carbon\Carbon;

class MandirController extends Controller
{
    public function detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $detail = Temple::select('id','name','short_description','details','state_id','city_id','opening_time','closeing_time','video_url','logo','package_service','trust_id','aadhaar_verify_status')
            ->with(['states','cities'])
            ->where('slug', $request->slug)
            ->where('status', 1)
            ->first();


        if($detail){
            return response()->json([
                'status' => true,
                'message' => 'Got data',
                'detail' => $detail
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Unable to get data',
        ]);
    }

    public function slider_images(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $temple = Temple::where('slug', $request->slug)
            ->where('status', 1)
            ->with('galleries2')
            ->first();

        if (!$temple || !$temple->galleries2) {
            return response()->json([
                'status' => false,
                'message' => 'Temple or gallery not found',
            ]);
        }

        $images = !empty($temple->galleries2->images) ? json_decode($temple->galleries2->images, true) : [];

        $mandirImages = [];

        foreach ($images as $image) {
            $mandirImages[] = getValidImage(
                'storage/app/public/temple/gallery/' . $image,
                type: 'product'
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Got data',
            'slider_images' => $mandirImages
        ]);
    }

    public function package(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required',
            'temple_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $packages = TempleServicePrice::where('package_id', $request->package_id)
            ->where('temple_id', $request->temple_id)
            ->where('status', 1)
            ->get();


        if($packages){
            return response()->json([
                'status' => true,
                'message' => 'Got data',
                'packages' => $packages
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Unable to get data',
        ]);
    }

    public function customer_check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = User::where('phone', '+91'.$request->mobile)->first();

        if ($user) {
            return response()->json([
                'status' => true,
                'message' => 'Got data',
                'user' => $user
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Unable to get data'
        ]);
    }

    public function booking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required',
            'service_type' => 'required',
            'mobile' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $serviceType = $request->service_type;
        $aadharNumber = $request->aadhar;

        $user = User::where('phone', '+91'.$request->mobile)->first();
        if (!$user) {
            $nameSplit = explode(' ', $request->name);
            $user = User::create([
                'name' => $request->name,
                'f_name' => $nameSplit[0] ?? null,
                'l_name' => $nameSplit[1] ?? null,
                'phone' => '+91'.$request->mobile,
                'email' => 'user@mahakal.com',
                'password' => Hash::make('12345678'),
            ]);
        }

        $package = TempleServicePrice::where('id', $request->service_id)->where('status', 1)->with(['temple','slots'])->first();
        $temple = Temple::where('id', $package->temple_id)->where('status', 1)->first();
        $purohits = Purohit::where('temple_id', $package->temple_id)->where('status', 1)->get();
        $paymentPublishedStatus = config('get_payment_publish_status');
        $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
        $payment_gateways_list = payment_gateways();
        $digital_payment = getWebConfig(name: 'digital_payment');

        $data = [
            'user'=>$user,
            'package'=>$package,
            'temple'=>$temple,
            'purohits'=>$purohits,
            'paymentPublishedStatus'=>$paymentPublishedStatus,
            'paymentGatewayPublishedStatus'=>$paymentGatewayPublishedStatus,
            'payment_gateways_list'=>$payment_gateways_list,
            'digital_payment'=>$digital_payment,
            'serviceType'=>$serviceType,
            'aadharNumber'=>$aadharNumber
        ];

        return response()->json([
            'status' => true,
            'message' => 'Got data',
            'booking' => $data
        ]);
    }

    public function get_package_timeslot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required',
            'day' => 'required',
            'date' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $currentDate = Carbon::today();
        $currentTime = Carbon::now()->format('H:i:s');
        
        $slotsQuery = TempleServiceSlot::where('temple_service_prices_id', $request->package_id)
            ->where('day_of_week', $request->day)
            ->where('is_available', 1)
            ->where('status', 1);
        
        if ($request->date && Carbon::parse($request->date)->isSameDay($currentDate)) {
            $slotsQuery->where('start_time', '>', $currentTime);
        }

        $slots = $slotsQuery->get(['id', 'slots_limi_capacity', 'start_time', 'end_time']);

        $formattedSlots = $slots->map(function ($slot) use ($request) {
            
            $timeSend = $slot->start_time . ' - ' . $slot->end_time;
            $timeRange = Carbon::parse($slot->start_time)->format('h:i A') . ' - ' .
                Carbon::parse($slot->end_time)->format('h:i A');

            $bookedOrders = TempleOrderDetails::where('type', $request->type)
                ->where('package_id', $request->packageId)
                ->where('booking_date', $request->date)
                ->where('time_slot', $timeSend)
                ->get();
            
            $bookedCount = 0;
            foreach ($bookedOrders as $order) {
                $customers = json_decode($order->customers, true);
                $bookedCount += is_array($customers) ? count($customers) : 0;
            }

            $available = max($slot->slots_limi_capacity - $bookedCount, 0);

            return [
                'id' => $slot->id,
                'time' => $timeRange,
                'limit' => $slot->slots_limi_capacity,
                'booked' => $bookedCount,
                'available' => $available,
            ];
        });

        if($formattedSlots){
            return response()->json([
                'status' => true,
                'message' => 'Got data',
                'formattedSlots' => $formattedSlots
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'unable to get data',
        ]);
    }
}
