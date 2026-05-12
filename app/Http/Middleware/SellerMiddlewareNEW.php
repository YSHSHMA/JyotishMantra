<?php

namespace App\Http\Middleware;

use App\Models\RemoteAccess;
use App\Services\VendorService;
use Closure;
use App\Utils\Helpers;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Http;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function __construct(
        private readonly VendorService             $vendorService,
    ){}

    public function handle($request, Closure $next)
    {
        if (auth('seller')->check() && auth('seller')->user()->status == 'approved') {
            // $response = Http::get('https://api.ipify.org');
            // $ipAddress = $response->body();
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $allowedIP = RemoteAccess::where('host_address',$ipAddress)->exists();
            if(!$allowedIP){
                $this->vendorService->logout();
                Toastr::success(translate('You do not have access to this portal').'.');
                return redirect()->route('vendor.auth.login');
            }
            
            return $next($request);
        }
        auth()->guard('seller')->logout();

        return redirect()->route('vendor.auth.login');
    }
}
