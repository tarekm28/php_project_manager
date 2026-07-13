<?php
// public/index.php

require_once __DIR__ . '/../app/core/Session.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/core/AuthMiddleware.php';
require_once __DIR__ . '/../app/core/AdminMiddleware.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/core/Controller.php';
require_once __DIR__ . '/../app/core/Model.php';
require_once __DIR__ . '/../app/core/Response.php';

Session::start();

$router = new Router();

// Public routes (no middleware)
$router->post('/login', 'AuthController@authenticate');

// Authenticated routes
$router->get('/me', 'AuthController@me', [AuthMiddleware::class]);
$router->post('/logout', 'AuthController@logout', [AuthMiddleware::class]);

// Admin-only routes
$router->get('/tasks', 'TaskController@index', [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/tasks', 'TaskController@create', [AuthMiddleware::class, AdminMiddleware::class]);
$router->delete('/tasks', 'TaskController@delete', [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('/users', 'UserController@index', [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('/users', 'UserController@create', [AuthMiddleware::class, AdminMiddleware::class]);
$router->delete('/users', 'UserController@delete', [AuthMiddleware::class, AdminMiddleware::class]);

// Employee routes (any authenticated user)
$router->get('/tasks/mine', 'TaskController@myTasks', [AuthMiddleware::class]);
$router->get('/tasks/team', 'TaskController@teamTasks', [AuthMiddleware::class]);
$router->get('/tasks/all', 'TaskController@allTasks', [AuthMiddleware::class]);
$router->post('/tasks/take', 'TaskController@take', [AuthMiddleware::class]);
$router->post('/tasks/complete', 'TaskController@complete', [AuthMiddleware::class]);

$router->dispatch();