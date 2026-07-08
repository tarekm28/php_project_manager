<?php
session_start();

function db_connect() {
    return new mysqli("localhost", "root", "", "new_proj");
}

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function get_app_base_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script_path = $_SERVER['PHP_SELF'] ?? $_SERVER['SCRIPT_NAME'] ?? '/';
    $script_dir = dirname($script_path);

    if (basename($script_dir) === 'admin' || basename($script_dir) === 'employee') {
        $script_dir = dirname($script_dir);
    }

    return rtrim($protocol . '://' . $host . $script_dir, '/');
}

function redirect_to_login() {
    header('Location: ' . get_app_base_url() . '/login.php');
    exit;
}

function require_login() {
    if (!is_logged_in()) {
        redirect_to_login();
    }
}

function require_admin() {
    require_login();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo '<h1>No access</h1>';
        echo '<p>You are logged in but not an admin.</p>';
        echo '<p><a href="logout.php">Logout</a></p>';
        exit;
    }
}

function login_user(array $user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
}