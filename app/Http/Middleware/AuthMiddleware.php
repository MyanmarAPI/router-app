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
        if (!$request->has('api_key')) {
            return response()->json(config('status.messages.404'), 404);
        }

        $api_key = $request->input('api_key');

        //Check for API Authentication
    	$auth_url = config('app.auth');

        $client = new Client(['base_url' => $auth_url['base_url']]);

        //ToDo : Header or Auth Type need to change later according to Main App Authentication
        $headers = ['X-API-KEY' 		=> env('API_APP_KEY'),
			        'X-API-SECRET'  => env('API_APP_SECRET')];

        try {

            $auth_res = $client->get($auth_url['uri'].'/'.$api_key, [
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
                $request->session()->put('resquest_user', $auth_res->json());
                return $next($request); 
            break;

        } 

        return response()->json(config('status.messages.401'), 401);

    }

}
