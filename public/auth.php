<?php
session_start();

function db_connect() {
    return new mysqli("localhost", "root", "", "new_proj");
}

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

function require_admin() {
    require_login();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo '<h1>No access</h1>';
        echo '<p>You are logged in but not an admin.</p>';
        exit;
    }
}

function login_user(array $user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
}