<?php

/*|-------------------------------------------
  | Endpoints
  |
  | 'endpoint-uri' => [
  | 	'name' => 'Name of the endpoint',
  |		'desc' => 'Description of the endpoint',
  |		'base' => 'http://endpoint-base-url.com/candidate/v1/', //Inculde "/" at the end of base_url
  |		'docs' => 'http://endpoint-docs-url.com',
  |		'API_KEYS' => 'SomeRandomKey', //Leave it blank if your endpoint isn't filter with these keys.
  |		'API_SECRET' => 'SomeRandomSecret',
  |	]
  |-------------------------------------------
 */

return [
	'candidate' => [
		'name' => 'Candidate API',
		'desc' => '',
		'base' => env('CANDIDATES_BASE'),
		'docs' => '',
		'API_KEY' => env('CANDIDATES_KEY'),
		'API_SECRET' => env('CANDIDATES_SECRET'),
		'status'=>'/list'
	],
	'faq' => [
		'name' => 'FAQ API',
		'desc' => '',
		'base' => env('FAQ_BASE'),
		'docs' => '',
		'API_KEY' => env('FAQ_KEY'),
		'API_SECRET' => env('FAQ_SECRET'),
		'status'=>'/list'
	],
	'geo' => [
		'name' => 'Geolocation API',
		'desc' => '',
		'base' => env('GEO_BASE'),
		'docs' => '',
		'API_KEY' => env('GEO_KEY'),
		'API_SECRET' => env('GEO_SECRET'),
		'status'=>'/district?no_geo=true'
	],
	'party' => [
		'name' => 'Party API',
		'desc' => 'Return Party List On Myanmar Election',
		'base' => env('PARTY_BASE'),
		'docs' => '',
		'API_KEY' => '',
		'API_SECRET' => '',
		'status'=>'/'
	],
	'parliament' => [
		'name' => 'OMI Parliement',
		'desc' => 'Return Parliament Performance data',
		'base' => env('OMI_BASE'),
		'docs' => '',
		'API_KEY' => '',
		'API_SECRET' => '',
		'status'=>'/members/all.json'
	],

];
