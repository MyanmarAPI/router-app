<?php namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Request;
use App\Jobs\SendAnalytics;
//use App\Analytics\ApiReport;

class Controller extends BaseController
{

    public function endpointList()
    {
        $endpoints = config('endpoints');

        $endpoints = array_map(function($ep){
            unset($ep["base"]);
            return $ep;
        }, $endpoints);
        return response()->json($endpoints);
    }

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
                    //Header Keys
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

                    //Internal API Report
                    /*$report = new ApiReport;

                    $resource_info = [
                        'endpoint' => $endpoint,
                        'resource' => $resource,
                        'query' => $request->query()
                    ];*/

                    //$report->makeReport($request, $request_app, $resource_info);

                    //Push Queue Job for Google Analytics
                    $this->dispatch(new SendAnalytics($request->path(), $request_app));

                    return $response->json();
                    break;
            }
    		
    	} 

    	return response()->json(config('status.messages.404'), 404);

    }
}
