<?php

require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/database.php';

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
            $redirectUrl = ($scriptDir ?: '') . '/index.php?route=/';
            Response::redirect($redirectUrl);
            return;
        }
        $this->view('auth/login');
    }

    public function authenticate(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password']) {
            Auth::login($user);
            $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
            $redirectUrl = ($scriptDir ?: '') . '/index.php?route=/';
            Response::redirect($redirectUrl);
            return;
        }

        $this->view('auth/login', ['error' => 'Invalid username or password']);
    }

    public function logout(): void
    {
        Auth::logout();
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
        $redirectUrl = ($scriptDir ?: '') . '/index.php?route=/login';
        Response::redirect($redirectUrl);
    }
}