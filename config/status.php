<?php 

return [
	'messages' => [
		'401' => [
			'errors' => [
				'message' => 'Authentication failed.',
				'type' => 'unauthorized'
			]
		],
		'500' => [
			'errors' => [
				'message' => 'Internal Server Error.',
				'type' => 'server_error'
			]
		],
		'404' => [
			'errors' => [
				'message' => 'Request url not found.',
				'type' => 'invalid_request'
			]
		]
	]
];