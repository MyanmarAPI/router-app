<?php

namespace App\Http\Middleware;

use Closure;
use Response;

class CORSMiddleware
{
    public function handle($request, Closure $next)
    {

    	$headers = [
    		'Access-Control-Allow-Origin' => '*',
    		'Access-Control-Allow-Headers' => 'X-API-KEY, X-API-SECRET',
    		'Access-Control-Allow-Methods' => 'GET, OPTIONS'
    	];

    	if($request->getMethod() == "OPTIONS") {

    		return Response::make('OK', 200, $headers);

    	}

        $response = $next($request);

        foreach ($headers as $key => $value) {

        	$response->headers->set($key, $value);

        }

        return $response;
    }
}