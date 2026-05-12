<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReactValidMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasHeader('X-software-id')) {
            if(env('APP_MODE') == 'demo'){
                return $next($request);
            }

            $reactData = getWebConfig(name: 'react_setup');
            $reactStatus = isset($reactData['status']) ? $reactData['status'] : 0;
            $reactDomain = isset($reactData['react_domain']) ? $reactData['react_domain'] : null;
            if ($reactStatus == 1 && $reactDomain) {
                $url = str_ireplace('www.', '', parse_url(request()->headers->get('origin'), PHP_URL_HOST));
                if (str_ireplace('www.', '', parse_url($reactDomain, PHP_URL_HOST)) == $url) {
                    return $next($request);
                }
            }
            return response()->json(['message'=>translate('invalid_react_setup')],403);
        }
        // continue request
        return $next($request);
    }
}
