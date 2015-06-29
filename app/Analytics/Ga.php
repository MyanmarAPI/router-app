<?php namespace App\Analytics;

/**
 * Google Analytics for API
 *
 * @package Election API Router
 * @author 
 **/

use TheIconic\Tracking\GoogleAnalytics\Analytics;

class Ga 
{
	/**
	 * Send Page View Report to Google Analytics
	 *
	 * @return void
	 **/
	public function sendPageView($path) {

		$analytics = new Analytics();

		$tracking_id = config('app.analytics.ga.tracking_id');
		$version = config('app.analytics.ga.version');

		$analytics
		    ->setProtocolVersion($version)
		    ->setTrackingId($tracking_id)
		    ->setClientId('12345678') //ToDo : replace with uuid
		    ->setQueueTime(1000)
		    ->setDocumentPath($path);

		$sent = $analytics->sendPageview();

	}

} // END class Ga 

