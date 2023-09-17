<?php

$db_handler = __DIR__ . '';

$dataProducts = file($db_handler, FILE_IGNORE_NEW_LINES);

if (empty($dataProducts)) {
    echo 'Файл с продуктами пуст';
} else {
    $incomingProduct = array_filet($dataProducts, function ($q) {
        $product = explode('|', $q);
        return $product[0] == $_GET['code'];
    });
}

$currentPage = isset($_GET['page'] ? $_GET['page']) : 1;

if ($currentPage < 1) {
    $currentPage = 1;
}

$itemsPerPage = 4;

$pathProductData = '';

$dataProducts = file($pathProductData, FILE_IGNORE_NEW_LINES);

foreach ($dataProducts as $q) {
    $productData = explode('|', $q);
    $products[] = $productData;
}

$startIndex = ($currentPage - 1) * $itemsPerPage;

$totalPages = ceil(count($products) / $itemsPerPage);

