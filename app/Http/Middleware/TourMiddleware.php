<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TourMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userStatus = auth('tour')->user()->status ?? '';
        if (auth('tour')->check() && $userStatus == 'approved') {
            return $next($request);
        } elseif (auth('tour')->check() && in_array($userStatus, ['hold', 'suspended', 'pending', 'rejected'])) {

            if (!empty(auth('tour')->user()->aadhar_front_image) && !empty(auth('tour')->user()->pancard_image)) {
                return $next($request);
            }
            $allowedRoutes = [
                'tour-vendor.profile.update',
                'tour-vendor.profile.profile-edit',
                'tour-vendor.dashboard.index',
                'tour-vendor.message.*',
                'tour-vendor.messages.*',
            ];
            $currentRouteName = $request->route()->getName();
            if (in_array($currentRouteName, $allowedRoutes)) {
                return $next($request);
            }
            toastr()->error('Please Complete your Profile First , to gain full access to the panel.');
            return redirect()->route('tour-vendor.profile.update', [auth('tour')->user()->relation_id]);
        }

        auth()->guard('tour')->logout();

        return redirect()->route('vendor.auth.login');
    }
}
