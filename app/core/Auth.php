<?php
class Auth
{
    public static function login(array $user): void
    {
        Session::start();
        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::set('role', $user['role']);
    }

    public static function logout(): void
    {
        Session::start();
        Session::destroy();
    }

    public static function check(): bool
    {
        Session::start();
        return Session::get('user_id') !== null;
    }

    public static function user(): ?array
    {
        if (!self::check()) return null;
        return [
            'id' => Session::get('user_id'),
            'username' => Session::get('username'),
            'role' => Session::get('role')
        ];
    }

    public static function isAdmin(): bool
    {
        return self::check() && Session::get('role') === 'admin';
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            Response::json(['error' => 'Unauthorized'], 401);
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            Response::json(['error' => 'Forbidden'], 403);
        }
    }
}