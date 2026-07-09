<?php
require_once __DIR__ . '/core/Session.php';
require_once __DIR__ . '/core/auth.php';
require_once __DIR__ . '/controller/adminController.php';
require_once __DIR__ . '/controller/employee_controller.php';

Session::start();
Auth::requireLogin();

if (Auth::isAdmin()) {
    $controller = new adminController();
    $controller->index();
    exit;
}

$controller = new EmployeeController();
$controller->index();
exit;