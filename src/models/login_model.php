<?php
declare(strict_types=1);
error_reporting(-1);
session_start();

// Функция подключения к БД
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

// Инициализация приходящих данных с $_POST
function initInputData($data): array
{
    foreach ($data as $key => $value) {
        $user[$key] = htmlspecialchars(strip_tags(trim($value)));
    }
    return $user;
}

// Валидация почты
function validateEmail($email)
{
    $msg = '';

    if (empty($email)) {
        $msg .= 'Заполните поле почты' . PHP_EOL;
    } elseif (!preg_match("/[0-9a-z]+@[a-z]/", $email)) {
        $msg .= 'Почта содержит недопустимые данные' . PHP_EOL;
    }

    return $msg;
}

// Валидация пароля
function validatePassword($password)
{
    $msg = '';

    if (empty($password)) {
        $msg .= 'Заполните поле пароль' . PHP_EOL;
    } elseif (!preg_match('/^(?![0-9]+$).+/', $password)) {
        $msg .= 'Пароль не должен содержать только цифры' . PHP_EOL;
    } elseif (!preg_match('/^[^!№;]+$/u', $password)) {
        $msg .= 'Пароль содержит недопустимые символы' . PHP_EOL;
    } elseif (!preg_match('/^(?![A-Za-z]+$).+/', $password)) {
        $msg .= 'Пароль не должен состоять только из букв' . PHP_EOL;
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $msg .= 'Пароль должен содержать минимум одну заглавную букву' . PHP_EOL;
    } elseif (strlen($password) <= 5) {
        $msg .= 'Пароль содержит меньше 5 символов ' . PHP_EOL;
    } elseif (strlen($password) > 15) {
        $msg .= 'Пароль больше 15 символов ' . PHP_EOL;
    }

    return $msg;
}

// Функция для получения всех данных пользователя через почту.
function getUsersData($email)
{
    $sql = "SELECT * FROM users WHERE email = :email";
    $sth = connectionDB()->prepare($sql);
    $sth->execute(['email' => $email]);
    return $sth->fetch(\PDO::FETCH_ASSOC);
}

// Получение пути до аватарки пользователя
function getAvatar($email)
{
    $sql = "SELECT avatar FROM users WHERE email = :email";
    $sth = connectionDB()->prepare($sql);
    $sth->bindParam(":email", $email, PDO::PARAM_STR);
    $sth->execute();

    return $sth->fetch(PDO::FETCH_ASSOC);
}

// Получение данных пользователя
function loginUser($email)
{
    $sql = "SELECT * FROM users WHERE email = :email";
    $sth = connectionDB()->prepare($sql);
    $sth->bindParam(":email", $email, PDO::PARAM_STR);
    $sth->execute();

    return $sth->fetch(PDO::FETCH_ASSOC);
}

// Функция для записи данных пользователя в сессию.
function writeInSession($data)
{
    $_SESSION['user'] = [
        'id' => $data['id'],
        'name' => $data['name'],
        'email' => $data['email'],
        'phone_number' => $data['phone_number'],
        'avatar' => $data['avatar']
    ];
}

if (!empty($_POST)) {

    $userFromForm = initInputData($_POST);

    $msg = validateEmail($userFromForm['email']);
    $msg .= validatePassword($userFromForm['password']);

    if (!empty($msg)) {
        $_SESSION['msg'] = $msg;
        header('Location: /auth/login');
        die;
    } else {
        $user = getUsersData($_POST['email']);
        if (!password_verify($userFromForm['password'], $user['password'])) {
            echo 'что-то пошлло не атк';
            die;
        }
        $userData = loginUser($_POST['email']);

        writeInSession($userData);
        header('Location: /');
    }

}

