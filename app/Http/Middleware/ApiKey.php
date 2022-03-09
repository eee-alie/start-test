<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKey
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
        $packageNameTemp = array_search($request->header('x-api-key'), config('apiKey.X_API_KEYS'));
        if ($packageNameTemp) {
            $request['packageName'] = $packageNameTemp;
            return $next($request);
        }
        abort(403, 'x-api-key incorrect');
    }
}
