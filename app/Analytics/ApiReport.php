<?php namespace App\Analytics;

/**
 * Internal Report for API Request
 *
 * @package default
 * @author 
 **/

use App\Model\Report;
use Illuminate\Http\Request;

class ApiReport 
{

	private $report_model;

	public function __construct()
	{
		$this->report_model = new Report;
	}
	
	public function makeReport($ip_address, $app_info, $resource_info)
	{

		//Check Report record already exist or not
		if ($this->report_model->checkReport($ip_address, $app_info, $resource_info)) {

			# Record Exists (Total Request Increment)
			return $this->report_model->updateReport($ip_address, $app_info, $resource_info);
			
		}

		# Record doesn't exist. Create New record
		return $this->report_model->newReport($ip_address, $app_info, $resource_info);

	}

} // END class ApiReport 