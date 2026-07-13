<?php

class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data);

        $viewFile = __DIR__ . '/../view/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View not found: {$view}");
        }

        require $viewFile;
    }
}
