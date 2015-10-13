<?php

namespace App\Providers;

use App\Queue\MongoLiteFailedJobProvider;
use Illuminate\Queue\QueueServiceProvider as DefaultServiceProvider;

class QueueServiceProvider extends DefaultServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->alias(
            'queue', 'Illuminate\Contracts\Queue\Factory'
        );

        $this->app->alias(
            'queue.connection', 'Illuminate\Contracts\Queue\Queue'
        );
    }

    /**
     * Register the failed job services.
     *
     * @return void
     */
    protected function registerFailedJobServices()
    {
        $this->app->singleton('queue.failer', function ($app) {
            return new MongoLiteFailedJobProvider();
        });
    }
}
