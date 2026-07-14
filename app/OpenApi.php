<?php
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Task Manager API",
    version: "1.0.0",
    description: "REST API for task management with role-based access control"
)]
#[OA\Server(
    url: "http://localhost/proj1/public/index.php?route=",
    description: "Local development server",
    variables: []
)]
#[OA\SecurityScheme(
    securityScheme: "sessionAuth",
    type: "apiKey",
    in: "cookie",
    name: "PHPSESSID",
    description: "Session-based authentication via PHP session cookie"
)]
class OpenApi
{
}