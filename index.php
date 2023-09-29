<?php
declare(strict_types=1);
\error_reporting(-1);
\session_start();

// Получаем такущий url
$url = \trim($_SERVER['REQUEST_URI'], '/');

// Иниц. маршрута
$routes = require __DIR__ . '/config/routes.php';

$currentAction = [];
// Перебираем маршруты и находим нужный через текущий url
foreach ($routes as $pattern => $value) {
    if (\preg_match($pattern, $url)) {
        // Присваиваем model, controller, view
        $currentAction = $value;
        break;
    }
}

require __DIR__ . '/src/models/base_model.php';

//$dbh = connectionDB();
require __DIR__ . "/src/models/{$currentAction['model']}_model.php";

require __DIR__ . '/src/controllers/base_controller.php';
require __DIR__ . "/src/controllers/{$currentAction['controller']}_controller.php";
require __DIR__ . '/views/layouts/default.php';
