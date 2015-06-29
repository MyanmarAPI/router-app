<?php namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use App\Jobs\SendAnalytics;

class Controller extends BaseController
{
    public function getEndpoint(Request $request, $endpoint, $resource) {

    	$endpoints = config('endpoints');

    	if (array_key_exists($endpoint, $endpoints)) {

    		$client = new Client(['base_url' => $endpoints[$endpoint]['base']]);

            try {

                $response = $client->get('api/'.$resource, [
                    'query' => $request->query()
                ]);
                
            } catch (ClientException $e) {

                $response = $e->getResponse();
                
            }

            //Push Queue Job for Analytics
            $this->dispatch(new SendAnalytics($request->path()));

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
                    return $response->json();
                    break;
            }
    		
    	} 

    	return response()->json(config('status.messages.404'), 404);

    }
}
