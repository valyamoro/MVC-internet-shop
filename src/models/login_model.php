<?php
declare(strict_types=1);
error_reporting(-1);
session_set_cookie_params(3600);
session_start();

$msg = false;

foreach ($_POST as $key => $value) {
    $user[$key] = htmlspecialchars(strip_tags(trim($value)));
}

$email = $user['email'];
$password = $user['password'];

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
    header('Location: ../../../views/login.php');
    die;
} else {
    $pathUsersData = __DIR__ . '/../../storage\user.txt';
    $pathUsersWay = __DIR__ . '/../../storage\user_way.txt';

    $dataUsers = file($pathUsersData, FILE_IGNORE_NEW_LINES);

    $approvedUsers = array_filter($dataUsers, function ($q) use ($email, $password) {
        $user = explode('|', $q);
        return $user[2] === $email && password_verify($password, $user[3]);
    });

    if (empty($approvedUsers)) {
        $_SESSION['msg'] = 'Почта или пароль введены неверно.';
        header('Location: /auth/login.php');
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
    include __DIR__ . '/../../security/monitoring/session.php';
    logSessionEvent('User login profile: ' . $_SESSION['user']['name']);

    session_regenerate_id();
    header('Location: /auth/profile');
    die;

}

