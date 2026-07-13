<?php

class AuthMiddleware
{
    public static function handle(): void
    {
        if (!Auth::check()) {
            Response::redirect('index.php?route=/login');
        }
    }
}
