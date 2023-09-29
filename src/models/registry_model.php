<?php
declare(strict_types=1);
error_reporting(-1);
//session_start();
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
// Инициализация приходящих данных с $_POST
function initInputData($data): array
{
    foreach ($data as $key => $value) {
        $user[$key] = htmlspecialchars(strip_tags(trim($value)));
    }

    return $user;
}
// Инициализация приходящих данных с $_FILES
function initInputDataAvatar($data): array
{
    foreach ($data as $key => $value) {
        $avatar[$key] = $value;
    }
    return $avatar;
}

// Валидация имени
function validateUserName($userName)
{
    $msg = '';

    if (empty($userName)) {
        $msg .= 'Заполните поле имя' . PHP_EOL;
    } elseif (preg_match('#[^а-яa-z]#ui', $userName)) {
        $msg .= 'Имя содержит недопустимые символы' . PHP_EOL;
    } elseif (mb_strlen($userName) > 15) {
        $msg .= 'Имя содержит больше 15 символов' . $userName . PHP_EOL;
    } elseif (mb_strlen($userName) <= 3) {
        $msg .= 'Имя содержит менее 4 символов' . $userName . PHP_EOL;
    }

    return $msg;
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

// Валидация номера телефона
function validatePhoneNumber($phoneNumber)
{
    $msg = '';

    if (empty($phoneNumber)) {
        $msg .= 'Заполните поле номер' . PHP_EOL;
    } elseif (!preg_match('/((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?/',
        $phoneNumber)) {
        $msg .= 'Некоректный номер' . $phoneNumber . PHP_EOL;
    }

    return $msg;
}

// Валидация аватара пользователя
function validateAvatarUser($avatar)
{
    $msg = '';

    $maxFileSize = 1 * 1024 * 1024;
    $allowedExtensions = ['jpeg', 'png', 'gif', 'webp', 'jpg'];

    $extension = pathinfo($avatar['avatar']['name'], PATHINFO_EXTENSION);

    if (empty($avatar['avatar']['name'])) {
        $msg .= 'Аватар обязателен.';
    } elseif (!in_array($extension, $allowedExtensions)) {
        $msg .= 'Недопустимый тип файла.';
    } elseif ($avatar['avatar']['size'] > $maxFileSize) {
        $msg .= 'Размер файла превышает допустимый.';
    }

    return $msg;
}

// Функция проверки и изменения номера телефона пользователя
function checkPhoneNumber(string &$phone): array|string
{
    $phone = str_replace(['+', '8'], '', $phone);
    if (strlen($phone) === 10 && substr($phone, 0, 1) !== '7') {
        $phone = '7' . $phone;
    }

    return $phone;
}

// Функция проверки наличия почты в базе данных
function checkUserEmail(string $email): bool
{
    $query = 'select id from users where email=:email limit 1';
    $sth = connectionDB()->prepare($query);
    $sth->execute([':email' => $email]);
    $result = $sth->rowCount();

    return (bool)$result;
}

// Функция регистрации пользователя
function registerUser($name, $email, $password, $phone, $avatarPath): void
{
    $query = "INSERT INTO users (name, email, password, phone_number, avatar) VALUES (:name, :email, :password, :phone, :avatar)";

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $params = [
        ':name' => $name,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':phone' => $phone,
        ':avatar' => $avatarPath,
    ];

    try {
        $sth = connectionDB()->prepare($query);
        $sth->execute($params);
    } catch (\Exception $e) {
        die("Ошибка при выполнении запроса: " . $e->getMessage());
    }
}

// Функция загрузки аватарки
function uploadAvatar(array $avatar): string|bool
{
    $pathDirectoryUploadAvatar = __DIR__ . '\..\..\uploads\avatars\\';

    $filePath = $pathDirectoryUploadAvatar . uniqid() . $avatar['avatar']['name'];

    if (!move_uploaded_file($avatar['avatar']['tmp_name'], $filePath)) {
        return $result = false;
    } else {
        return $filePath = '..\\' . strstr($filePath, 'src');
    }
}

if (!empty($_POST)) {
    $user = initInputData($_POST);
    $avatar = initInputDataAvatar($_FILES);

    $msg = validateUserName($user['user_name']);
    $msg .= validateEmail($user['email']);
    $msg .= validatePassword($user['password']);
    $msg .= validatePhoneNumber($user['phone_number']);
    $msg .= validateAvatarUser($avatar);

    $pathAvatar = uploadAvatar($avatar); // Вызываем один раз и сохраняем результат

    if (!empty($msg)) {
        $_SESSION['msg'] = $msg;
        header('Location: /auth/registry');
        die;
    }

    if (checkUserEmail($user['email'])) {
        $_SESSION['msg'] = 'Такой пользователь уже существует';
        header('Location: /auth/registry');
        die;
    } else {
        checkPhoneNumber($user['phone_number']);

        registerUser(
            $user['user_name'],
            $user['email'],
            $user['password'],
            $user['phone_number'],
            $pathAvatar
        );
    }
}


