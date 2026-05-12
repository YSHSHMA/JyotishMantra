<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visitor;
use Illuminate\Support\Facades\Http;

class TrackVisitor
{
    // public function handle(Request $request, Closure $next)
    // {
    //     $ip = $request->ip();

    //     if (!Visitor::where('ip_address', $ip)->exists()) {
    //         $city = null;
    //         $country = null;
    //         $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

    //         try {
    //             $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}");

    //             if ($response->successful()) {
    //                 $data = $response->json();
    //                 $city = $data['city'] ?? null;
    //                 $country = $data['country'] ?? null;

    //                 Visitor::create([
    //                     'ip_address' => $ip,
    //                     'url'        => $request->fullUrl(),
    //                     'referer'    => $referer,
    //                     'city'       => $city,
    //                     'country'    => $country,
    //                 ]);
    //             }
    //             // Else: do not store visitor if API failed or returned invalid data
    //         } catch (\Exception $e) {
    //             // Log the exception if needed, but do not interrupt request flow
    //             // logger()->error('IP API Request failed: ' . $e->getMessage());
    //         }
    //     }

    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next)
    {
        try {
            $ip = $request->ip();

            if (!Visitor::where('ip_address', $ip)->exists()) {
                $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

                Visitor::create([
                    'ip_address' => $ip,
                    'url'        => $request->fullUrl(),
                    'referer'    => $referer,
                    'city'       => null,
                    'country'    => null,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Visitor middleware error: ' . $e->getMessage());
        }

        return $next($request);
    }

}