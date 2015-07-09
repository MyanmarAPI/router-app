<?php namespace App\Model;

/**
 * Report Model Class
 *
 * @package Election API Router
 * @author 
 **/

use MongoDate;
use Carbon\Carbon;

class Report extends Model
{

	protected $collection = 'api_report';

	/**
	 * New API Report
	 *
	 * @return App\Model
	 * @author 
	 **/
	public function newReport($ip_address, $app_info, $resource_info)
	{
		unset($resource_info['query']['api_key']);

		$time = $this->prepareTime();

		return $this->getCollection()->insert([
			'user_id' => $app_info['user_id'],
			'api_key' => $app_info['key'],
			'app_id' => $app_info['_id'],
			'path' => $resource_info['path'],
			'endpoint' => $resource_info['endpoint'],
			'resource' => $resource_info['resource'],
			'ip_address' => $ip_address,
			'date' => new MongoDate(strtotime($time['now']." 00:00:00")),
			'daily' => 1,
			'hour' => [
				$time['hour'] => 1
			],
			'minute' => [
				$time['minute'] => 1
			]
		]);
	}

	/**
	 * Update Report on current request
	 *
	 * @return App\Model
	 * @author 
	 **/
	public function updateReport($ip_address, $app_info, $resource_info)
	{
		$time = $this->prepareTime();

		return $this->getCollection()->update([
				'api_key' => $app_info['key'],
				'path' => $resource_info['path'],
				'ip_address' => $ip_address,
				'date' => new MongoDate(strtotime($time['now']." 00:00:00"))
			], ['$inc' => [
					'daily' => 1, 
					'hour.'.$time['hour'] => 1, 
					'minute.'.$time['minute'] => 1
					]
				]);

	}

	/**
	 * Prepare Time for Report
	 *
	 * @return void
	 * @author 
	 **/
	private function prepareTime()
	{
		$now = Carbon::now();

		$hour = $now->hour;

		$minute = ($hour*60)+$now->minute;

		return [
			'nowObj' => $now,
			'now' => $now->toDateString(),
			'hour' => $hour,
			'minute' => $minute
		];
	}

	/**
	 * Check Report already exist or not
	 *
	 * @return App\Model
	 * @author 
	 **/
	public function checkReport($ip_address, $app_info, $resource_info)
	{
		$now = Carbon::now();

		$report = $this->getCollection()->first([
				'api_key' => $app_info['key'],
				'path' => $resource_info['path'],
				'ip_address' => $ip_address,
				'date' => new MongoDate(strtotime($now->toDateString()." 00:00:00"))
			]);
		
		if ($report) {

			return true;

		}

		return false;
	}

} // END class Report