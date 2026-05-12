<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrusteesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     public function handle(Request $request, Closure $next): Response
    {
        $userStatus = auth('trust')->user()->status ?? '';
        if (auth('trust_employee')->check()) {
            $Employee = auth('trust_employee')->user()->id ?? '';
            $checkData = \App\Models\Seller::where('type', 'trust')->where('relation_id', auth('trust_employee')->user()->relation_id ?? '')->first();
            if ($Employee && $checkData['status'] == 'approved') {
                return $next($request);
            } else {
                toastr()->error("Trust Vendor Status: " . $checkData['status']);
                auth()->guard('trust_employee')->logout();
                return redirect()->route('vendor.auth.login');
            }
        } elseif (auth('purohit')->check()) {
            $Employee = auth('purohit')->user()->id ?? '';
            $gettrust_id = \App\Models\Purohit::with(['temple'])->where('id',$Employee)->first();
            $checkData = \App\Models\Seller::where('type', 'trust')->where('relation_id', ($gettrust_id['temple']['trust_id']??0))->first();
            if ($Employee && ($checkData['status']??"") == 'approved') {
                return $next($request);
            } else {
                toastr()->error("Trust Vendor Status: " . ($checkData['status']??""));
                auth()->guard('purohit')->logout();
                return redirect()->route('vendor.auth.login');
            }
        }  else if (auth('trust')->check() && $userStatus == 'approved') {
            return $next($request);
        } elseif (auth('trust')->check() && in_array($userStatus, ['hold', 'suspended', 'pending', 'rejected'])) {
            if (!empty(auth('trust')->user()->aadhar_front_image) && !empty(auth('trust')->user()->pancard_image)) {
                return $next($request);
            }
            $allowedRoutes = [
                'trustees-vendor.profile.update',
                'trustees-vendor.profile.profile-edit',
                'trustees-vendor.dashboard.index',
                'trustees-vendor.message.*',
                'trustees-vendor.messages.*',
                'trustees-vendor.profile.update2',
                'trustees-vendor.profile.delete-image',
            ];
            $currentRouteName = $request->route()->getName();
            if (in_array($currentRouteName, $allowedRoutes)) {
                return $next($request);
            }
            foreach ($allowedRoutes as $allowed) {
                if (\Illuminate\Support\Str::is($allowed, $currentRouteName)) {
                    return $next($request);
                }
            }
            toastr()->error('Welcome to Mahakal.com! Please complete your profile to unlock full access to your dashboard features.');
            return redirect()->route('trustees-vendor.profile.update', [auth('trust')->user()->relation_id]);
        }

        auth()->guard('trust')->logout();
        auth()->guard('trust_employee')->logout();

        return redirect()->route('vendor.auth.login');
    }
}
