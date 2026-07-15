<?php
require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../core/Model.php';

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Task",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", description: "Unique identifier for the task"),
        new OA\Property(property: "task", type: "string", description: "Description of the task"),
        new OA\Property(property: "assigned_to", type: "string", description: "Team assigned to the task"),
        new OA\Property(property: "employee_responsible", type: "string", description: "Username of the employee responsible for the task"),
        new OA\Property(property: "status", type: "string", description: "Current status of the task (Pending, In Progress, Completed)"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", description: "Timestamp when the task was created"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", description: "Timestamp when the task was last updated")
    ]
)]


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

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
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

    public function update(int $id, array $data): bool
    {
        $allowed = ['task', 'assigned_to', 'employee_responsible', 'status'];
        $sets = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $sets[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($sets)) return false;
        
        $values[] = $id;
        $stmt = $this->db->prepare("UPDATE tasks SET " . implode(', ', $sets) . " WHERE id = ?");
        return $stmt->execute($values);
    }

    public function getLastID(): int
    {
        return (int)$this->db->lastInsertId();
    }
}
