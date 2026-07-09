<?php
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../model/Task.php';
require_once __DIR__ . '/../model/User.php';

class EmployeeController extends Controller
{
    private Task $task;

    public function __construct()
    {
        Auth::requireLogin();
        $this->task = $this->model('Task');
    }

    public function index(): void
    {
        $page = $_GET['page'] ?? 'project_overview';
        $allowedPages = ['project_overview', 'team_tasks', 'current_tasks'];

        if (!in_array($page, $allowedPages, true)) {
            $page = 'project_overview';
        }

        $data = ['page' => $page];

        if ($page === 'project_overview') {
            $data['tasks'] = $this->task->getAll();
        }
        if ($page === 'team_tasks') {
            $data['tasks'] = $this->task->getByRole(Auth::user()['role'] ?? '');
        }
        if ($page === 'current_tasks') {
            $data['currentTasks'] = $this->task->getByEmployee(Auth::user()['username'] ?? '');
        }

        $this->view('employee/employee_interface', $data);
    }
}
