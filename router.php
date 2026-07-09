<?php
require_once __DIR__ . '/core/Session.php';
require_once __DIR__ . '/core/Request.php';
require_once __DIR__ . '/core/Response.php';
require_once __DIR__ . '/core/auth.php';
require_once __DIR__ . '/controller/employee_controller.php';

function routeEmployeeRequest(): array
{
    Auth::requireLogin();

    $pagecontroller = new EmployeeController();
    $controller = new TaskController();
    $page = Request::get('page', 'project_overview');
    $allowedPages = ['project_overview', 'team_tasks', 'current_tasks'];

    if (!in_array($page, $allowedPages, true)) {
        $page = 'project_overview';
    }

    if (Request::method() === 'POST') {
        if (isset($_POST['take_task'], $_POST['task_id'])) {
            $controller->take((int) $_POST['task_id'], $_SESSION['user_id']);
            Response::redirect('view/employee/employee_interface.php?page=team_tasks');
        }

        if (isset($_POST['complete_task'], $_POST['task_id'])) {
            $controller->complete((int) $_POST['task_id'], $_SESSION['user_id']);
            Response::redirect('view/employee/employee_interface.php?page=current_tasks');
        }
    }

    return $pagecontroller->getPageData($page);
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

    if (Request::method() === 'POST') {
        if (isset($_POST['create_task'], $_POST['task'], $_POST['role'])) {
            $controller->createTask($_POST['task'], $_POST['role']);
            Response::redirect('view/admin/admin_interface.php?page=task_management');
        }

        if (isset($_POST['delete_task'], $_POST['task_id'])) {
            $controller->deleteTask((int) $_POST['task_id']);
            Response::redirect('view/admin/admin_interface.php?page=task_management');
        }

        if (isset($_POST['create_user'], $_POST['username'], $_POST['password'], $_POST['role'])) {
            $controller->createUser($_POST['username'], $_POST['password'], $_POST['role']);
            Response::redirect('view/admin/admin_interface.php?page=user_management');
        }

        if (isset($_POST['delete_user'], $_POST['user_id'])) {
            $controller->deleteUser((int) $_POST['user_id']);
            Response::redirect('view/admin/admin_interface.php?page=user_management');
        }
    }

    return ['page' => $page];
}