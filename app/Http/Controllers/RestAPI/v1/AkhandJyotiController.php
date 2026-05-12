<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\AkhandJyoti;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AkhandJyotiController extends Controller
{
    public function create(Request $request)
    {
        $create = new AkhandJyoti;
        $create->customer_id = $request->customer_id;
        $create->start_datetime = Carbon::now();
        $create->end_datetime = Carbon::now()->addHours(24);
        $create->save();
        if ($create) {
            return response()->json(['status' => true, 'message' => 'Akhand Jyoti Started'], 200);
        }
        return response()->json(['status' => false, 'message' => 'An Error Occurred'], 400);
    }

    public function update(Request $request)
    {
        $update = AkhandJyoti::where('customer_id', $request->customer_id)->latest()->first();
        $update->start_datetime = Carbon::now();
        $update->end_datetime = Carbon::now()->addHours(24);
        $update->save();
        if ($update) {
            return response()->json(['status' => true, 'message' => 'Akhand Jyoti Update'], 200);
        }
        return response()->json(['status' => false, 'message' => 'An Error Occurred'], 400);
    }

    public function list(Request $request)
    {
        $last7DaysRecords = AkhandJyoti::where('customer_id', $request->customer_id)
            ->where('start_datetime', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->where('start_datetime', '<=', Carbon::now()->endOfDay())
            ->get();
        $groupedRecords = $last7DaysRecords->groupBy(function ($item) {
            return Carbon::parse($item->start_datetime)->format('l');
        });
        $weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weekdayDataStatus = collect($weekdays)->mapWithKeys(function ($day) use ($groupedRecords) {
            return [$day => $groupedRecords->has($day)];
        });

        $activeAkhandJyoti = AkhandJyoti::where('customer_id', $request->customer_id)->latest()->first();
        $akhandJyotiCount = count($last7DaysRecords);
        $totalUsersCount = AkhandJyoti::groupBy('customer_id')->get()->count();

        if ($last7DaysRecords && $totalUsersCount) {
            return response()->json(['status' => true, 'activeAkhandJyoti' => $activeAkhandJyoti, 'weekdays' => $weekdayDataStatus, 'akhanJyotiCount' => $akhandJyotiCount, 'totalUsersCount' => $totalUsersCount], 200);
        }
        return response()->json(['status' => false, 'message' => 'An Error Occurred'], 400);
    }

    public function getStatus(Request $request)
    {
        $data = AkhandJyoti::where('customer_id', $request->customer_id)->latest()->first();
        if ($data) {
            $currentDateTime = now();
            if ($currentDateTime > $data->end_datetime) {
                return response()->json(['status' => true, 'akhandJyoti' => false, 'message' => 'Akhand jyoti has been stopped.'], 200);
            } else {
                return response()->json(['status' => true, 'akhandJyoti' => true, 'message' => 'Akhand jyoti is running.'], 200);
            }
        } else {
            return response()->json(['status' => true, 'akhandJyoti' => false, 'message' => 'No data found'], 200);
        }
        return response()->json(['status' => false, 'message' => 'An Error Occurred'], 400);
    }
}
