<?php

$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

if ($currentPage < 1) {
    $currentPage = 1;
}

$itemsPerPage = 10;

$pathProductData = __DIR__ . '/../../storage/product.txt';

$dataProducts = file($pathProductData, FILE_IGNORE_NEW_LINES);

foreach ($dataProducts as $q) {
    $productData = explode('|', $q);
    $products[] = $productData;
}


$startIndex = ($currentPage - 1) * $itemsPerPage;

$totalPages = ceil(count($products) / $itemsPerPage);

$productsOnPage = array_slice($products, $startIndex, $itemsPerPage);
