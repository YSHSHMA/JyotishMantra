<?php

namespace App\Http\Middleware;

use App\Utils\Helpers;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Brian2694\Toastr\Facades\Toastr;

class EmployeeModulesCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next,$module): Response
    {
        if (Helpers::employeemodules_check($module)) {
            return $next($request);
    }
         Toastr::error(translate('access_Denied').'!');
        return back();
    }
}
