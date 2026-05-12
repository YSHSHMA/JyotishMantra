<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use App\Models\Astrologer\Astrologer as Guruji;
use App\Models\Service_order;
use App\Models\Chadhava_orders;
use App\Models\Service;
use App\Models\User;
use App\Models\PanditServiceDetail;
use App\Models\Chadhava;
use App\Models\PanditPriceSlab;
use App\Models\Vippooja;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    
    public function PujaList(Request $request, $id)
    {
        $vendor = Guruji::find($id);
        if (!$vendor) {
            Toastr::warning(translate('profile_not_found'));
            return redirect()->route('guruji.dashboard');
        }
        $orders = Service_order::where('type', 'pooja')->where('status', 0)->where('is_block', '!=', 9)->where('is_completed', 0)->where('payment_status', 1)->where('pandit_assign',$vendor->id)->with(['packages', 'services', 'customers', 'pandit'])
            ->selectRaw('service_id, COUNT(*) as total_orders, pandit_assign, booking_date, COUNT(created_at) as booking_count, SUM(pay_amount) - SUM(COALESCE(coupon_amount, 0)) AS total_amount, COALESCE(GROUP_CONCAT(DISTINCT members SEPARATOR "|"), "") as members, order_status, package_id, created_at, id')
            ->groupBy('service_id', 'booking_date', 'pandit_assign')
            ->orderBy('total_orders', 'DESC')
            ->get();
        $users = User::all();
        $ordersCount = Service_order::where('status', 0)
            ->where('pandit_assign', $vendor->id)
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $pooja = $ordersCount['pooja'] ?? 0;
        $vippooja = $ordersCount['vip'] ?? 0;
        $anusthan = $ordersCount['anushthan'] ?? 0;
        $counselling = $ordersCount['counselling'] ?? 0;
        $personalpooja = $ordersCount['panditpooja'] ?? 0;
        $personalcounseeling = $ordersCount['panditcounselling'] ?? 0;

        return view('guruji-views.orders.puja.list',compact('orders','vendor','pooja','vippooja','anusthan','counselling','personalpooja','personalcounseeling'));

    }


    public function detailsMembers(Request $request, $id){
        return view('guruji-views.orders.puja.orders-details-members');
    }
    public function OrderDetails(Request $request, $booking_date, $service_id, $status, $id)
    {
        $vendor = Guruji::findOrFail($id);
        $service = Service::findOrFail($service_id);
        $serviceId = Service_order::where('booking_date', $booking_date)->where('service_id', $service_id)->where('is_block', '!=', 9)->where('pandit_assign',$vendor->id)->value('service_id');
        $panditIds = PanditPriceSlab::where('service_id', $serviceId)->where('type', 'pooja')->where('status', 1)->pluck('pandit_id')
        ->unique()->toArray();
        
        if ($status == 1) {
            $details = Service_order::where('service_id', $service_id)
            ->where('booking_date', $booking_date)->where('pandit_assign',$vendor->id)
            ->where('status', 1)->where('type', 'pooja')->where('is_block', '!=', 9)->with(['services','packages','pandit'])
                ->selectRaw('service_id,COUNT(*) as total_orders,
                    pandit_assign,booking_date,
                    COUNT(booking_date) as booking_count,
                    SUM(pay_amount) - SUM(COALESCE(coupon_amount, 0)) AS total_amount,
                    GROUP_CONCAT(COALESCE(order_id, "") SEPARATOR "|") as order_id,
                    GROUP_CONCAT(COALESCE(members, "") SEPARATOR "|") as members,
                    GROUP_CONCAT(COALESCE(gotra, "") SEPARATOR "|") as gotra,
                    GROUP_CONCAT(COALESCE(leads_id, "") SEPARATOR ",") as leads,
                    order_status, package_id, created_at,
                    schedule_time, schedule_created,
                    live_stream, live_created_stream,
                    pooja_video, video_created_sharing,
                    reject_reason, pooja_certificate,
                    order_completed, order_canceled, order_canceled_reason
                ')->groupBy('service_id', 'booking_date')
                ->first();
            } else {
                $details = Service_order::where('service_id', $service_id)
                ->where('booking_date', $booking_date)->where('pandit_assign',$vendor->id)
                ->where('status', $status)->where('payment_status', 1)->where('type', 'pooja')
                ->where('is_block', '!=', 9)->with(['services','pandit'])->selectRaw('
                    service_id,COUNT(*) as total_orders,pandit_assign,
                    booking_date,COUNT(booking_date) as booking_count,
                    SUM(pay_amount) - SUM(COALESCE(coupon_amount, 0)) AS total_amount,
                    GROUP_CONCAT(COALESCE(order_id, "") SEPARATOR "|") as order_id,
                    GROUP_CONCAT(COALESCE(members, "") SEPARATOR "|") as members,
                    GROUP_CONCAT(COALESCE(gotra, "") SEPARATOR "|") as gotra,
                    GROUP_CONCAT(COALESCE(leads_id, "") SEPARATOR ",") as leads,
                    order_status, created_at,schedule_time, schedule_created,
                    live_stream, live_created_stream,payment_status,pooja_video, video_created_sharing,
                    reject_reason, pooja_certificate,
                    order_completed, order_canceled, order_canceled_reason
            ')->groupBy('service_id', 'booking_date')->first();
            // dd($details);
        }

        return view(
            'guruji-views.orders.puja.orders-details',
            compact('details', 'service', 'vendor')
        );
    }
    // Single Order Status Time,Live,Pooja
    public function status_times($id, Request $request)
    {
        // dd($request->all());
        $processedCustomers = [];
        $orders = Service_order::where('indivisual',1)->where('type', 'pooja')->where('is_block', '!=', 9)->where('pandit_id', $id)->where('payment_status', 1)->where('status', 0)
        ->with(['services', 'customers', 'customer'])->get();
        $order = $orders->first();
        if (!$order) {
            Toastr::error('Order not found or invalid order status.');
            return back();
        }
        $serviceId = $order->service_id;
        $addressList = PanditServiceDetail::where('service_id', $serviceId)->where('pandit_id', $order->pandit_assign)
            ->get()->keyBy('service_id');
        $services = Service::where('id', $serviceId)->get();
        foreach ($services as $service) {
            if (isset($addressList[$service->id]) && !empty($addressList[$service->id]->address)) {
                $service->final_venue = $addressList[$service->id]->address;
            } else {
                $service->final_venue = $service->pooja_venue;
            }
        }
        foreach ($orders as $order) {
            $order->booking_date = $request->booking_date;
            $order->schedule_time = $request->schedule_time;
            $order->order_status = $request->order_status;
            $order->schedule_created = now();
            $order->save();
        }
        foreach ($orders as $order) {
            if ($order->customers) {
                $hasCompleted = Service_order::where('service_id', $order->service_id)
                    ->where('customer_id', $order->customer_id)->where('status', 1)->exists();
                if ($hasCompleted) {
                    continue; 
                }
                if (in_array($order->customer_id, $processedCustomers)) {
                    continue;
                }
                $messageData = [
                    'service_name'   => $service->name ?? 'N/A',
                    'puja_venue'     => $service->final_venue ?? 'N/A',   // correct!
                    'scheduled_time' => $request->schedule_time ? date('h:i A', strtotime($request->schedule_time)) 
                                            : 'N/A',
                    'booking_date'   => $order->booking_date ? date('d-m-Y', strtotime($order->booking_date)) 
                                            : 'N/A',
                    'orderId'        => $order->order_id,
                    'customer_id'    => $order->customer_id,
                ];               
                SendWhatsappMessage::dispatch('whatsapp', 'Pandit_Schedule', $messageData);
                $processedCustomers[] = $order->customer_id;
                $userInfo = \App\Models\User::where('id', $order->customer_id)->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $service_name = \App\Models\Service::where('id', $order->service_id)
                        ->where('product_type', 'pooja')
                        ->first();
                    $bookingDetails = \App\Models\Service_order::where('service_id', ($order->service_id ?? ""))
                        ->where('type', 'panditpooja')->where('booking_date', ($order->booking_date ?? ""))
                        ->where('customer_id', ($order->customer_id ?? ""))->where('order_id', ($order->order_id ?? ""))
                        ->first();
                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Puja Time Scheduled',
                        'htmlContent' =>
                        \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-schedule-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];
                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }
        Toastr::success(translate('Booking_date_and_Schedule_time_changed_successfully'));
        return back();
    }

    public function live_streams($id, Request $request)
    {
        $processedCustomers = [];
        $orders = Service_order::where('indivisual',1)->where('type', 'panditpooja')->where('is_block', '!=', 9)->where('id', $id)->where('payment_status', 1)->where('status', 0)->with(['services', 'customers', 'customer'])->get();        $order = $orders->first();
        $serviceId = $order->service_id;
        $addressList = PanditServiceDetail::where('service_id', $serviceId)->where('pandit_id', $order->pandit_assign)
            ->get()->keyBy('service_id');
        $services = Service::where('id', $serviceId)->get();
        foreach ($services as $service) {
            if (isset($addressList[$service->id]) && !empty($addressList[$service->id]->address)) {
                $service->final_venue = $addressList[$service->id]->address;
            } else {
                $service->final_venue = $service->pooja_venue;
            }
        }
        foreach ($orders as $order) {
            $order->live_stream = $request->live_stream;
            $order->order_status = $request->order_status;
            $order->live_created_stream = now();
            $order->save();
        }
        foreach ($orders as $order) {
            if ($order->customers) {
                $hasCompleted = Service_order::where('service_id', $order->service_id)
                    ->where('customer_id', $order->customer_id)->where('status', 1)->exists();
                if ($hasCompleted) {
                    continue; 
                }
                if (in_array($order->customer_id, $processedCustomers)) {
                    continue;
                }
            
                $messageData = [
                    'service_name'   => $service->name,
                    'live_stream'    => $order->live_stream ?? 'mahakal.com',
                    'puja_venue'     => $service->final_venue ?? 'N/A',
                    'scheduled_time' => date('h:i A', strtotime($order->schedule_time)) ?? 'N/A',
                    'booking_date'   => date('d-m-Y', strtotime($order->booking_date)),
                    'customer_id'    => $order->customer_id,
                    'orderId'        => $order->order_id,
                ];

                // Send WhatsApp message using the helper function
                SendWhatsappMessage::dispatch('whatsapp', 'Pandit Live Stream', $messageData);
                $processedCustomers[] = $order->customer_id;
                $userInfo = \App\Models\User::where('id', $order->customer_id)->first();
                $service_name = \App\Models\Service::where('id', $order->service_id)->where('product_type', 'pooja')->first();
                $bookingDetails = \App\Models\Service_order::where('service_id', ($order->service_id ?? ""))
                    ->where('type', 'panditpooja')->where('booking_date', ($order->booking_date ?? ""))->where('customer_id', ($order->customer_id ?? ""))
                    ->where('order_id', ($order->order_id ?? ""))->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Puja Live Now',
                        'htmlContent' =>
                        \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-live-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];
                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }
        // dd($orders);
        Toastr::success(translate('live_stream_changed_successfully'));
        return back();
    }

    public function pooja_videos($id, Request $request)
    {
        $processedCustomers = [];
        $orders = Service_order::where('indivisual',1)->where('type', 'panditpooja')->where('is_block', '!=', 9)->where('id', $id)->where('payment_status', 1)->where('status', 0)->with(['services', 'customers', 'customer'])->get();        $order = $orders->first();
        $serviceId = $order->service_id;
        $addressList = PanditServiceDetail::where('service_id', $serviceId)->where('pandit_id', $order->pandit_assign)
            ->get()->keyBy('service_id');
        $services = Service::where('id', $serviceId)->get();
        foreach ($services as $service) {
            if (isset($addressList[$service->id]) && !empty($addressList[$service->id]->address)) {
                $service->final_venue = $addressList[$service->id]->address;
            } else {
                $service->final_venue = $service->pooja_venue;
            }
        }
        foreach ($orders as $order) {
            $order->pooja_video = $request->pooja_video;
            $order->order_status = $request->order_status;
            $order->video_created_sharing = now();
            $order->save();
        }
        foreach ($orders as $order) {
            if ($order->customers) {
                $messageData = [
                    'service_name' => $service->name,
                    'share_video' =>  $request->pooja_video ?? 'mahakal.com',
                    'puja_venue'     => $service->final_venue ?? 'N/A',
                    'scheduled_time' => date('h:i A', strtotime($order->schedule_time)) ?? 'N/A',
                    'booking_date' => date('d-m-Y', strtotime($order->booking_date)),
                    'customer_id' => $order->customer_id,
                    'orderId' => $order->order_id,
                ];
                SendWhatsappMessage::dispatch('whatsapp', 'Pandit Shared Video', $messageData);
                // send email
                $userInfo = \App\Models\User::where('id', $order->customer_id)->first();
                $service_name = \App\Models\Service::where('id', $order->service_id)
                    ->where('product_type', 'pooja')
                    ->first();
                if ($userInfo && !empty($userInfo['email']) && filter_var($userInfo['email'], FILTER_VALIDATE_EMAIL)) {
                    $bookingDetails = $order;

                    $data = [
                        'type' => 'pooja',
                        'email' => $userInfo->email,
                        'subject' => 'Puja Video Link Shared',
                        'htmlContent' =>
                        \Illuminate\Support\Facades\View::make('admin-views.email.email-template.pooja-share-template', compact('userInfo', 'service_name', 'bookingDetails'))->render(),
                    ];

                    SendEmailPoojaJob::dispatch($data);
                }
            }
        }
        $orders->load('customer');

        foreach ($orders as $order) {
            if ($order->order_status == 5 && $order->customer) {
                event(new OrderStatusEvent(key: '5', type: 'puja', order: $order));
            }
        }
        Toastr::success(translate('pooja_video_changed_successfully'));

        return back();
    }
    
    public function cancel_poojas($id, Request $request)
    {
        // dd($request->all());
        $orders = Service_order::where('id', $id)->where('payment_status', 1)->where('type', 'panditpooja')->where('is_block', '!=', 9)->where('status', 0)->with(['services', 'customers'])->get();
        foreach ($orders as $pooja) {
            $pooja->order_canceled_reason = $request->order_canceled_reason;
            $pooja->order_canceled = now();
            $pooja->status = 2;
            $pooja->order_status = $request->order_status;
            $pooja->is_completed = 2;

            $pooja->save();
        }
        // dd($orders);
        Toastr::success(translate('pooja_Cancel_successfully'));
        return redirect()->route('admin.pooja.orders.list', ['2']);
    }

    public function ChadhavaList(Request $request, $id)
    {
        $vendor = Guruji::find($id);
        if (!$vendor) {
            Toastr::warning(translate('profile_not_found'));
            return redirect()->route('guruji.dashboard');
        }
        $chadhavaOrder = Chadhava_orders::where('type', 'chadhava')->where('is_block', '!=', 9)->where('status', 0)->where('is_completed', 0)->where('payment_status', 1)->where('pandit_assign',$vendor->id)
            ->with(['leads', 'chadhava', 'customers', 'pandit'])->selectRaw('service_id, COUNT(*) as total_orders,pandit_assign,booking_date, COUNT(created_at) as booking_count, SUM(pay_amount) as total_amount, GROUP_CONCAT(members SEPARATOR "|") as members,order_status,created_at,customer_id,id')
            ->groupBy('service_id', 'booking_date', 'pandit_assign')->orderBy('total_orders', 'DESC')->get();
        $users = User::all();   
        return view('guruji-views.orders.chadhava.list',compact('vendor','chadhavaOrder'));
    }
    
}
