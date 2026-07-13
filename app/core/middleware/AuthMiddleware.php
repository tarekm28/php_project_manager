<?php
namespace App\Core;

class AuthMiddleware
{
    public static function handle(): void
    {
        if (!Auth::check()) {
            Response::json(['error' => 'Unauthorized'], 401);
        }
    }
}