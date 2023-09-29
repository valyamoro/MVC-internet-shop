<?php

return [
    '#^auth/profile?#' => [
        'controller' => 'profile',
        'model' => 'profile',
        'view' => '/auth/profile',
    ],
	'#^registry?#' => [
		'model' => 'registry',
		'view' => '',
	],
	'#^auth/registry?#' => [
        'controller' => 'registry',
		'model' => 'registry',
		'view' => '/auth/registry',
	],

	'#^auth/login?#' => [
        'controller' => 'login',
		'model' => 'login',
		'view' => 'auth/login',
	],

	'#^#' => [
        'controller' => 'index',
		'model' => 'index',
		'view' => 'shop/index',
	],

];
