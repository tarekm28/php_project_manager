<?php
require_once __DIR__ . '/core/auth.php';
require_once __DIR__ . '/core/Session.php';

Session::start();
Auth::logout();

header('Location: login.php');
exit;