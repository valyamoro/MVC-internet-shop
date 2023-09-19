<?php
declare(strict_types=1);
error_reporting(-1);
session_start();

$msg = '';

foreach ($_POST as $key => $value) {
    $user[$key] = htmlspecialchars(strip_tags(trim($value)));
}

$email = $user['email'];
$userName = $user['user_name'];
$phone = $user['phone_number'];
$password = $user['password'];

extract($_FILES);

if (empty($userName)) {
    $msg .= 'Заполните поле имя' . PHP_EOL;
} elseif (preg_match('#[^а-яa-z]#ui', $userName)) {
    $msg .= 'Имя содержит недопустимые символы' . PHP_EOL;
} elseif (mb_strlen($userName) > 15) {
    $msg .= 'Имя содержит больше 15 символов' . $userName . PHP_EOL;
} elseif (mb_strlen($userName) <= 3) {
    $msg .= 'Имя содержит менее 4 символов' . $userName . PHP_EOL;
}

if (empty($email)) {
    $msg .= 'Заполните поле почты' . PHP_EOL;
} elseif (!preg_match("/[0-9a-z]+@[a-z]/", $email)) {
    $msg .= 'Почта содержит недопустимые данные' . PHP_EOL;
}

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

if (empty($phone)) {
    $msg .= 'Заполните поле номер' . PHP_EOL;
} elseif (!preg_match('/((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?/',
    $phone)) {
    $msg .= 'Некоректный номер' . $phone . PHP_EOL;
}

$maxFileSize = 1 * 1024 * 1024;
$allowedExtensions = ['jpeg', 'png', 'gif', 'webp', 'jpg'];

$extension = pathinfo($avatar['name'], PATHINFO_EXTENSION);

if (empty($avatar['name'])) {
    $msg .= 'Аватар обязателен.';
} elseif (!in_array($extension, $allowedExtensions)) {
    $msg .= 'Недопустимый тип файла.';
} elseif ($avatar['size'] > $maxFileSize) {
    $msg .= 'Размер файла превышает допустимый.';
}

if (!empty($msg)) {
    $_SESSION['msg'] = $msg;
    header('Location: /auth/registry');
    die;
} else {
    $phone = str_replace(['+', '8'], '', $phone);
    if (strlen($phone) === 10 && substr($phone, 0, 1) !== '7') {
        $phone = '7' . $phone;
    }
    $pathDirectoryStorage = __DIR__ . '\..\..\storage';
    $pathDirectoryUpload = __DIR__ . '\..\..\uploads';
    $pathDirectoryUploadAvatar = __DIR__ . '\..\..\uploads\avatars\\';

    $itemsDirectory = [$pathDirectoryStorage, $pathDirectoryUploadAvatar, $pathDirectoryUpload];
    foreach ($itemsDirectory as $item) {
        if (!is_dir($item)) {
            mkdir($item, 0777, true);
        }
    }

    $usersDataFilePath = __DIR__ . '\..\..\storage\user.txt';
    $usersAvatarDataFilePath = __DIR__ . '\..\..\storage\user_way.txt';

    $itemsFile = [$usersDataFilePath, $usersAvatarDataFilePath];
    foreach ($itemsFile as $item) {
        fclose(fopen($item, 'a+b'));
    }

    $filePath = $pathDirectoryUploadAvatar . uniqid() . $avatar['name'];

    move_uploaded_file($avatar['tmp_name'], $filePath);

    $filePath = '..\\' . strstr($filePath, 'src');

    $dataUsers = file($usersDataFilePath, FILE_IGNORE_NEW_LINES);

    $userId = $dataUsers ? (intval(explode('|', end($dataUsers))[0]) + 1) : 1;

    $isUserExists = false;
    foreach ($dataUsers as $line) {
        $userData = explode('|', $line);
        if ($userData[2] === $email || $userData[4] === $phone_number) {
            $isUserExists = true;
            break;
        }
    }

    if ($isUserExists) {
        $_SESSION['msg'] = 'Пользователь с этими данными уже зарегистрирован!';
        header('Location: /auth/registry');
        die;
    }

    $handlerDataUser = fopen($usersDataFilePath, 'a + b');

    if (!flock($handlerDataUser, LOCK_EX)) {
        $_SESSION['msg'] = 'Не удалось зарегистрироваться, повторите попытку позже!';
        header('Location: /auth/registry');
        die;
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $userData = "{$userId}|{$userName}|{$email}|{$password}|{$phone}";
        fwrite($handlerDataUser, $userData . PHP_EOL);

        flock($handlerDataUser, LOCK_UN);
    }

    $handlerAvatar = fopen($usersAvatarDataFilePath, 'a + b');

    if (!flock($handlerAvatar, LOCK_EX)) {
        $_SESSION['msg'] = 'Не удалось зарегистрироваться, повторите попытку позже!';
        header('Location: /auth/registry');
        die;
    } else {
        $avatar = "{$userId}|{$filePath}";

        fwrite($handlerAvatar, $avatar . PHP_EOL);
        flock($handlerAvatar, LOCK_UN);
    }

    fclose($handlerDataUser);
    fclose($handlerAvatar);

    if (!empty($msg)) {
        $_SESSION['msg'] = $msg;
        header('Location: /auth/registry');
        die;
    } else {
        header('Location: /');
        die;
    }


}

