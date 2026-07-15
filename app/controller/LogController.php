<?php
require_once __DIR__ . '/../core/Controller.php';

class LogController extends Controller
{
    private ActivityLog $log;

    public function __construct()
    {
        Auth::requireAdmin();
        $this->log = $this->model('ActivityLog');
    }

    public function index(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $logs = $this->log->getAll($perPage, $offset);
        $total = $this->log->getCount();

        Response::json([
            'logs' => $logs,
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ]);
    }

    public function byTask(): void
    {
        $taskId = (int)($_GET['task_id'] ?? 0);
        if (!$taskId) {
            Response::json(['error' => 'Task ID required'], 400);
            return;
        }
        $logs = $this->log->getByEntity('task', $taskId);
        Response::json($logs);
    }

    public function byUser(): void
    {
        $userId = (int)($_GET['user_id'] ?? 0);
        if (!$userId) {
            Response::json(['error' => 'User ID required'], 400);
            return;
        }
        $logs = $this->log->getByUser($userId);
        Response::json($logs);
    }
}