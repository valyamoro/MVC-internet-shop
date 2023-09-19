<?php

return [
    '#^auth/profile?#' => [
        'model' => '',
        'view' => '/auth/profile',
    ],
	'#^registry?#' => [
		'model' => 'registry',
		'view' => '',
	],
	'#^auth/registry?#' => [
		'model' => '',
		'view' => '/auth/registry',
	],

	'#^auth/login?#' => [
		'model' => '',
		'view' => 'auth/login',
	],

	'#^#' => [
		'model' => 'index',
		'view' => 'shop/index',
	],

];
