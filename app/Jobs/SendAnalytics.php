<?php

namespace App\Jobs;

use App\Jobs\Job;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use App\Analytics\Ga;

class SendAnalytics extends Job implements SelfHandling, ShouldQueue
{
    use SerializesModels;

    private $request_path;

    private $app_info;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     * @return void
     */
    public function __construct($request_path, $app_info)
    {

        $this->request_path = $request_path;

        $this->app_info = $app_info;

    }

    /**
     * Execute the job.
     *
     * @param  Request  $request
     * @return void
     */
    public function handle(Ga $ga)
    {

        $ga->sendPageview($this->request_path, $this->app_info);

    }
}