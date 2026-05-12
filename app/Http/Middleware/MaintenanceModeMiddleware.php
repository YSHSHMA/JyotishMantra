<?php

namespace App\Http\Middleware;

use App\Utils\Helpers;
use Closure;

class MaintenanceModeMiddleware
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
        $maintenance_mode = Helpers::get_business_settings('maintenance_mode') ?? 0;
        if ($maintenance_mode) {
            return redirect()->route('maintenance-mode');
        }
        return $next($request);
    }
}
