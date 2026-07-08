<?php 
require_once __DIR__ . '/../Models/Task.php';

class TaskController {
    public function index() {
        $tasks = Task::getAll();
        require __DIR__ . '/../Views/admin/task_management.php';
    }

    public function create($task, $role) {
        Task::create($task, $role);
        header('Location: admin_interface.php?page=task_management');
        exit();
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_task'])) {
            $taskId = (int)$_POST['task_id'];
            Task::delete($taskId);
        }
        header('Location: admin_interface.php?page=task_management');
        exit();
    }