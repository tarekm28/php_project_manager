<?php
namespace App\Core;

class Controller
{
    protected function model(string $model)
    {
        $modelClass = "App\\Models\\{$model}";
        if (!class_exists($modelClass)) {
            throw new \Exception("Model not found: {$model}");
        }
        return new $modelClass();
    }

    protected function getInput(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            return json_decode(file_get_contents('php://input'), true) ?? [];
        }
        return $_POST;
    }
}