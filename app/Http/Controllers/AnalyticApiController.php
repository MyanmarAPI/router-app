<?php 

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Model\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use MongoDate;

class AnalyticApiController extends BaseController
{

	private $model;

	private $filterKeys = ['user_id', 'api_key', 'endpoint', 'token'];

	public function __construct(Report $report)
	{
		$this->model = $report;
	}

	public function getAllDefaults(Request $request)
	{
		$results['hourly'] = $this->getHourly($request, false);
		$results['daily'] = $this->getDaily($request, false);
		$results['monthly'] = $this->getMonthly($request, false);

		//$results['total_hits'] = $this->getTotalHits($request, false);

		return response()->json($results);
	}

	public function getDaily(Request $request, $json = true)
	{

		$filters = $this->getFilterKeys($request);

		$from = $request->input('from');

		$to = $request->input('to');

		if (!$from && !$to) {
			$now = Carbon::now();
			$to = $now->toDateString();
			$from = $now->subDays(10)->toDateString();
		}

		$result["date_range"] = [
			"from" => $from,
			"to" => $to
		];

		$result["data"] = $this->model->getDaily($from, $to, $filters);

		if ($request->input('thd')) {
			$result["total_hits"] = $this->model->getTotalHits([
				'date' => [
					'$gte' => new MongoDate(strtotime($from." 00:00:00")),
					'$lte' => new MongoDate(strtotime($to." 00:00:00"))
				]
			], ['endpoint', 'api_key']);
		}

		if (!$json) {
			return $result;
		}

		return response()->json($result);
	}

	public function getHourly(Request $request, $json = true)
	{
		$filters = $this->getFilterKeys($request);

		$date = $request->input('date');

		if (!$date) {
			$date = Carbon::now()->toDateString();
		}

		$result["date_range"] = [
			"date" => $date
		];

		$result["data"] = $this->model->getHourly($date, $filters);

		if ($request->input('thd')) {
			$result["total_hits"] = $this->model->getTotalHits([
				'date' => new MongoDate(strtotime($date." 00:00:00"))
			]);
		}

		if (!$json) {
			return $result;
		}

		return response()->json($result);
	}

	public function getMonthly(Request $request, $json = true)
	{

		$filters = $this->getFilterKeys($request);

		$from = $request->input('from');

		$to = $request->input('to');

		if (!$from && !$to) {
			$now = Carbon::now();
			$to = $now->year.'-'.$now->month;
			$from_time = $now->subYear();
			$from = $from_time->year.'-'.$from_time->month;
		}

		$result["date_range"] = [
			"from" => $from,
			"to" => $to
		];

		$result["data"] = $this->model->getMonthly($from, $to, $filters);

		if ($request->input('thd')) {
			$result["total_hits"] = $this->model->getTotalHits([
			'date' => [
				'$gte' => new MongoDate(strtotime($from."-01 00:00:00")),
				'$lte' => new MongoDate(strtotime($to." 00:00:00"))
				]
			], ['endpoint', 'api_key']);	
		}

		if (!$json) {
			return $result;
		}

		return response()->json($result);

	}

	public function getTotalHits(Request $request, $json = true)
	{
		$filters = $this->getFilterKeys($request);

		$hit_contents = [];

		if ($request->has('hit_contents') && $request->input('hit_contents')) {
			$hit_contents = explode("|", $request->input('hit_contents'));
		}

		$result = $this->model->getTotalHits($filters, $hit_contents);

		if (!$json) {
			return $result;
		}

		return response()->json($result);

	}

	private function getFilterKeys(Request $request)
	{
		return array_filter(array_intersect_key($request->query(), array_flip($this->filterKeys)));
	}

	/*private function checkDateFormat($date)
	{

	}*/

}