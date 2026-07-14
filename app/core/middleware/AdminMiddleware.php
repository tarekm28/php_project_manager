<?php

class AdminMiddleware
{
    public static function handle(): void
    {
        if (!Auth::isAdmin()) {
            Response::json(['error' => 'Forbidden'], 403);
        }
    }
}