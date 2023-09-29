<?php

// Функция для подключения к БД
function connectionDB(): ?\PDO
{
    $dbh = null;

    if (!\is_null($dbh)) {
        return $dbh;
    }

        $dbh = new \PDO(
            'mysql:host=localhost;dbname=mvc-int-shop;charset=utf8mb4',
            'root',
            '', [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'",
        ],
    );
    return $dbh;
}

// Функция для получения всех товаров с БД
function getProducts(): array
{
    $query = 'SELECT * FROM products ORDER BY id DESC';
    $sth = connectionDB()->prepare($query);
    $sth->execute();
    $result = $sth->fetchAll();

    return $result !== false ? $result : [];
}

// Функция для получения номера текущей страницы
function getCurrentPage()
{
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

    if ($currentPage < 1) {
        $currentPage = 1;
    }
    return $currentPage;
}

