<?php

// Функция для корректного вывода информации.
function dump(mixed $data): void
{
    echo '<pre>'; \print_r($data); echo '</pre>';
}

// Функция для обработки данных и вывода их во view
function render(string $view, array $data = []): string
{
    // Создание переменных из индексов массива.
    \extract($data);

    // Путь до "представления"
    $viewPath = __DIR__ . "/../../views/{$view}.php";

    // Если файла со страницей не существует, то возвращает 404.
    if (!\file_exists($viewPath)) {
        $code = 404;
        \http_response_code($code);
        require __DIR__ . "/../../views/errors/{$code}.php";
        die;
    }
    \ob_start();
    include $viewPath;

    return \ob_get_clean();
}

function redirect(string $http = ''): never
{
    $redirect = $http ?? $_SERVER['HTTP_REFERER'] ?? '/';

    \header("Location: {$redirect}");
    die;
}

