<?php
include __DIR__ . '/../../security/monitoring/session.php';
session_start();

logSessionEvent('User logged out: ' . $_SESSION['user']['name']);

session_unset();
session_destroy();

session_write_close();

header('Location: /');
die;