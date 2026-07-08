<?php 
require_once __DIR__ . '/../Models/Task.php';

class TaskController {
    public function index() {
        $tasks = Task::getAll();
        require __DIR__ . '/../Views/admin/task_management.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task = $_POST['task'];
            $role = $_POST['role'];
            Task::create($task, $role);
        }
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

    public function take() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['take_task'])) {
            $taskId = (int)$_POST['task_id'];
            $username = $_SESSION['username'];
            Task::take($taskId, $username);
        }
        header('Location: admin_interface.php?page=project_overview');
        exit();
    }

    public function complete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_task'])) {
            $taskId = (int)$_POST['task_id'];
            $username = $_SESSION['username'];
            Task::complete($taskId, $username);
        }
        header('Location: admin_interface.php?page=project_overview');
        exit();
    }