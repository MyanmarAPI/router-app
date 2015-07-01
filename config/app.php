<?php 

return [
	'auth' => [
		'base_url' => 'http://maepaysoh.org/', //Auth App Base url
		'uri' => 'api/v1/authenticate' //Auth Uri
	],
	'docs_url' => 'http://developer.mmelection.dev/docs', //Main Documentation Url
	'analytics' => [
		'ga' => [
			'version' => 1,
			'tracking_id' => 'UA-64355859-1' //Change Google Analytic Tracking ID
		]
	],

	/*
	|--------------------------------------------------------------------------
	| Encryption Key
	|--------------------------------------------------------------------------
	|
	| This key is used by the Illuminate encrypter service and should be set
	| to a random, 32 character string, otherwise these encrypted strings
	| will not be safe. Please do this before deploying an application!
	|
	*/

	'key' => 'oum2lb7hfes6NGdr2FuLcW4gxQhdgTjW',

	'cipher' => MCRYPT_RIJNDAEL_128	
];