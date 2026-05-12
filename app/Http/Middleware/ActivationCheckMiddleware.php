<?php

namespace App\Http\Middleware;

use App\Utils\Helpers;
use App\Traits\ActivationClass;
use Brian2694\Toastr\Facades\Toastr;
use Closure;
use http\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ActivationCheckMiddleware
{
    use ActivationClass;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
