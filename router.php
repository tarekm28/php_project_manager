<?php
require_once __DIR__ . '/core/Session.php';
require_once __DIR__ . '/core/Request.php';
require_once __DIR__ . '/core/Response.php';
require_once __DIR__ . '/core/auth.php';
require_once __DIR__ . '/controller/employee_controller.php';

function routeEmployeeRequest(): array
{
    Auth::requireLogin();

    $controller = new EmployeeController();
    $page = Request::get('page', 'project_overview');
    $allowedPages = ['project_overview', 'team_tasks', 'current_tasks'];

    if (!in_array($page, $allowedPages, true)) {
        $page = 'project_overview';
    }

    if (Request::method() === 'POST') {
        if (isset($_POST['take_task'], $_POST['task_id'])) {
            $controller->takeTask((int) $_POST['task_id']);
            Response::redirect('view/employee/employee_interface.php?page=team_tasks');
        }

        if (isset($_POST['complete_task'], $_POST['task_id'])) {
            $controller->completeTask((int) $_POST['task_id']);
            Response::redirect('view/employee/employee_interface.php?page=current_tasks');
        }
    }

    return $controller->getPageData($page);
}

function routeAdminRequest(): array
{
    Auth::requireAdmin();
    $controller = new adminController();

    $page = Request::get('page', 'project_overview');
    $allowedPages = ['project_overview', 'user_management', 'task_management'];

    if (!in_array($page, $allowedPages, true)) {
        $page = 'project_overview';
    }

    return ['page' => $page];
}