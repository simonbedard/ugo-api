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

        /**
         * Check if we should skip the cache
         */
        if (config('ugo.api.skip_cache')) return $next($request);

        /**
         * Check id the cache containe the query
         */
        return (Cache::has($request->fullUrl()))
            ? Cache::get($request->fullUrl())->response()->header('X-Ugo-Cache', 'hit')
            : $next($request);
    }


    public function terminate($request, $response)
    {
    }
}
