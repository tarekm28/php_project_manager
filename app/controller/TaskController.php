<?php

require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/Response.php';

class TaskController extends Controller
{
    private Task $task;


    public function __construct()
    {
        Auth::requireLogin();

        $this->task = $this->model('Task');
    }


    public function index(): void
    {
        Auth::requireAdmin();
        $tasks = $this->task->getAll();

        $this->view('admin/task_management', [
            'tasks' => $tasks
        ]);
    }


    public function create(): void
    {
        Auth::requireAdmin();
        $this->task->create(
            $_POST['task'],
            $_POST['role']
        );

        Response::redirect('index.php?route=/&page=task_management');
    }


    public function delete(): void
    {
        Auth::requireAdmin();
        $this->task->delete(
            (int)$_POST['task_id']
        );

        Response::redirect('index.php?route=/&page=task_management');
    }


    public function take(): void
    {
        $taskId = (int)$_POST['task_id'];
        $username = Auth::user()['username'] ?? '';

        $this->task->take(
            $taskId,
            $username
        );

        Response::redirect('index.php?route=/&page=team_tasks');
    }


    public function complete(): void
    {
        $taskId = (int)$_POST['task_id'];
        $username = Auth::user()['username'] ?? '';

        $this->task->complete(
            $taskId,
            $username
        );

        Response::redirect('index.php?route=/&page=current_tasks');
    }
}