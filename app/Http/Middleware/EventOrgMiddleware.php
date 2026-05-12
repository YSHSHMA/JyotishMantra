<?php

namespace App\Http\Middleware;

use App\Models\Seller;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EventOrgMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userStatus = auth('event')->user()->status ?? '';
        if (auth('event_employee')->check()) {
            $Employee = auth('event_employee')->user()->id ?? '';
            $checkData = Seller::where('type', 'event')->where('relation_id', auth('event_employee')->user()->relation_id ?? '')->first();
            if ($Employee && $checkData['status'] == 'approved') {
                return $next($request);
            } else {
                toastr()->error("Event Vendor Status: " . $checkData['status']);
                auth()->guard('event_employee')->logout();
                return redirect()->route('vendor.auth.login');
            }
        } else if (auth('event')->check() && $userStatus == 'approved') {
            return $next($request);
        } elseif (auth('event')->check() && in_array($userStatus, ['hold', 'suspended', 'pending', 'rejected'])) {
            if (!empty(auth('event')->user()->aadhar_front_image) && !empty(auth('event')->user()->pancard_image)) {
                return $next($request);
            }
            $allowedRoutes = [
                'event-vendor.profile.update',
                'event-vendor.profile.profile-edit',
                'event-vendor.profile.update2',
                'event-vendor.dashboard.index',
                'event-vendor.message.*',
                'event-vendor.messages.*',
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
            return redirect()->route('event-vendor.profile.update', [auth('event')->user()->relation_id]);
        }

        auth()->guard('event')->logout();

        return redirect()->route('vendor.auth.login');
    }
}