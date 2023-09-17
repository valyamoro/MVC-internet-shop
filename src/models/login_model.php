<?php

$msg = [];

extract($_POST);

if (empty($email)) {
    $msg .= 'Заполните поле почты' . PHP_EOL;
} elseif (!preg_match("/[0-9a-z]+@[a-z]/", $email)) {
    $msg .= 'Почта содержит недопустимые данные' . PHP_EOL;
}

if (empty($password)) {
    $msg .= 'Заполните поле пароль' . PHP_EOL;
}

if (!empty($msg)) {
    $_SESSION['msg'] = $msg;
    header('Location: ');
    die;
} else {
    $pathUsersData = __DIR__ . '\..\..\..\storage\user.txt';
    $pathUserWay = __DIR__ . '\..\..\..\storage\user_way.txt';

    $dataUsers = file($pathUsersData, FILE_IGNORE_NEW_LINES);

    $approvedUsers = array_filter($dataUsers, function($q) use ($email, $password) {
        $user = explode('|', $q);
        return $user[2] === $email && password_verify($password, $user[3]);
    });

    if (empty($approvedUsers)) {
        $_SESSION['msg'] = 'Неверные данные!';
        header('Location: login.php');
        die;
    } else {
        $currentUser = explode('|', reset($approvedUsers));
    }

    $avatarData = file($pathUsersWay, FILE_IGNORE_NEW_LINES);

    $currentId = $currentUser[0];

    $approvedAvatarUsers = array_filter($avatarData, function ($q) use ($currentId) {
        $user = explode('|', $q);
        return $user[0] === $currentId;
    });

    $currentUserAvatar = explode('|', reset($approvedAvatarUsers));

    $_SESSION['msg'] = 'Вы авторизировались!';
    $_SESSION['user'] = [
        'id' => $currentUser[0],
        'name' => $currentUser[1],
        'email' => $currentUser[2],
        'phone' => $currentUser[4],
        'avatar' => $currentUserAvatar[1],
    ];

    header('Location: ');
    die;
}