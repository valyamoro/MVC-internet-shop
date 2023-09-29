<?php

$metaTitle = 'DEMO-SHOP';

// Переменная с продуктами.
$products = getProducts();
// Получаем текущую страницу.
$currentPage = getCurrentPage();
// Кол-во товаров на страницу.
$itemPerPage = 10;

// Номер первого товара на странице.
$startIndex = ($currentPage - 1) * $itemPerPage;

// Общее кол-во страниц
$totalPages = ceil(count($products) / $itemPerPage);

// Кол-во товаров на странице.
$productsOnPage = array_slice($products, $startIndex, $itemPerPage);

// Отправляем на страницу с продуктами нужные данные.
$content = render($currentAction['view'], [
    'products' => $products,
    'productsOnPage' => $productsOnPage,
    'totalPages' => $totalPages,
    'currentPage' => $currentPage,

]);