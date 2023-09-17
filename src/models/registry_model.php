<?php

$msg = [];

extract($_POST);
extract($_FILES);

if (empty($user_name)) {
    $msg .= 'Заполните поле имя' . PHP_EOL;
} elseif (preg_match('#[^а-яa-z]#ui', $user_name)) {
    $msg .= 'Имя содержит недопустимые символы' . PHP_EOL;
} elseif (mb_strlen($user_name) > 15) {
    $msg .= 'Имя содержит больше 15 символов' . $user_name . PHP_EOL;
} elseif (mb_strlen($user_name) <= 3) {
    $msg .= 'Имя содержит менее 4 символов' . $user_name . PHP_EOL;
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

if (empty($phone_number)) {
    $msg .= 'Заполните поле номер' . PHP_EOL;
} elseif (!preg_match('/((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?/',
    $phone_number)) {
    $msg .= 'Некоректный номер' . $phone_number . PHP_EOL;
}

$maxFileSize = 1 * 1024 * 1024;
$allowedExtensions = ['jpeg', 'png', 'gif', 'webp', 'jpg'];
// Получаю расширение пришедшего из $_FILES файла.
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
    header('Location: /reg-main.my/views/registry.php');
    die;
} else {
    $phone_number = str_replace(['+', '8'], '', $phone_number);
    if (strlen($phone_number) === 10 && substr($phone_number, 0, 1) !== '7') {
        $phone_number = '7' . $phone_number;
    }
    $pathDirectoryStorage = __DIR__ . '\..\..\..\storage_files';
    $pathDirectoryUpload = __DIR__ . '\..\..\..\uploads';
    $pathDirectoryUploadAvatar = __DIR__ . '\..\..\..\uploads\avatars\\';

    $itemsDirectory = [$pathDirectoryStorage, $pathDirectoryUploadAvatar, $pathDirectoryUpload];
    foreach ($itemsDirectory as $item) {
        if (!is_dir($item)) {
            mkdir($item, 0777, true);
        }
    }

    $usersDataFilePath = __DIR__ . '\..\..\..\storage_files\user.txt';
    $usersAvatarDataFilePath = __DIR__ . '\..\..\..\storage_files\user_way.txt';

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
        header('Location: ../../views/registry.php');
        die;
    }

    $handlerDataUser = fopen($usersDataFilePath, 'a + b');

    if (!flock($handlerDataUser, LOCK_EX)) {
        $_SESSION['msg'] = 'Не удалось зарегистрироваться, повторите попытку позже!';
        die;
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $userData = "{$userId}|{$user_name}|{$email}|{$password}|{$phone_number}";
        fwrite($handlerDataUser, $userData . PHP_EOL);

        flock($handlerDataUser, LOCK_UN);
    }

    $handlerAvatar = fopen($usersAvatarDataFilePath, 'a + b');

    if (!flock($handlerAvatar, LOCK_EX)) {
        $_SESSION['msg'] = 'Не удалось зарегистрироваться, повторите попытку позже!';
        die;
    } else {
        $avatar = "{$userId}|{$filePath}";

        fwrite($handlerAvatar, $avatar . PHP_EOL);
        flock($handlerAvatar, LOCK_UN);
    }

    fclose($handlerDataUser);
    fclose($handlerAvatar);

    if (!empty($msg)) {
        $_SESSION['error'] = $msg;
        header('Location: ../../../views/register.php');
        die;
    } else {
        $_SESSION['msg'] = 'Регистрация успешно завершена!';
        header('Location: ../../../views/registry.php');
        die;
    }


}

