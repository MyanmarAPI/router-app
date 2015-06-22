<?php namespace App\Http\Middleware;

use Closure;

use App\Analytics\Ga;

class AnalyticsMiddleware {

    private $ga;

    public function __construct()
    {
        $this->ga = new Ga;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $request_path = $request->path();

        //Ga 
        $this->ga->sendPageview($request_path);

        return $response;
    }

}
