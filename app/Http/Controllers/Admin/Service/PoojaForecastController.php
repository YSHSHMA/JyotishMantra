<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use App\Services\PoojaForecastService;

class PoojaForecastController extends Controller
{
    public function runForecast()
    {
        $forecastService = new PoojaForecastService();
        $forecastService->generateForecast(14); // 7 ya 14 days ka forecast

        return response()->json(['message' => 'Pooja Forecast Updated Successfully']);
    }
}