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
	public function newReport($ip_address, $app_info, $resource_info, $request_time = null)
	{
		if (isset($resource_info['query']['api_key'])) {
			unset($resource_info['query']['api_key']);
		}

		if (isset($resource_info['query']['token'])) {
			unset($resource_info['query']['token']);
		}

		$time = $this->prepareTime($request_time);

		return $this->getCollection()->insert([
			'user_id' => $app_info['user_id'],
			'api_key' => $app_info['app_key'],
			'user_token' => $app_info['token'],
			//'app_id' => $app_info['_id'],
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
	public function updateReport($ip_address, $app_info, $resource_info, $request_time = null)
	{
		$time = $this->prepareTime($request_time);

		return $this->getCollection()->update([
				'api_key' => $app_info['app_key'],
				'path' => $resource_info['path'],
				//'ip_address' => $ip_address,
				'user_token' => $app_info['token'],
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
	private function prepareTime($now)
	{
		if (!$now) {
			$now = Carbon::now();
		}

		$hour = $now->hour;

		$minute = ($hour*60)+$now->minute;

		if ($hour == 0) {
			$hour = "00";
		}

		if ($minute == 0) {
			$minute = "00";
		}

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
				'api_key' => $app_info['app_key'],
				'path' => $resource_info['path'],
				//'ip_address' => $ip_address,
				'user_token' => $app_info['token'],
				'date' => new MongoDate(strtotime($now->toDateString()." 00:00:00"))
			]);
		
		if ($report) {

			return true;

		}

		return false;
	}

	/**
	 * Get Hourly Hits
	 *
	 * @return void
	 * @author 
	 **/
	public function getHourly($date, $filter = [])
	{	

		$pipeline = [];
		$pipeline['_id'] = '$date';

		for ($i=0; $i < 24; $i++) { 

			if ($i == 0) {
				$pipeline[$date.' '.$i.':00'] = ['$sum' => '$hour.00'];
			} else {
				$pipeline[$date.' '.$i.':00'] = ['$sum' => '$hour.'.$i];	
			}

		}

		$match_query = [
				'date' => new MongoDate(strtotime($date." 00:00:00"))
			];

		$result = $this->collection()->aggregate(
			[
				'$match' => $this->getFinalMatchQuery($match_query, $filter)
			],
			[
				'$group' => $pipeline
			]);

		$graph_data = [];

		if (!empty($result['result'])) {

			unset($result['result'][0]['_id']);

			foreach ($result['result'][0] as $key => $value) {
				$graph_data[] = [
					'period' => $key,
					'value' => $value
				]; 
			}
		} else {

			for ($i=0; $i < 24 ; $i++) { 
				$graph_data[] = [
					'period' => $date.' '.$i.':00',
					'value' => 0
				]; 
			}

		}

		return $graph_data;

	}

	/**
	 * Get Daily Hit
	 *
	 * @return void
	 * @author 
	 **/
	public function getDaily($from, $to, $filter = [])
	{

		$match_query = ['date' => [
							'$gte' => new MongoDate(strtotime($from." 00:00:00")),
							'$lte' => new MongoDate(strtotime($to." 00:00:00"))
							]
						];

		$result = $this->collection()->aggregate(
			[
				'$match' => $this->getFinalMatchQuery($match_query, $filter)
			],
			[
				'$group' => [
					'_id' => '$date',
					'daily' => ['$sum' => '$daily']
				]
			]);

		$result_data = [];

		foreach ($result['result'] as $data) {
			$result_data[date("Y-m-d", $data['_id']->sec)] = $data['daily'];
		}	

		$from_date = $from;

		// Date Loop Snippet Credit 
		// http://www.if-not-true-then-false.com/2009/php-loop-through-dates-from-date-to-date-with-strtotime-function/
		
		$analytic_data = [];
	
		while (strtotime($from_date) <= strtotime($to)) {

			if (array_key_exists($from_date, $result_data)) {
				$hit_count = $result_data[$from_date];
			} else {
				$hit_count = 0;
			}

			$analytic_data[] = [
				'period' => $from_date,
				'value' => $hit_count
			];
			
			$from_date = date ("Y-m-d", strtotime("+1 day", strtotime($from_date)));
		}

		return $analytic_data;

	}

	/**
	 * Get Monthly Analytic Data
	 *
	 * @return void
	 * @author 
	 **/
	public function getMonthly($from, $to, $filter = [])
	{

		$end_to_date = date("Y-m-t", strtotime($to));

		$match_query = ['date' => [
							'$gte' => new MongoDate(strtotime($from."-01 00:00:00")),
							'$lte' => new MongoDate(strtotime($end_to_date." 00:00:00"))
							]
						];

		$result = $this->collection()->aggregate(
			[
				'$match' => $this->getFinalMatchQuery($match_query, $filter)
			],
			[
				'$group' => [
					'_id' => ['month' => ['$month' => '$date'], 'year' => ['$year' => '$date']],
					'hit' => ['$sum' => '$daily']
				]
			]
			);

		$result_data = [];

		foreach ($result['result'] as $d) {
			$result_data[$d['_id']['year'].'-'.$d['_id']['month']] = $d['hit'];
		}

		$from_date = $from;

		$analytic_data = [];

		while (strtotime($from_date) <= strtotime($to)) {

			if (array_key_exists($from_date, $result_data)) {
				$hit_count = $result_data[$from_date];
			} else {
				$hit_count = 0;
			}

			$analytic_data[] = [
				'period' => $from_date,
				'value' => $hit_count
			];

			$from_date = date ("Y-n", strtotime("+1 month", strtotime($from_date)));
		}

		return $analytic_data;

	}

	/**
	 * Get Total Hits
	 *
	 * @return void
	 * @author 
	 **/
	public function getTotalHits($filter, $contents = [])
	{
		$hit_contents = [
			'endpoint' => 'Endpoint',
			'api_key' => 'API Key',
			'ip_address' => 'IP Adress',
			'user_id' => 'User ID',
			'user_token' => 'User Token'
		];

		if (!empty($contents)) {

			$get_hits_contents = $contents;

		} else {

			$get_hits_contents = array_keys($hit_contents);

		}

		$data = [];

		foreach ($get_hits_contents as $content) {
			if (array_key_exists($content, $hit_contents)) {
				$result = $this->getHitbyInfo($content, $filter);
				$data[$content] = [
					'title' => $hit_contents[$content],
					'data' => $result,
					'count' => count($result)
				];
			}	
		}

		return $data;
	}

	/**
	 * Get Total Hits by Endpoint
	 *
	 * @return void
	 * @author 
	 **/
	public function getHitbyInfo($info = 'endpoint', $filter = [])
	{

		if (!empty($filter)) {

			$result = $this->collection()->aggregate(
				[
					'$match' => $filter
				],
				[
					'$group' => [
						'_id' => '$'.$info,
						'hit' => ['$sum' => '$daily']
					]
				]
			);
			
		} else {

			$result = $this->collection()->aggregate(
				[
					'$group' => [
						'_id' => '$'.$info,
						'hit' => ['$sum' => '$daily']
					]
			]);

		}
		

		$res_data = array_map(function($data){

			$data['info'] = $data['_id'];
			unset($data['_id']);
			return $data;

		}, $result['result']);

		usort($res_data, function($a, $b){
		    return $b['hit'] - $a['hit'];
		});

		return $res_data;
	}

	/**
	 * Merge Request Filter with default Match Query
	 *
	 * @return array
	 * @author 
	 **/
	private function getFinalMatchQuery($match_query, $filter)
	{
		if (!empty($filter)) {
			return array_merge($match_query, $filter);
		}

		return $match_query;
	}

} // END class Report