<?php

namespace App\Http\Controllers\Admin\Visitor;

use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\Visitor;
use Illuminate\Http\RedirectResponse;
use App\Traits\FileManagerTrait;
use App\Utils\Helpers;
use DateTime;
use DateTimeZone;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VisitorController
{

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */

     public function visitor()
     {
         $visitor = Visitor::orderBy('id', 'desc')->get();
 
         return view('admin-views.visitor.visitor-list', compact('visitor'));
     }
 
    public function ShowVisitorData(Request $request)
     {
         $query = Visitor::query();
 
         // Filter by Date, Month, Last 6 Months, Year
         switch ($request->filter_type) {
             case 'date':
                 if ($request->from_date && $request->to_date) {
                     $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
                 }
                 break;
 
             case 'month':
                 $month = $request->filter_month ?? now()->month;
                 $year = $request->filter_year ?? now()->year;
                 $query->whereMonth('created_at', $month)
                     ->whereYear('created_at', $year);
                 break;
 
             case 'last_6_months':
                 $query->where('created_at', '>=', now()->subMonths(6));
                 break;
 
             case 'year':
                 $year = $request->filter_year ?? now()->year;
                 $query->whereYear('created_at', $year);
                 break;
         }
 
         // Additional Filters
         if ($request->filled('filter_ip')) {
             $query->where('ip_address', 'like', '%' . $request->filter_ip . '%');
         }
 
         if ($request->filled('filter_country')) {
             $query->where('country', 'like', '%' . $request->filter_country . '%');
         }
 
         if ($request->filled('filter_city')) {
             $query->where('city', 'like', '%' . $request->filter_city . '%');
         }
 
         $visitors = $query->orderBy('id', 'desc')->get()->map(function ($visitor) {
             return [
                 'id' => $visitor->id,
                 'created_at' => $visitor->created_at->format('Y-m-d H:i:s'),
                 'ip_address' => $visitor->ip_address,
                 'country' => $visitor->country,
                 'city' => $visitor->city,
                 'referer' => $visitor->referer,
                 'url' => $visitor->url,
             ];
         });
 
         return response()->json(['data' => $visitors]);
     }
}