<?php
declare(strict_types=1);
error_reporting(-1);
session_start();

$url = \trim($_SERVER['REQUEST_URI'], '/');
if (empty($url)) {
	$metaTitle = 'DEMO-SHOP';
	$template = 'shop/index';
} elseif ($url === 'auth/registry') {
	$metaTitle = 'REGISTRY';
	$template = 'auth/registry';
} elseif ($url === 'auth/login') {
	$metaTitle = 'LOGIN';
	$template = 'auth/login';
} elseif ($url === 'auth/profile') {
    $metaTitle = 'PROFILE';
    $template = 'auth/profile';
}


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
require __DIR__ . "/views/{$action['view']}.php";
$content = ob_get_clean();

require __DIR__ . '/views/layouts/default.php';
