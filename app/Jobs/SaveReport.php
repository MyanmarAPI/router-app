<?php 

namespace App\Jobs;

use App\Jobs\Job;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use App\Analytics\ApiReport;

class SaveReport extends Job implements SelfHandling, ShouldQueue
{
	use SerializesModels;

	private $ip_address;

	private $request_app;

	private $resource_info;

	/**
	 * Create a new job instance.
	 *
	 * @param  User  $user
	 * @return void
	 */
	public function __construct($ip_address, $request_app, $resource_info)
	{
		$this->ip_address = $ip_address;

		$this->request_app = $request_app;

		$this->resource_info = $resource_info;
	}

	/**
	 * Execute the job.
	 *
	 * @param  Request  $request
	 * @return void
	 */
	public function handle(ApiReport $report)
	{
		if ($this->attempts() > 3) {
			$this->delete();
		} else {
			$report->makeReport($this->ip_address, $this->request_app, $this->resource_info);
		}
	}

}