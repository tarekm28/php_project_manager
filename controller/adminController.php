<?php
require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../model/Task.php';
require_once __DIR__ . '/../model/User.php';

class adminController extends Controller
{
    private Task $task;
    private User $user;

    public function __construct()
    {
        Auth::requireAdmin();
        $this->task = $this->model('Task');
        $this->user = $this->model('User');
    }

    public function index(): void
    {
        $page = $_GET['page'] ?? 'project_overview';
        $allowedPages = ['project_overview', 'task_management', 'user_management'];

        if (!in_array($page, $allowedPages, true)) {
            $page = 'project_overview';
        }

        $data = ['page' => $page];

        if ($page === 'project_overview' || $page === 'task_management') {
            $data['tasks'] = $this->task->getAll();
        }

        if ($page === 'user_management') {
            $data['users'] = $this->user->getAll();
        }

        $this->view('admin/admin_interface', $data);
    }
}
