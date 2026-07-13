<?php

class Controller
{
    protected function model(string $model)
    {
    $modelFile = __DIR__ . '/../model/' . $model . '.php';

    if (!file_exists($modelFile)) {
        throw new Exception("Model not found: {$model}");
    }

    require_once $modelFile;

    return new $model();
    }

    protected function view($view, array $data = []): void
    {
        extract($data);

        $viewFile = __DIR__ . '/../view/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View not found: {$view}");
        }

        require $viewFile;
    }
}
