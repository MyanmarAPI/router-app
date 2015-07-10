<?php

/*|-------------------------------------------
  | Endpoints
  | 
  | 'endpoint-uri' => [
  | 	'name' => 'Name of the endpoint',
  |		'desc' => 'Description of the endpoint',
  |		'base' => 'http://endpoint-base-url.com/',
  |		'docs' => 'http://endpoint-docs-url.com',
  |		'API_KEYS' => 'SomeRandomKey', //Leave it blank if your endpoint isn't filter with these keys.
  |		'API_SECRET' => 'SomeRandomSecret',
  |	]
  |-------------------------------------------
 */

return [
	'candidates' => [
		'name' => 'Candidate API',
		'desc' => '',
		'base' => '',
		'docs' => '',
		'API_KEYS' => env('CANDIDATES_KEY'),
		'API_SECRET' => env('CANDIDATES_SECRET')
	],
	'faq' => [
		'name' => 'FAQ API',
		'desc' => '',
		'base' => '',
		'docs' => '',
		'API_KEYS' => env('FAQ_KEY'),
		'API_SECRET' => env('FAQ_SECRET')
	],
	'geolocation' => [
		'name' => 'Geolocation API',
		'desc' => '',
		'base' => '',
		'docs' => '',
		'API_KEYS' => '',
		'API_SECRET' => ''
	],
	'party' => [
		'name' => 'Party API',
		'desc' => 'Return Party List On Myanmar Election',
		'base' => 'http://107.167.184.145:8080',
		'docs' => '',
		'API_KEYS' => '', 
		'API_SECRET' => ''
	],

];
