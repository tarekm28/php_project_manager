<?php
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/Controller.php';

require_once __DIR__ . '/adminController.php';
require_once __DIR__ . '/employee_controller.php';

class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireLogin();

        if (Auth::isAdmin()) {
            $controller = new adminController();
            $controller->index();
        } else {
            $controller = new EmployeeController();
            $controller->index();
        }
    }
}