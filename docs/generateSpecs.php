<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Manually load all app files (no autoloader/namespaces)
foreach (glob(__DIR__ . '/../app/**/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/../app/*.php') as $file) {
    require_once $file;
}

$openapi = (new \OpenApi\Generator())
    ->setVersion('3.1.0')
    ->generate([__DIR__ . '/../app']);

$specPath = __DIR__ . '/../public/api-docs/openapi.json';
file_put_contents($specPath, $openapi->toJson());

echo "Generated: $specPath (" . filesize($specPath) . " bytes)\n";