<?php
require_once __DIR__ . '/../app/core/Session.php';
require_once __DIR__ . '/../app/core/auth.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Model.php';

Session::start();

$router = new Router();

// Authentication Routes
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@authenticate');
$router->get('/logout', 'AuthController@logout');
$router->post('/logout', 'AuthController@logout');

// Dashboard Home Route
$router->get('/', 'DashboardController@index');

// Admin Action Routes
$router->post('/admin/tasks/create', 'TaskController@create');
$router->post('/admin/tasks/delete', 'TaskController@delete');
$router->post('/admin/users/create', 'UserController@create');
$router->post('/admin/users/delete', 'UserController@delete');

// Employee Action Routes
$router->post('/employee/tasks/take', 'TaskController@take');
$router->post('/employee/tasks/complete', 'TaskController@complete');

$router->dispatch();