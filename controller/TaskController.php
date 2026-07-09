<?php

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
        $tasks = $this->task->getAll();

        $this->view('admin/task_management', [
            'tasks' => $tasks
        ]);
    }


    public function create(): void
    {
        $this->task->create(
            $_POST['task'],
            (int)$_POST['assigned_to']
        );

        header('Location: /tasks');
        exit;
    }


    public function delete(): void
    {
        $this->task->delete(
            (int)$_POST['task_id']
        );

        header('Location: /tasks');
        exit;
    }


    public function take(int $taskId, int $userId): void
    {
        $this->task->take(
            $taskId,
            $userId
        );

        header('Location: /tasks');
        exit;
    }


    public function complete(int $taskId, int $userId): void
    {
        $this->task->complete(
            $taskId,
            $userId
        );

        header('Location: /tasks');
        exit;
    }
}