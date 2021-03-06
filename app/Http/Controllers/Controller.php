<?php namespace App\Http\Controllers;

use Config;
use Rabbit;
use GuzzleHttp\Client;
use App\Jobs\SaveReport;
use App\Jobs\SendAnalytics;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Laravel\Lumen\Routing\Controller as BaseController;
use Carbon\Carbon;

class Controller extends BaseController
{

    /**
     * List of Endpoint avaliable
     *
     * @return \Illuminate\Http\Response
     **/
    public function endpointList()
    {
        $endpoints = config('endpoints');

        $endpoints = array_map(function($endpoint){
            return [
                'name' => $endpoint["name"],
                'desc' => $endpoint["desc"],
                'docs' => $endpoint["docs"]
            ];
        }, $endpoints);
        
        return response()->json($endpoints);
    }

    /**
     * Check endpoints status
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string                   $endpoint Name of the Endpoint
     * @param  string                   $resource Uri of endpoint resource
     * @return \Illuminate\Http\Response
     **/
    public function status(Request $request) {

        $endpoints = config('endpoints');
        $statuses=[];
        $allgood=true;
        

        foreach ($endpoints as $key=>$endpoint) {
             
            $response=null;
            $client = new Client();

            try {

                $response = $client->get($endpoint['base'].$endpoint['status'], [
                    'headers' => [
                        'X-API-KEY' => $endpoint['API_KEY'],
                        'X-API-SECRET' => $endpoint['API_SECRET']
                    ]
                ]);
                
            } catch (ClientException $e) {
                $allgood=false;
                $response = $e->getResponse();
                
            } catch (ConnectException $e) {
                $allgood=false;
                $statuses[$key]=config('status.messages.500');

            } catch (\Exception $e) {
                $allgood=false;
                $statuses[$key]=config('status.messages.500');

            }
            if($response!==null){
                switch ($response->getStatusCode()) {

                    case 500:
                        $allgood=false;
                        $statuses[$key]=config('status.messages.500');
                        break;
                    
                    case 401:
                        $allgood=false;
                        $statuses[$key]=config('status.messages.401');
                        break;

                    case 404:
                        $allgood=false;
                        $statuses[$key]=config('status.messages.404');
                        break;

                    case 200:
                        $statuses[$key]='OK';
                        break;
                }
            }
            
            
        } 

        return response()->json([
                    'all_endpoint_good'=>$allgood,
                    'endpoints'=>$statuses
                ]);

    }

    /**
     * Get Endpoint Data
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string                   $endpoint Name of the Endpoint
     * @param  string                   $resource Uri of endpoint resource
     * @return \Illuminate\Http\Response
     **/
    public function getEndpoint(Request $request, $endpoint, $resource = "") {

    	$endpoints = config('endpoints');

        //$requestApp = $request->session()->get('request_user');
        
        $requestApp = app('request_user');

    	if (array_key_exists($endpoint, $endpoints)) {

    		$client = new Client(['base_url' => $endpoints[$endpoint]['base']]);

            try {

                $response = $client->get($resource, [
                    'headers' => [
                        'X-API-KEY' => $endpoints[$endpoint]['API_KEY'],
                        'X-API-SECRET' => $endpoints[$endpoint]['API_SECRET']
                    ],
                    'query' => $this->getQueryFromRequest($request)
                ]);
                
            } catch (ClientException $e) {

                $response = $e->getResponse();
                
            } catch (ConnectException $e) {

                return response()->json(config('status.messages.500'), 500);

            } catch (\Exception $e) {
                
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

                    $this->pushAnalyticJobs($request, $endpoint, $resource, $requestApp, Carbon::now());

                    $responseJson = json_decode($response->getBody($asString = TRUE));

                    if ( $this->requestForZawgyi($request)) {

                        $zawgyi = Rabbit::uni2zg(json_encode($responseJson, JSON_UNESCAPED_UNICODE));

                        $responseJson = json_decode($zawgyi);

                        if(isset($responseJson->meta)) {

                            $responseJson->meta->format = 'zawgyi';

                        }

                        //Format meta replace for _meta
                        if (isset($responseJson->_meta->format)) {

                            $responseJson->_meta->format = 'zawgyi';
                            $responseJson->_meta->unicode = false;

                        }

                    } else {

                        if(isset($responseJson->meta)) {

                            $responseJson->meta->format = 'unicode';
                            
                        }

                    }

                    // Tweak for Retrofit Mapper
                    /*if ( isset($responseJson['meta']['pagination']['links'])) {
                        $links = $responseJson['meta']['pagination']['links'];

                        if ( empty($links)) {
                            $responseJson['meta']['pagination']['links'] = new \stdClass();
                        }
                    }*/

                    return response()->json($responseJson);

                    break;
            }
    		
    	} 

    	return response()->json(config('status.messages.404'), 404);

    }

    /**
     * Generate User Token
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     **/
    public function generateToken(Request $request)
    {
        if ($request->has('api_key')) {

            $apiKey = $request->input('api_key');

            $authUrl = config('app.auth');
            
            $client = new Client(['base_url' => $authUrl['base_url']]);

            $headers = ['X-API-KEY'         => env('AUTH_APP_KEY'),
                        'X-API-SECRET'  => env('AUTH_APP_SECRET')];

            try {

                $tokenResponse = $client->get($authUrl['token_uri'].'/'.$apiKey, [
                                    'headers' => $headers
                                ]);

            } catch (ClientException $e) {

                $tokenResponse = $e->getResponse();

            }

            switch ($tokenResponse->getStatusCode()) {
                case 200:
                    $responseData = $tokenResponse->json();

                    return response_ok([
                        '_meta' => [
                            'status' => 'ok',
                            'count' => 1,
                            'api_version' => 1,
                        ],
                        'data' => [
                            'token' => $responseData['token']
                        ]
                    ]);
                    break;

                case 500:
                    return response_error("Something wrong with token generate process");
                    break;

                default:
                    return response()->json($tokenResponse->json(), $tokenResponse->getStatusCode());
                    break;     
                
            }

        }

        return response_missing("You must pass your 'api_key' to generate user token.");
    }

    /**
     * Push Analytic Data to Queue Jobs
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string                   $endpoint Name of the Endpoint
     * @param  string                   $resource Uri of endpoint resource
     * @param  array                    $requestApp Requested application.
     * @return void
     **/
    private function pushAnalyticJobs(Request $request, $endpoint, $resource, $requestApp, $request_time = null)
    {

        if (env('GA_ANALYTIC')) {

            //Push Queue Job for Google Analytics
            $this->dispatch(new SendAnalytics($request->path(), $requestApp));   

        }

        if (env('ANALYTIC_REPORT')) {
            
            //Push Queue Job for Internal Analytic Report
            $resourceInfo = [
                'endpoint' => $endpoint,
                'resource' => $resource,
                'query' => $request->query(),
                'path' => $request->path()
            ];

            $this->dispatch(new SaveReport($request->getClientIp(), $requestApp, $resourceInfo, $request_time));
        }
    }

    /**
     * Check request has a zawgyi font trigger.
     *
     * @param  \Illuminate\Http\Request $request
     * @return boolean
     */
    private function requestForZawgyi(Request $request)
    {
        return ($request->has('font') && $request->input('font') == 'zawgyi');
    }
    
    /**
     * Get query param lists from request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    private function getQueryFromRequest(Request $request)
    {
        $query = $request->query();

        // If request is not for zawgyi, 
        // or doesn't include 'q' query param (q is search keyword)
        // return without convert.
        if ( ! $this->requestForZawgyi($request) || ! $request->has('q')) {
            return $query;
        }
        
        // Convert zawgyi to unicode for search keyword.
        $query['q'] = Rabbit::zg2uni($query['q']);

        return $query;
    }

}
