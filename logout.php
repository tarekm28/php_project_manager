<?php
require_once 'auth.php';

$_SESSION = [];
session_unset();
session_destroy();

redirect_to_login();