<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Models\Chadhava_orders;
use App\Models\PoojaForecast;
use App\Models\SellerWallet;
use App\Models\Service;
use App\Models\Service_order;
use App\Models\Vippooja;
use Illuminate\Http\Request;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

class LiveStreamController extends Controller
{

    public function LiveStreamNow(Request $request, $streamKey)
    {
        $liveKey = $streamKey;
        $orders = Service_order::where('live_stream', 'https://mahakal.com/live-stream/'.$liveKey)->get();
        $allData = collect();
        $isComplete = false;
        foreach ($orders as $order) {
            $serviceId   = $order->service_id;
            $bookingDate = $order->booking_date;
            $isComplete = $order->is_completed == 1 ? true : false;
            if ($order->type === 'pooja') {
                $serviceCheck = Service::where('id', $serviceId)
                    ->where('status', 1)
                    ->where('product_type', 'pooja')
                    ->first();
                if ($serviceCheck) {
                    $data = Service_order::where('service_id', $serviceId)
                        ->where('booking_date', $bookingDate)
                        ->with(['services', 'customers', 'payments'])
                        ->get();
                    $allData = $allData->merge($data);
                }
            } elseif (in_array($order->type, ['vip', 'anushthan'])) {
                $serviceCheck = Service::where('id', $serviceId)
                    ->where('status', 1)
                    ->first();
                if ($serviceCheck) {
                    $data = Vippooja::where('service_id', $serviceId)
                        ->where('booking_date', $bookingDate)
                        ->with(['services', 'customers', 'payments'])
                        ->get();
                    $allData = $allData->merge($data);
                }
            }
        }
        $pujaList = PoojaForecast::with(['service' => function ($query) {
                    $query->where('status', 1)->where('product_type', 'pooja');
                }
            ])->whereIn('type', ['weekly', 'special'])->orderBy('booking_date', 'asc')->withCount('PoojaOrderReview')->withAvg('review', 'rating')->where('is_expired', 0)->get()
            ->filter(function ($item) {
                return $item->service;
            })->unique('service_id')->values();
        
        return view('web-views.streamLive.livestream', [
            'liveKey'  => $liveKey,
            'pujaList' => $pujaList,
            'allData'  => $allData,
            'iscomplete' => $isComplete
        ]);
    }


}