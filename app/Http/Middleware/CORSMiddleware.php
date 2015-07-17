<?php

namespace App\Http\Middleware;

use Closure;

class CORSMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->headers->set('Access-Control-Allow-Headers', 'X-API-KEY,X-API-SECRET');

        $response->headers->set('Access-Control-Allow-Methods', 'GET');

        return $response;
    }
}