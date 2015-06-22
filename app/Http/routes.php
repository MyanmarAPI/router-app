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
    	'status' => 404,
    	'message' => 'Please check '.config('app.docs_url').' for api documentation.'
    ], 404);
});

$app->group(['middleware' => 'auth|analytics'], function($app)
{
	$app->get('{endpoint}', [
		'as' => 'api.endpoint',
		'uses' => 'App\Http\Controllers\Controller@getEndpoint'
	]);
});

//Temp Route for tesing Auth
$app->get('api/authenticate', function(){

	return response()->json([
		'status' => 200,
		'message' => 'Authentication success'
	], 200);

});



