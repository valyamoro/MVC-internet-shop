<?php
declare(strict_types=1);
error_reporting(-1);
session_start();

$url = \trim($_SERVER['REQUEST_URI'], '/');

//if (empty($url)) {
//	$metaTitle = 'DEMO-SHOP';
//	$template = 'shop/index';
//} elseif ($url === 'registry') {
//	$metaTitle = 'REGISTRY';
//	$template = 'auth/registry';
//} elseif ($url === 'login') {
//	$metaTitle = 'LOGIN';
//	$template = 'auth/login';
//}


$routes = require __DIR__ . '/config/routes.php';

$action = [];
foreach ($routes as $pattern => $value) {
	if (\preg_match($pattern, $url)) {
		$action = $value;
		break;
	}
}

if (!empty($action['model'])) {
	require __DIR__ . "/src/models/{$action['model']}_model.php";
}

ob_start();
$name = 'Ivan';
require __DIR__ . "/views/{$action['view']}.php";
$content = ob_get_clean();

require __DIR__ . '/views/layouts/default.php';
