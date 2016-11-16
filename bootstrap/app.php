<?php

require_once __DIR__.'/../vendor/autoload.php';

Dotenv::load(__DIR__.'/../');

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
	realpath(__DIR__.'/../')
);

$app->withFacades();

// $app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    'Illuminate\Contracts\Debug\ExceptionHandler',
    'App\Exceptions\Handler'
);

$app->singleton(
    'Illuminate\Contracts\Console\Kernel',
    'App\Console\Kernel'
);

/*|-------------------------------------------
  | Configuration Files
  |-------------------------------------------
 */
$app->configure('endpoints');
$app->configure('app');
$app->configure('queue');
$app->configure('status');
$app->configure('database');
$app->configure('mongo_lite');
/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->routeMiddleware([
	'auth' => 'App\Http\Middleware\AuthMiddleware',
	'app2app' => 'Hexcores\Api\Http\Middleware\VerifyApiRequestHeader',
    'cros' => 'App\Http\Middleware\CORSMiddleware',
    'etag' => 'App\Http\Middleware\ETagMiddleware'
]);

 $app->middleware([
    'Illuminate\Cookie\Middleware\EncryptCookies',
    'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
    'Illuminate\Session\Middleware\StartSession',
    'Illuminate\View\Middleware\ShareErrorsFromSession',
    //'Laravel\Lumen\Http\Middleware\VerifyCsrfToken',
 ]);

// $app->routeMiddleware([

// ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register('Hexcores\MongoLite\Laravel\MongoLiteServiceProvider');
$app->register('App\Providers\AppServiceProvider');
$app->register('App\Providers\QueueServiceProvider');
$app->register('App\Providers\LogServiceProvider');

// Remove default queue binding.
// Because lumen's queue failed used the database (mysql, pgsql),
// So we need to change this process to mongodb using mongo lite package.
// ** Added By Nyan Lynn Htut at Oct-13-2015 **
unset($app->availableBindings['queue']);
unset($app->availableBindings['queue.connection']);
unset($app->availableBindings['Illuminate\Contracts\Queue\Factory']);
unset($app->availableBindings['Illuminate\Contracts\Queue\Queue']);


/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

require __DIR__.'/../app/Http/routes.php';

return $app;
