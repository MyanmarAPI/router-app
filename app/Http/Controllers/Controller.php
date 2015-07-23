<?php namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Request;
use App\Jobs\SendAnalytics;
use App\Jobs\SaveReport;

class Controller extends BaseController
{

    /**
     * List of Endpoint avaliable
     *
     * @return json
     * @author 
     **/
    public function endpointList()
    {
        $endpoints = config('endpoints');

        $endpoints = array_map(function($ep){
            $ep_data = [
                'name' => $ep["name"],
                'desc' => $ep["desc"],
                'docs' => $ep["docs"]
            ];
            return $ep_data;
        }, $endpoints);
        
        return response()->json($endpoints);
    }

    /**
     * Get Endpoint Data
     *
     * @param  $request Illuminate\Http\Request Request Object
     * @param  $endpoint (string) Name of the Endpoint
     * @param  $resource (string) uri of endpoint resource
     * @return json
     * @author 
     **/
    public function getEndpoint(Request $request, $endpoint, $resource = null) {

    	$endpoints = config('endpoints');

        $request_app = $request->session()->get('resquest_user');

        if (!$resource) {
            $resource = "/";
        }

    	if (array_key_exists($endpoint, $endpoints)) {

    		$client = new Client(['base_url' => $endpoints[$endpoint]['base']]);

            try {

                $response = $client->get($resource, [
                    'headers' => [
                        'X-API-KEY' => $endpoints[$endpoint]['API_KEY'],
                        'X-API-SECRET' => $endpoints[$endpoint]['API_SECRET']
                    ],
                    'query' => $request->query()
                ]);
                
            } catch (ClientException $e) {

                $response = $e->getResponse();
                
            } catch (ConnectException $e) {

                return response()->json(config('status.messages.500'), 500);

            }

            switch ($response->getStatusCode()) {

                case 500:
                    return response()->json(config('status.messages.500'), 500);
                    break;
                
                case 401:
                    return response()->json(config('status.messages.401'), 401);
                    break;

                case 404:
                    return response()->json(config('status.messages.404'), 404);
                    break;

                case 200:

                    $this->pushAnalyticJobs($request, $endpoint, $resource, $request_app);

                    return $response->json();

                    break;
            }
    		
    	} 

    	return response()->json(config('status.messages.404'), 404);

    }

    /**
     * Generate User Token
     *
     * @return void
     * @author 
     **/
    public function generateToken(Request $request)
    {
        if ($request->has('api_key')) {

            $api_key = $request->input('api_key');

            $auth_url = config('app.auth');
            
            $client = new Client(['base_url' => $auth_url['base_url']]);

            $headers = ['X-API-KEY'         => env('AUTH_APP_KEY'),
                        'X-API-SECRET'  => env('AUTH_APP_SECRET')];

            try {

                $token_res = $client->get($auth_url['token_uri'].'/'.$api_key, [
                                    'headers' => $headers
                                ]);

            } catch (ClientException $e) {

                $token_res = $e->getResponse();

            }

            switch ($token_res->getStatusCode()) {
                case 200:
                    $res_data = $token_res->json();
                    return response_ok([
                        '_meta' => [
                            'status' => 'ok',
                            'count' => 1,
                            'api_version' => 1,
                        ],
                        'data' => [
                            'token' => $res_data['token']
                        ]
                    ]);
                    break;

                case 500:
                    return response_error("Something wrong with token generate process");
                    break;

                default:
                    return response()->json($token_res->json(), $token_res->getStatusCode());
                    break;     
                
            }

        }

        return response_missing("You must pass your 'api_key' to generate user token.");
    }

    /**
     * Push Analytic Data to Queue Jobs
     *
     * @return void
     * @author 
     **/
    private function pushAnalyticJobs(Request $request, $endpoint, $resource, $request_app)
    {

        if (env('GA_ANALYTIC')) {

            //Push Queue Job for Google Analytics
            $this->dispatch(new SendAnalytics($request->path(), $request_app));   

        }

        if (env('ANALYTIC_REPORT')) {
            
            //Push Queue Job for Internal Analytic Report
            $resource_info = [
                'endpoint' => $endpoint,
                'resource' => $resource,
                'query' => $request->query(),
                'path' => $request->path()
            ];

            $this->dispatch(new SaveReport($request->getClientIp(), $request_app, $resource_info));

        }

    }

}
