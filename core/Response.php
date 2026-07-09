<?php

class Response
{
    public static function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    public static function json($data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
