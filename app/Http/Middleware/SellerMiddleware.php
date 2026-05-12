<?php

namespace App\Http\Middleware;

use Closure;
use App\Utils\Helpers;
use Illuminate\Support\Facades\Auth;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $userStatus = auth('seller')->user()->status ?? "";
        if (auth('seller')->check() && auth('seller')->user()->status == 'approved') {
            return $next($request);
        } elseif (auth('seller')->check() && in_array($userStatus, ['hold', 'suspended', 'pending', 'rejected'])) {
            if (!empty(auth('seller')->user()->aadhar_number) && !empty(auth('seller')->user()->pan_number) && auth('seller')->user()->status != 'hold') {
                return $next($request);
            }
            $allowedRoutes = [
                'vendor.profile.update',
                'vendor.profile.ven-update',
                'vendor.dashboard.index',
                'vendor.profile.update2',
            ];
            $currentRouteName = $request->route()->getName();
            if (in_array($currentRouteName, $allowedRoutes)) {
                return $next($request);
            }
            toastr()->error('First fill the Remaining info to list your Products & show to the Customers.');
            return redirect()->route('vendor.profile.update', [auth('seller')->id()]);
        }

        auth()->guard('seller')->logout();

        return redirect()->route('vendor.auth.login');
    }
}