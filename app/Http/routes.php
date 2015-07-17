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

//Analytic API

$app->group([
        'prefix' => 'analytics/v1',
        'namespace' => 'App\Http\Controllers',
        'middleware' => 'app2app|cros'
        ], function($app){

    $app->get('all/today', [
        'as' => 'api.analytics.all.today',
        'uses' => 'AnalyticApiController@getAllDefaults'
    ]);

    $app->get('hourly', [
        'as' => 'api.analytics.hourly',
        'uses' => 'AnalyticApiController@getHourly'
    ]);

    $app->get('daily', [
        'as' => 'api.analytics.daily',
        'uses' => 'AnalyticApiController@getDaily'
    ]);

    $app->get('monthly', [
        'as' => 'api.analytics.monthly',
        'uses' => 'AnalyticApiController@getMonthly'
    ]);

});

//Endpoint List
$app->get('endpoints', [
    'as' => 'api.endpoint.list',
    'uses' => 'App\Http\Controllers\Controller@endpointList'
]);

//Authentication
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

