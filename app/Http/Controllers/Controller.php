<?php namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Config;

class Controller extends BaseController
{
    public function getEndpoint($endpoint) {

    	$endpoints = config('endpoints');

    	if (array_key_exists($endpoint, $endpoints)) {
    		return response()->json($endpoints);
    	} 

    	return response()->json([
    		'status' => 404,
    		'message' => 'Request url not found.'
    	], 404);

    }
}
