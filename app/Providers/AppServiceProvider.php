<?php namespace App\Providers;

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\RotatingFileHandler;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configureLogging($this->app);
        
    }

    /**
     * Configure logging with daily log.
     *
     * @param  \Illuminate\Contracts\Foundation\Application
     * @return void
     */
    protected function configureLogging($app)
    {
        $app->singleton('Psr\Log\LoggerInterface', function () {
            $handler = new RotatingFileHandler(storage_path('logs/lumen.log'), 3, Logger::DEBUG);
            $handler->setFormatter(new LineFormatter(null, null, true, true));

            return new Logger('lumen', [$handler]);
        });
    }

}
