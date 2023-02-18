<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Cache;

use Closure;
use Illuminate\Http\Request;

class UgoCacheApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        
        if (Cache::has($request->fullUrl())) {
            $response = Cache::get($request->fullUrl())->response()->header('X-Ugo-Cache', 'hit');
            return $response;
        } else {
            return $next($request);
        }
    }


    public function terminate($request, $response)
    {
    }
}
