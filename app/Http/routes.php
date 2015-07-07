<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$app->get('/', function() use ($app) {
    return response()->json([
    	'errors' => [
    		'message' => 'Please check '.config('app.docs_url').' for api documentation.',
    		'type' => 'invalid_request'
    	]
    ], 404);
});

$app->group(['middleware' => 'auth'], function($app)
{
	$app->get('{endpoint}', [
        'as' => 'api.endpoint',
        'uses' => 'App\Http\Controllers\Controller@getEndpoint'
    ]);

    $app->get('{endpoint}/{resource}', [
        'as' => 'api.endpoint.resource',
        'uses' => 'App\Http\Controllers\Controller@getEndpoint'
    ]);
});

$app->get('endpoints', [
    'as' => 'api.endpoint.list',
    'uses' => 'App\Http\Controllers\Controller@endpointList'
]);

