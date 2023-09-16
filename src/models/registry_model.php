<?php

if (!empty($_POST['registry'])) {
	$user = [];
	foreach ($_POST as $key => $value) {
		$user[$key] = \htmlspecialchars(\strip_tags(\trim($value)));
	}

	$msg = '';

	// Валидация пришедших данных из $_POST и $_FILES.
	if (empty($username)) {
		$msg .= 'Заполните поле имя' . PHP_EOL;
	} elseif (preg_match('#[^а-яa-z]#ui', $username)) {
		$msg .= 'Имя содержит недопустимые символы' . PHP_EOL;
	} elseif (mb_strlen($username) > 15) {
		$msg .= 'Имя содержит больше 15 символов' . $username . PHP_EOL;
	} elseif (mb_strlen($username) <= 3) {
		$msg .= 'Имя содержит менее 4 символов' . $username . PHP_EOL;
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

}





