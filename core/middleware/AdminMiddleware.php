<?php

class AdminMiddleware
{
    public static function handle(): void
    {
        if (!Auth::isAdmin()) {
            http_response_code(403);
            exit('Forbidden');
        }
    }
}
