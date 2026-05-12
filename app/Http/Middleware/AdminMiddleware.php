<?php

namespace App\Http\Middleware;

use App\Services\AdminService;
use Closure;
use App\Utils\Helpers;
use App\Models\RemoteAccess;
use Illuminate\Support\Facades\Auth;

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
        //  $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {
            $ipAddress = Helpers::getPublicIp();
            $host = request()->getHost();
            if (in_array($host, ['mahakal.com', 'www.mahakal.com'])) {
                if ($ipAddress == null) {
                    $this->adminService->logout();
                    session()->flash('success', translate('logged out successfully'));
                    return redirect('login/' . getWebConfig(name: 'admin_login_url'))->with('error', 'Failed to retrieve your public IP');
                }

                $allowedIP = RemoteAccess::where('host_address', $ipAddress)->exists();
                if (!$allowedIP) {
                    $this->adminService->logout();
                    session()->flash('success', translate('logged out successfully'));
                    return redirect('login/' . getWebConfig(name: 'admin_login_url'))->with('error', 'You do not have access to this portal');
                }
            }

            return $next($request);
        } else {
            abort(404);
        }
    }
}
