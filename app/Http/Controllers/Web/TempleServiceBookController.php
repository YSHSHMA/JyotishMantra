<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Temple;
use App\Models\ServiceReview;
use App\Models\User;
use App\Models\TempleServicePackages;
use App\Models\TempleServicePrice;
use App\Models\TempleLeadMaster;
use App\Models\TempleLeadDetail;
use App\Models\TempleOrderMaster;
use App\Models\TempleOrderDetails;
use App\Models\TempleServiceSlot;
use Illuminate\Http\Request;
use App\Models\Purohit;
use App\Utils\CartManager;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use function App\Utils\payment_gateways;
use Illuminate\Support\Facades\Hash;


class TempleServiceBookController extends Controller
{

    public function mandir_site(Request $request, $slug)
    {
        $temple = Temple::where('slug', $slug)->where('status', 1)->with(['states','cities'])->first();
        if (!$temple) {
            abort(404, 'Temple not found');
        }
        $templeServices = [];
        if (!empty($temple->package_service)) {
            $decoded = json_decode($temple->package_service, true);
            $templeServices = collect($decoded)
                ->filter(fn($s) => isset($s['status']) && $s['status'] == 1)
                ->values()
                ->toArray();
        }
       
        $packages = TempleServicePrice::where('temple_id', $temple->id)->where('status', 1)->with(['slots'])->get()->groupBy('package_id');
        $purohits = Purohit::where('temple_id', $temple->id)->where('status', 1)->get();
        $paymentPublishedStatus = config('get_payment_publish_status');
        $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
        $payment_gateways_list = payment_gateways();
        $digital_payment = getWebConfig(name: 'digital_payment');
        return view('web-views.temple.temple-site', compact(
            'temple',
            'templeServices',
            'packages',
            'purohits',
            'paymentPublishedStatus',
            'paymentGatewayPublishedStatus',
            'payment_gateways_list',
            'digital_payment'
        ));
    }
    
    public function templeservicebookNow(Request $request, $slug)
    {
        $temple = Temple::where('slug', $slug)->where('status', 1)->first();
        if (!$temple) {
            abort(404, 'Temple not found');
        }
        $templeServices = [];
        if (!empty($temple->package_service)) {
            $decoded = json_decode($temple->package_service, true);
            $templeServices = collect($decoded)
                ->filter(fn($s) => isset($s['status']) && $s['status'] == 1)
                ->values()
                ->toArray();
        }
        $packages = TempleServicePrice::where('temple_id', $temple->id)->where('status', 1)->with(['slots'])->get()->groupBy('package_id');
        $purohits = Purohit::where('temple_id', $temple->id)->where('status', 1)->get();
        $paymentPublishedStatus = config('get_payment_publish_status');
        $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
        $payment_gateways_list = payment_gateways();
        $digital_payment = getWebConfig(name: 'digital_payment');
        return view('web-views.temple.temple-service', compact(
            'temple',
            'templeServices',
            'packages',
            'purohits',
            'paymentPublishedStatus',
            'paymentGatewayPublishedStatus',
            'payment_gateways_list',
            'digital_payment'
        ));
    }

    public function getPackageTimeSlots($packageId, Request $request)
    {
        $currentDate = Carbon::today();
        $currentTime = Carbon::now()->format('H:i:s');

        // Get all available slots for the given package and day
        $slotsQuery = TempleServiceSlot::where('temple_service_prices_id', $packageId)
            ->where('day_of_week', $request->day)
            ->where('is_available', 1)
            ->where('status', 1);

        // If it's today's date, show only future slots
        if ($request->date && Carbon::parse($request->date)->isSameDay($currentDate)) {
            $slotsQuery->where('start_time', '>', $currentTime);
        }

        $slots = $slotsQuery->get(['id', 'slots_limi_capacity', 'start_time', 'end_time']);

        $formattedSlots = $slots->map(function ($slot) use ($request, $packageId) {
            // Format time string
            $timeSend = $slot->start_time . ' - ' . $slot->end_time;
            $timeRange = Carbon::parse($slot->start_time)->format('h:i A') . ' - ' .
                Carbon::parse($slot->end_time)->format('h:i A');

            // 🔍 Find all bookings for this time slot, type, and date
            $bookedOrders = TempleOrderDetails::where('type', $request->type)
                ->where('package_id', $packageId)
                ->where('booking_date', $request->date)
                ->where('time_slot', $timeSend)
                ->get();

            // 🧮 Count total booked customers
            $bookedCount = 0;
            foreach ($bookedOrders as $order) {
                $customers = json_decode($order->customers, true);
                $bookedCount += is_array($customers) ? count($customers) : 0;
            }

            // ✅ Calculate available slots
            $available = max($slot->slots_limi_capacity - $bookedCount, 0);

            return [
                'id' => $slot->id,
                'time' => $timeRange,
                'limit' => $slot->slots_limi_capacity,
                'booked' => $bookedCount,
                'available' => $available,
            ];
        });

        return response()->json($formattedSlots);
    }

    public function mandirCustomerCheck(Request $request)
    {
        if (empty($request->mobile)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid mobile no']);
        }

        $user = User::where('phone', '+91'.$request->mobile)->first();

        if ($user) {
            return response()->json(['status' => 'success', 'user' => $user]);            
        }

        return response()->json(['status' => 'false', 'user' => null]);
    }

    public function mandirBooking(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'service_id' => 'required',
            'service_type' => 'required',
            'mobile' => 'required',
        ]);

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
        $temple = Temple::where('id', $package->temple_id)->where('status', 1)->with(['states','cities'])->first();
        if (!$temple) {
            abort(404, 'Temple not found');
        }
        $purohits = Purohit::where('temple_id', $package->temple_id)->where('status', 1)->get();
        $paymentPublishedStatus = config('get_payment_publish_status');
        $paymentGatewayPublishedStatus = isset($paymentPublishedStatus[0]['is_published']) ? $paymentPublishedStatus[0]['is_published'] : 0;
        $payment_gateways_list = payment_gateways();
        $digital_payment = getWebConfig(name: 'digital_payment');
        return view('web-views.temple.temple-site-booking', compact(
            'temple',
            'user',
            'package',
            'purohits',
            'paymentPublishedStatus',
            'paymentGatewayPublishedStatus',
            'payment_gateways_list',
            'digital_payment',
            'serviceType',
            'aadharNumber'
        ));
    }

    public function mandirpaymentsuccess($order_id, $qr)
    {
        $qrs = base64_decode($qr);
        $orderinfo = TempleOrderMaster::where('order_id', $order_id)->with(['temple'])->first();
        $orderDetails = TempleOrderDetails::where('order_id', $orderinfo->order_id)->first();
        $temple = Temple::where('id', $orderDetails->temple_id)->where('status', 1)->with(['states','cities'])->first();
        if (!$temple) {
            abort(404, 'Temple not found');
        }
        return view('web-views.temple.temple-site-success', compact('orderinfo', 'orderDetails', 'qrs','temple'));
    }

    public function mandirShowQrDetail(Request $request, $order_id)
    {
        $orderinfo = TempleOrderMaster::where('order_id', $order_id)->with(['temple'])->first();
        $orderDetails = TempleOrderDetails::where('order_id', $order_id)->first();
        if (auth('trust')->check()) {
            return redirect()->route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => "all-order", 'id' => $orderinfo['id']]);
        } elseif (auth('trust_employee')->check()) {
            return redirect()->route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => "all-order", 'id' => $orderinfo['id']]);
        }
        $token = explode(' ', $request->header('authorization'));
        if (count($token) > 1 && strlen($token[1]) > 30) {
            $seller = \App\Models\Seller::where(['auth_token' => $token['1']])->first();
            if (isset($seller)) {
                return redirect()->route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => "all-order", 'id' => $orderinfo['id']]);
            }
            $selleremployee = \App\Models\VendorEmployees::where(['auth_token' => $token['1']])->first();
            if (isset($selleremployee)) {
                return redirect()->route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => "all-order", 'id' => $orderinfo['id']]);
            }
        }
        $temple = Temple::where('id', $orderDetails->temple_id)->where('status', 1)->with(['states','cities'])->first();
        if (!$temple) {
            abort(404, 'Temple not found');
        }
        return view('web-views.temple.temple-site-order-detail', compact('orderinfo', 'orderDetails','temple'));
    }

    public function templeCustomerCheck(Request $request)
    {
        $data = $request->input('customer');

        if (!$data || empty($data['mobile'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid data']);
        }

        $user = User::where('phone', $data['mobile'])->first();

        if (!$user) {
            $nameSplit = explode(' ', $data['name']);
            $user = User::create([
                'name' => $data['name'],
                'f_name' => $nameSplit[0] ?? null,
                'l_name' => $nameSplit[1] ?? null,
                'phone' => $data['mobile'],
                'email' => 'user@mahakal.com',
                'password' => Hash::make('12345678'),
            ]);
        }

        return response()->json(['status' => 'success', 'user' => $user]);
    }

    public function templeServiceSave(Request $request)
    {
        $orderId = '';
        $typeOrderId = '';
        $lastOrder = TempleLeadMaster::select('id')->latest()->first();
        $lastId = !empty($lastOrder['id']) ? (100001 + $lastOrder['id']) : 100001;
        $leadmasterCheck = TempleLeadMaster::where('user_id', $request->user_id)
            ->where('payment_status', 0)
            ->where('status', 0)
            ->first();
        $packageData = TempleServicePrice::find($request->package_id);
        if ($packageData) {
            $basePrice   = $packageData->base_price * $request->customer_qty;
            $platformFee = $packageData->platform_fee_percentage * $request->customer_qty;
            $receiptFee  = $packageData->receipt_fee_percentage * $request->customer_qty;
            $perCustomerPrice = $basePrice;
            if ($request->type == 'bhojan' || $request->type == 'locker') {
                $gstAmount = 0;
            } else {
                $gstAmount = ($perCustomerPrice * $packageData->gst_rate) / 100;
            }
            $totalPricePerCustomer = $perCustomerPrice + $gstAmount + $platformFee + $receiptFee;
            $totalPrice = $totalPricePerCustomer;
        } else {
            $totalPrice = 0;
            $totalPricePerCustomer = 0;
        }

        if ($leadmasterCheck) {
            $orderId = $leadmasterCheck->order_id;
            TempleLeadMaster::where('order_id', $orderId)->update([
                'customer_qty' => $leadmasterCheck->customer_qty + $request->customer_qty,
                'amount'       => $leadmasterCheck->amount + $totalPrice,
                'payment_mode' => ($leadmasterCheck->amount + $totalPrice) > 0 ? 'cash' : 'free',
            ]);
        } else {
            // Create new order id
            $orderId = 'MCOM' . $lastId;
            $trustid = Temple::select('trust_id')->where('id', $request->temple_id)->first();

            // Create new lead
            TempleLeadMaster::create([
                'temple_id'    => $request->temple_id ?? null,
                'user_id'      => $request->user_id,
                'trust_id'     => $trustid['trust_id'] ?? null,
                'order_id'     => $orderId,
                'customer_qty' => $request->customer_qty,
                'amount'       => $totalPrice,
                'payment_mode' => $totalPrice > 0 ? 'cash' : 'free',
            ]);
        }

        // Lead details
        $leadDetailsCheck = TempleLeadDetail::where('order_id', $orderId)
            ->where('type', $request->type)
            ->exists();

        if (!$leadDetailsCheck) {
            if ($request->type == 'puja' && !empty($request->pandit_id)) {
                $panditId = $request->pandit_id;
            } elseif($request->type == 'puja') {
                $purohits = Purohit::where('temple_id', $request->temple_id)->where('status', 1) ->get();
                $panditId = $purohits->count() > 0 ? $purohits->random()->id : null;
            }else{
                $panditId = 0;
            }
            $timeSlotID = $request->type == 'darshan' || $request->type == 'bhojan' ? $request->time_slot_id : null;

            $typeOrderId = ($request->type == 'puja' ? 'PJ' : ($request->type == 'darshan' ? 'DS' : ($request->type == 'bhojan' ? 'BJ' : 'LK'))) . $lastId;


            TempleLeadDetail::create([
                'package_id'   => $request->package_id,
                'amount'       => $totalPrice,
                'booking_date' => $request->date,
                'order_id'     => $orderId,
                'type'         => $request->type,
                'type_order_id' => $typeOrderId,
                'customer_qty'  => $request->customer_qty,
                'customers'    => json_encode($request->customers),
                'pandit_id'    => $panditId,
                'time_slot_id' => $timeSlotID,
                'locker_items' => $request->locker_items ? json_encode($request->locker_items) : null,
            ]);

            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'error']);
    }

    public function GetLeadData(Request $request, $mobile)
    {
        $userData = User::select('id')->where('phone', $mobile)->first();
        if (!$userData) {
            return response()->json(['status' => 'error', 'message' => 'User not found']);
        }
        $leadGetinfo = TempleLeadMaster::where('user_id', $userData->id)->where('payment_status', 0)
            ->first();
        if (!$leadGetinfo) {
            return response()->json(['status' => 'error', 'message' => 'No pending leads found']);
        }
        $leadDetails = TempleLeadDetail::where('order_id', $leadGetinfo->order_id)->with(['package', 'timeslot'])->get();
        return response()->json([
            'status' => 'success',
            'user' => $userData,
            'lead' => $leadGetinfo,
            'details' => $leadDetails,
        ]);
    }

    public function templepaymentsuccess($order_id, $qr)
    {
        $qrs = base64_decode($qr);
        $orderinfo = TempleOrderMaster::where('order_id', $order_id)->with(['temple'])->first();
        $orderDetails = TempleOrderDetails::where('order_id', $orderinfo->order_id)->get();
        return view('web-views.temple.temple-success', compact('orderinfo', 'orderDetails', 'qrs'));
    }

    public function templeShowQrDetail(Request $request, $order_id)
    {
        $orderinfo = TempleOrderMaster::where('order_id', $order_id)->with(['temple'])->first();
        $orderDetails = TempleOrderDetails::where('order_id', $orderinfo->order_id)->get();
        if (auth('trust')->check()) {
            return redirect()->route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => "all-order", 'id' => $orderinfo['id']]);
        } elseif (auth('trust_employee')->check()) {
            return redirect()->route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => "all-order", 'id' => $orderinfo['id']]);
        }
        $token = explode(' ', $request->header('authorization'));
        if (count($token) > 1 && strlen($token[1]) > 30) {
            $seller = \App\Models\Seller::where(['auth_token' => $token['1']])->first();
            if (isset($seller)) {
                return redirect()->route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => "all-order", 'id' => $orderinfo['id']]);
            }
            $selleremployee = \App\Models\VendorEmployees::where(['auth_token' => $token['1']])->first();
            if (isset($selleremployee)) {
                return redirect()->route('trustees-vendor.recepit-management.recepit-qr-scanner', ['type' => "all-order", 'id' => $orderinfo['id']]);
            }
        }
        return view('web-views.temple.temple-order-detail', compact('orderinfo', 'orderDetails'));
    }
}
