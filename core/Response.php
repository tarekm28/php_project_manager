<?php

class Response
{
    public static function redirect(string $url): void
    {
        if (!headers_sent()) {
            header('Location: ' . $url);
        }
        exit;
    }

    public static function json($data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
