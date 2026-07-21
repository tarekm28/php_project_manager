<?php

ini_set('html_errors', 0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', 'localhost');

require_once __DIR__ . '/../app/Core/Session.php';
require_once __DIR__ . '/../app/Core/Auth.php';
require_once __DIR__ . '/../app/core/database.php';
require_once __DIR__ . '/../app/Core/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Core/middleware/AdminMiddleware.php';
require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/Controller.php';
require_once __DIR__ . '/../app/Core/Model.php';
require_once __DIR__ . '/../app/Core/Response.php';

Session::start();

$router = new Router();


$router->get('/me', 'AuthController@me', [AuthMiddleware::class]);
$router->post('/login', 'AuthController@authenticate');
$router->post('/logout', 'AuthController@logout', [AuthMiddleware::class]);


$router->get('/tasks', 'TaskController@index', [AuthMiddleware::class]);
$router->post('/tasks', 'TaskController@create', [AuthMiddleware::class, AdminMiddleware::class]);
$router->delete('/tasks', 'TaskController@delete', [AuthMiddleware::class, AdminMiddleware::class]);
$router->patch('/tasks', 'TaskController@edit', [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/users', 'UserController@index', [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/users', 'UserController@create', [AuthMiddleware::class, AdminMiddleware::class]);
$router->delete('/users', 'UserController@delete', [AuthMiddleware::class, AdminMiddleware::class]);
$router->patch('/users', 'UserController@edit', [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/users/tasks', 'UserController@getUserTasks', [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/users/reassign-tasks', 'UserController@reassignTasks', [AuthMiddleware::class, AdminMiddleware::class]);

$router->get('/tasks/mine', 'TaskController@myTasks', [AuthMiddleware::class]);
$router->get('/tasks/team', 'TaskController@teamTasks', [AuthMiddleware::class]);
$router->post('/tasks/take', 'TaskController@take', [AuthMiddleware::class]);
$router->post('/tasks/complete', 'TaskController@complete', [AuthMiddleware::class]);

$router->get('/logs', 'LogController@index', [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/logs/task', 'LogController@byTask', [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/logs/user', 'LogController@byUser', [AuthMiddleware::class, AdminMiddleware::class]);

$router->dispatch();