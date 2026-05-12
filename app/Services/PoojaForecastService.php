<?php

namespace App\Services;

use App\Models\Service;
use App\Models\PoojaForecast;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class PoojaForecastService
{
    /**
     * Generate forecast records for the next X days (default 7)
     */
    public function generateForecast($days = 8)
    {
        $today = Carbon::today();               // Gets today's date at 00:00:00
        $end = $today->copy()->addDays($days);  // Safely copy and add days
        // Mark existing forecasts as expired (before inserting new)
        PoojaForecast::whereDate('booking_date', '<=', $today->format('Y-m-d'))
            ->where('is_expired', 0)
            ->update(['is_expired' => 1]);
        // Eager load categories to reduce queries
        $services = Service::with('category')->where('status','1')->where('product_type','pooja')->get();

        foreach ($services as $service) {
            $categoryName = $service->category->name ?? 'Unknown';

            // 🕉️ Weekly Pooja
            if ($service->pooja_type == 0 && !empty($service->week_days)) {
                $weekDays = json_decode($service->week_days, true); 
                $dayNameMap = [
                    'sunday'    => 0,
                    'monday'    => 1,
                    'tuesday'   => 2,
                    'wednesday' => 3,
                    'thursday'  => 4,
                    'friday'    => 5,
                    'saturday'  => 6,
                ];
                $reverseMap = array_flip($dayNameMap); // for converting back to names

                $weekDayInts = collect($weekDays)
                    ->map(fn($day) => strtolower(trim($day)))
                    ->filter(fn($day) => isset($dayNameMap[$day]))
                    ->map(fn($day) => $dayNameMap[$day])
                    ->values()
                    ->toArray();

                // Prepare comma-separated string of day names
                $weekDayNamesStr = implode(', ', collect($weekDayInts)->map(fn($d) => ucfirst($reverseMap[$d]))->toArray());

                for ($date = $today->copy(); $date->lte($end); $date->addDay()) {
                    if (in_array($date->dayOfWeek, $weekDayInts)) {
                        $this->insertOrUpdateForecast($service, $date, 'weekly', $categoryName, $weekDayNamesStr, null);
                    }
                }
            }

            if ($service->pooja_type == 1 && !empty($service->schedule)) {
                $schedules = json_decode($service->schedule, true); // [{"schedule": "2025-07-21"}, ...]

                foreach ($schedules as $s) {
                    if (!empty($s['schedule'])) {
                        try {
                            // Combine schedule + optional time
                            $datetimeString = $s['schedule'];
                            if (!empty($s['schedule_time'])) {
                                $datetimeString .= ' ' . $s['schedule_time'];
                            }

                            $startDateTime = Carbon::parse($datetimeString); // e.g. 2025-09-30 12:00

                            $isInNext7Days = $startDateTime->between($today, $end);
                            $isInCurrentMonth = $startDateTime->isSameMonth($today) && $startDateTime->greaterThanOrEqualTo($today);

                            if ($isInNext7Days || $isInCurrentMonth) {
                                $this->insertOrUpdateForecast(
                                    $service,
                                    $startDateTime->copy()->startOfDay(),  // For booking_date
                                    'special',
                                    $categoryName,
                                    null, // no week_days
                                    $startDateTime
                                );
                            }

                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            }


            }
        }
    /**
     * Insert forecast record
     */
    protected function insertOrUpdateForecast($service, Carbon $date, $type, $categoryName, $weekDaysStr = null, $startDateTime = null)
    {
          // Check if the forecast already exists
        $exists = PoojaForecast::where('service_id', $service->id)
                ->whereDate('booking_date', $date->format('Y-m-d'))
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => '⚠️ Pooja Forecast already exists for this service on this date.',
                    'service_id' => $service->id,
                    'booking_date' => $date->format('Y-m-d')
                ]);
            }
            PoojaForecast::create([
                'service_id'     => $service->id,
                'booking_date'   => $date->format('Y-m-d'),
                'type'           => $type,
                'category'       => $categoryName,
                'total_orders'   => 0,
                'total_users'    => 0,
                'earnings'       => 0,
                'week_days'      => $weekDaysStr,
                'start_datetime' => $startDateTime,
            ]);
    }
}