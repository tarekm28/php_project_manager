<?php

class Controller
{

    protected function model(string $model)
    {
        $modelFile = __DIR__ . '/../model/' . strtolower($model) . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
        }

        if (!class_exists($model)) {
            throw new \Exception("Model not found: {$model}");
        }
        return new $model();
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