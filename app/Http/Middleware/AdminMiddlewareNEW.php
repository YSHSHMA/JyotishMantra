<?php

namespace App\Http\Middleware;

use App\Models\RemoteAccess;
use App\Services\AdminService;
use Closure;
use App\Utils\Helpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

     public function __construct(private readonly AdminService $adminService)
    {
    }

    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {
            // $response = Http::get('https://api.ipify.org');
            // $ipAddress = $response->body();
            $ipAddress = $_SERVER['REMOTE_ADDR'];
            $allowedIP = RemoteAccess::where('host_address',$ipAddress)->exists();
            if(!$allowedIP){
                $this->adminService->logout();
                session()->flash('success', translate('You do not have access to this portal'));
                return redirect('login/' . getWebConfig(name: 'admin_login_url'));
            }

            return $next($request);
        }else{
            abort(404);
        }
    }
}
