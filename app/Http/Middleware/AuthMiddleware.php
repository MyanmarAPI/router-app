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
        if ( ! $request->has('token')) {
            return response()->json(config('status.messages.404'), 404);
        }

        $token = $request->input('token');

        //Check for API Authentication
    	$authUrl = config('app.auth');

        $client = new Client(['base_url' => $authUrl['base_url']]);

        //ToDo : Header or Auth Type need to change later according to Main App Authentication
        $headers = ['X-API-KEY' 		=> env('AUTH_APP_KEY'),
			        'X-API-SECRET'  => env('AUTH_APP_SECRET')];

        try {

            $response = $client->get($authUrl['uri'].'/'.$token, [
                            'headers' => $headers
                        ]);

        } catch (ClientException $e) {

            $response = $e->getResponse();

        }

        switch ($response->getStatusCode()) {

            case 401:
                return response()->json(config('status.messages.401'), 401);
                break;
            
            case 200:
                $request->session()->put('request_user', $response->json());
                return $next($request); 
            break;

        } 

        return response()->json(config('status.messages.401'), 401);

    }

}
