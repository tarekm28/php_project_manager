<?php
// app/controller/TaskController.php

class TaskController extends Controller
{
    private Task $task;

    public function __construct()
    {
        $this->task = $this->model('Task');
    }

    public function index(): void
    {
        // AdminMiddleware already verified — just do the work
        $tasks = $this->task->getAll();
        Response::json($tasks);
    }

    public function create(): void
    {
        $data = $this->getInput();
        $task = $data['task'] ?? '';
        $role = $data['role'] ?? '';

        if (!$task || !$role) {
            Response::json(['error' => 'Task and role required'], 400);
            return;
        }

        $this->task->create($task, $role);
        Response::json(['success' => true, 'message' => 'Task created'], 201);
    }

    public function delete(): void
    {
        $data = $this->getInput();
        $taskId = (int)($data['task_id'] ?? 0);

        if (!$taskId) {
            Response::json(['error' => 'Task ID required'], 400);
            return;
        }

        $this->task->delete($taskId);
        Response::json(['success' => true]);
    }

    public function take(): void
    {
        $data = $this->getInput();
        $taskId = (int)($data['task_id'] ?? 0);
        $username = Auth::user()['username'] ?? '';

        if (!$taskId) {
            Response::json(['error' => 'Task ID required'], 400);
            return;
        }

        $this->task->take($taskId, $username);
        Response::json(['success' => true]);
    }

    public function complete(): void
    {
        $data = $this->getInput();
        $taskId = (int)($data['task_id'] ?? 0);
        $username = Auth::user()['username'] ?? '';

        if (!$taskId) {
            Response::json(['error' => 'Task ID required'], 400);
            return;
        }

        $task = $this->task->findById($taskId);
        if (!$task || ($task['employee_responsible'] ?? '') !== $username) {
            Response::json(['error' => 'Not authorized'], 403);
            return;
        }

        $this->task->complete($taskId, $username);
        Response::json(['success' => true, 'task_id' => $taskId, 'status' => 'completed']);
    }

    public function myTasks(): void
    {
        $username = Auth::user()['username'] ?? '';
        $tasks = $this->task->getByEmployee($username);
        Response::json($tasks);
    }

    public function teamTasks(): void
    {
        $role = Auth::user()['role'] ?? '';
        $tasks = $this->task->getByRole($role);
        Response::json($tasks);
    }

    public function allTasks(): void
    {
        $tasks = $this->task->getAll();
        Response::json($tasks);
    }
}