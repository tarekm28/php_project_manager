<?php
require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../core/Model.php';

class Task extends Model
{

    public function getAll(): array
    {
         
        $stmt =  $this->db->query("SELECT * FROM tasks ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getByRole(string $role): array
    {
         
        $stmt =  $this->db->prepare("SELECT * FROM tasks WHERE assigned_to = ? ORDER BY created_at DESC");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    public function getByEmployee(string $username): array
    {
         
        $stmt =  $this->db->prepare("SELECT * FROM tasks WHERE employee_responsible = ? AND status != 'Completed' ORDER BY created_at DESC");
        $stmt->execute([$username]);
        return $stmt->fetchAll();
    }

    public function take(int $taskId, string $username): void
    {
         
        $stmt = $this->db->prepare("UPDATE tasks SET employee_responsible = ?, status = 'In Progress' WHERE id = ? AND (employee_responsible IS NULL OR employee_responsible = '' OR employee_responsible = 'Unassigned')");
        $stmt->execute([$username, $taskId]);
    }

    public function complete(int $taskId, string $username): void
    {
         
        $stmt = $this->db->prepare("UPDATE tasks SET status = 'Completed' WHERE id = ? AND employee_responsible = ?");
        $stmt->execute([$taskId, $username]);
    }

    public function create(string $task, string $assignedTo): void
    {
         
        $stmt = $this->db->prepare("INSERT INTO tasks (task, assigned_to, status, created_at, updated_at) VALUES (?, ?, 'Pending', NOW(), NOW())");
        $stmt->execute([$task, $assignedTo]);
    }

    public function delete(int $taskId): void
    {
         
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$taskId]);
    }
}
