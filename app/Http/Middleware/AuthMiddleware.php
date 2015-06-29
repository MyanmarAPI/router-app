<?php namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

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

        $client = new Client(['base_url' => $auth_url['base_url']]);

        //ToDo : Header or Auth Type need to change later according to Main App Authentication
        $headers = ['X-API-KEY' 		=> $request->header('X-API-KEY'),
			        'X-API-SECRET'  => $request->header('X-API-SECRET')];

        try {

            $auth_res = $client->get($auth_url['uri'], [
                            'headers' => $headers
                        ]);

        } catch (ClientException $e) {

            $auth_res = $e->getResponse();

        }

        switch ($auth_res->getStatusCode()) {

            case 401:
                return response()->json(config('status.messages.401'), 401);
                break;
            
            case 200:
                return $next($request); 
            break;

        } 

        return response()->json(config('status.messages.401'), 401);

    }

}
