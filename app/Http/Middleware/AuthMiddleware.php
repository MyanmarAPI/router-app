<?php namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;

class AuthMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //Check for API Authentication
    	$auth_url = config('app.auth');

    	$request_url = $auth_url['base_url'].$auth_url['uri'];

        $client = new Client();

        //ToDo : Header or Auth Type need to change later according to Main App Authentication
        $headers = ['X-Auth-Token' 		=> $request->header('auth_token'),
			        'X-Request-App-ID'  => $request->header('app_id'),
			        'X-App-Secret-Key'  => $request->header('secret_key')];

        $auth_res = $client->get($request_url, [
					    'headers' => $headers
					]);

        switch ($auth_res->getStatusCode()) {
        	case 401:
        		return response()->json([
        			'status' => 401,
        			'message' => 'Authentication failed'
        		], 401);
        		break;
        	
        	case 200:
        		return $next($request);	
        	break;

        } 

        return response()->json([
        	'status' => 401,
        	'message' => 'Authentication failed'
        ], 401);    

    }

}
