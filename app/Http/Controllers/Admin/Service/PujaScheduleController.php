<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use App\Models\Category;
use App\Models\PoojaForecast;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;


class PujaScheduleController extends Controller
{
    

    public function index(Request $request)
    {
        $today = Carbon::today();
        $endDate = $today->copy()->addDays(8);
        PoojaForecast::whereDate('booking_date', '<=', $today->format('Y-m-d'))
            ->where('is_expired', 0)
            ->update(['is_expired' => 1]);
        $selectedCategory = $request->input('category');
        $selectedWeekday = $request->input('weekday');

        $categories = Category::where('parent_id', 33)->get();

        $pujaschedule = PoojaForecast::with(['service.category'])
            ->whereBetween('booking_date', [$today, $endDate])
            ->where('is_expired', '0')
            ->when($selectedCategory, function ($query) use ($selectedCategory) {
                $query->whereHas('service', function ($q) use ($selectedCategory) {
                    $q->where('sub_category_id', $selectedCategory);
                });
            })
            ->when($selectedWeekday, function ($query) use ($selectedWeekday) {
                $query->whereRaw("DAYNAME(booking_date) = ?", [$selectedWeekday]);
            })
            ->orderBy('booking_date', 'asc')
            ->get()
            ->filter(function ($item) {
                return $item->service;
            })
            ->groupBy('service_id');

        return view('admin-views.service.pujaschedule.list', compact('pujaschedule', 'categories'));
    }


    public function updatePoojaTime(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'new_time' => 'required|date_format:H:i',
        ]);
        $service = Service::find($request->service_id);
        $service->pooja_time = $request->new_time;
        $service->save();
        return redirect()->back()->with('success', 'Pooja time updated successfully!');
    }
    public function updatePoojaWeek(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'week_days' => 'nullable|array',
            'week_days.*' => 'in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
        ]);

        $service = Service::findOrFail($request->service_id);
        $existingDays = $service->week_days ? json_decode($service->week_days, true) : [];
        $newDays = $request->week_days ?? [];
        $duplicateDays = array_intersect($existingDays, $newDays);
        if (!empty($duplicateDays)) {
            return redirect()->back()->with('error', 'These week days already exist: ' . implode(', ', $duplicateDays));
        }
        $updatedDays = array_unique(array_merge($existingDays, $newDays));
        $service->week_days = json_encode($updatedDays);
        $service->save();
        return redirect()->back()->with('success', 'Week Days updated successfully!');
    }


}
