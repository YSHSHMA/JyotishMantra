<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use App\Models\Astrologer\Astrologer;
use App\Models\Devotee;
use App\Models\PanditTransectionPooja;
use App\Models\PoojaRecords;
use App\Models\ServiceReview;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PujaRecordsController extends Controller
{
    public function index()
    {
        $records = PoojaRecords::with(['service', 'vippooja'])
            ->orderBy('id', 'desc')->where('status', 1)
            ->get()
            ->groupBy('service_id');

        $recordpuja = [];

        foreach ($records as $serviceId => $pujas) {
            $firstPuja = $pujas->first();
            $serviceOrderId = $firstPuja->service_order_id;

            preg_match('/^[A-Z]+/', $serviceOrderId, $matches);
            $prefix = $matches[0] ?? '';

            if (in_array($prefix, ['VPJ', 'APJ'])) {
                $resolvedServiceName = $firstPuja->vippooja->name ?? 'VIP Puja';
            } elseif ($prefix === 'PJ') {
                $resolvedServiceName = $firstPuja->service->name ?? 'General Puja';
            } else {
                $resolvedServiceName = 'Unknown';
            }

            $dates = $pujas->pluck('booking_date')->filter()->unique()->map(function ($date) {
                return $date; // Keep raw date format
            })->values();

            // Day name frequency (e.g., Monday => 4 times)
            $dayFrequency = $pujas->pluck('booking_date')->filter()->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('l');
            })->countBy();

            // Month frequency (e.g., Jan => 3 times)
            $monthFrequency = $pujas->pluck('booking_date')->filter()->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('M');
            })->countBy();

            $recordpuja[] = [
                'service_name' => $resolvedServiceName,
                'total_days' => $dates->count(),
                'date_list' => $dates->toArray(),
                'total_pujas' => $pujas->count(),
                'total_amount' => $pujas->sum('amount'),
                'day_frequency' => $dayFrequency,
                'month_frequency' => $monthFrequency,
            ];
        }

    
        $recordpuja = collect($recordpuja)->sortByDesc('total_pujas')->values()->toArray();

        return view('admin-views.pooja.records.list', compact('recordpuja'));
    }


    public function dateDetailsPage($serviceName, $date, Request $request)
    {
        $parsedDate = Carbon::parse($date)->format('Y-m-d');

        $records = PoojaRecords::with(['service', 'vippooja'])
            ->whereDate('booking_date', $parsedDate)
            ->get();

        foreach ($records as $record) {
            $record->member_names = [];
            $members = Devotee::where('service_order_id', $record->service_order_id)->pluck('members')->where('status', 1)->first();
            if ($members) {
                $decoded = json_decode($members, true);
                $record->member_names = is_array($decoded) ? $decoded : [];
            }

            $serviceOrderId = $record->service_order_id;
            preg_match('/^[A-Z]+/', $serviceOrderId, $matches);
            $prefix = $matches[0] ?? '';

            if (in_array($prefix, ['VPJ', 'APJ'])) {
                $record->resolved_service_name = $record->vippooja->name ?? 'VIP Puja';
            } elseif ($prefix === 'PJ') {
                $record->resolved_service_name = $record->service->name ?? 'General Puja';
            } else {
                $record->resolved_service_name = 'Unknown';
            }

            $record->pandit_name = null;
            $record->pandit_phone = null;

            $panditData = PanditTransectionPooja::where('service_order_id', $record->service_order_id)->where('status', 1)->first();
            if ($panditData) {
                $astrologer = Astrologer::find($panditData->pandit_id);
                if ($astrologer) {
                    $record->pandit_name = $astrologer->name;
                    $record->pandit_phone = $astrologer->phone;
                }
            }
        }

        $totalOrders = $records->count();
        $totalAmount = $records->sum('amount');

        return view('admin-views.pooja.records.date-details', compact('records', 'serviceName', 'date', 'totalOrders', 'totalAmount'));
    }


    public function filteredExport(Request $request)
    {
        $range = $request->range ?? 'monthly';

        $query = PoojaRecords::with(['service', 'vippooja']);

        switch ($range) {
            case 'weekly':
                $query->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('booking_date', now()->month);
                break;
            case '6months':
                $query->whereBetween('booking_date', [now()->subMonths(6), now()]);
                break;
            case 'yearly':
                $query->whereYear('booking_date', now()->year);
                break;
        }

        $records = $query->get();

        return view('admin-views.pooja.records.export-view', compact('records', 'range'));
    }

    public function exportForm($service)
    {
        return view('admin-views.pooja.records.export-form', compact('service'));
    }

    public function exportDownload(Request $request)
    {
        $from = Carbon::parse($request->from_date)->startOfDay();
        $to = Carbon::parse($request->to_date)->endOfDay();
        $serviceName = $request->service;

        $records = PoojaRecords::with(['service', 'vippooja'])
            ->whereBetween('booking_date', [$from, $to])
            ->whereHas('service', function ($q) use ($serviceName) {
                $q->where('name', $serviceName);
            })
            ->get();

        $range = $from->format('d M Y') . ' - ' . $to->format('d M Y');

        return view('admin-views.pooja.records.export-view', compact('records', 'range'));
    }

    // ------------------------ 06/08/2025 Get The Puja Devotee Records By Er.Rahul Bathri------------------------------
    public function getDevotee(Request $request)
    {
        $query = Devotee::where('status', 1)->with([
            'serviceOrder.services',
            'serviceOrder.vippoojas',
            'serviceOrder.vippoojas',
            'chadhavaOrder.chadhava',
        ]);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('service_id')) {
            $serviceId = $request->service_id;
            $type = $request->type;

            $query->whereHas('serviceOrder', function ($q) use ($type, $serviceId) {
                if (in_array($type, ['pooja', 'vip', 'anushthan'])) {
                    $q->where('service_id', $serviceId);
                }
            });

            if ($type === 'chadhava') {
                $query->whereHas('chadhavaOrder', function ($q) use ($serviceId) {
                    $q->where('service_id', $serviceId);
                });
            }
        }
        //  Filter by booking_date
        if ($request->booking_date) {
            $query->whereHas('serviceOrder', function ($q) use ($request) {
                $q->whereDate('booking_date', $request->booking_date);
            });
        }


        $devotee = $query->get();

        return view('admin-views.pooja.devotee.list', compact('devotee'));
    }
    // By kanika
    public function poojaTransactionList(Request $request)
    {
        $query = PanditTransectionPooja::with(['service','vipPooja','pandit'])->orderBy('id', 'desc');
        // Optional Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('booking_date')) {
            $query->whereDate('booking_date', $request->booking_date);
        }
        $transactions = $query->get();
        return view('admin-views.pooja.transaction.list', compact('transactions'));
    }


    public function pujareview(Request $request){   
        $review = ServiceReview::with(['users','pandit','services','vippoojas','chadhava','offlinePooja'])->orderBy('created_at','desc')->get();
        return view('admin-views.pooja.review.list', compact('review'));
    }
  
    public function pujareviewstatus(Request $request)
    {
        ServiceReview::where('order_id',$request->order_id)->update(['status'=>$request->status]);
        return response()->json(['message' => 'Status updated successfully']);
    }
    public function  chanagecommentUpdate(Request $request){
        $record = ServiceReview::where('order_id', $request->order_id)->first();
        $record->comment = $request->comment;
        $record->youtube_link = $request->youtube_link ?? '';
        $record->save();
        return back()->with('success', 'Comment and YouTube link updated successfully.');
    }
    public function  deletecomment(Request $request,$order_id){
        $record = ServiceReview::where('order_id', $order_id)->delete();
        if($record){
            return back()->with('error', 'Unable to delete.');
        }
        return back()->with('success', 'Deleted successfully.');
    }


}
