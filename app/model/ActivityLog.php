<?php
require_once __DIR__ . '/../core/Model.php';

class ActivityLog extends Model
{
    public function log(
        int $userId,
        string $username,
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO activity_logs 
                (user_id, username, action, entity_type, entity_id, old_values, new_values, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $userId,
                $username,
                $action,
                $entityType,
                $entityId,
                $oldValues ? json_encode($oldValues) : null,
                $newValues ? json_encode($newValues) : null
            ]);
        } catch (\PDOException $e) {
        }
    }

    public function getAll(int $limit = 100, int $offset = 0): array
    {
        try {
            $limit = max(0, (int) $limit);
            $offset = max(0, (int) $offset);
            $stmt = $this->db->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}");
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getByEntity(string $entityType, int $entityId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM activity_logs 
                WHERE entity_type = ? AND entity_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$entityType, $entityId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getByUser(int $userId, int $limit = 50): array
    {
        try {
            $limit = max(0, (int) $limit);
            $stmt = $this->db->prepare("SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT {$limit}");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getCount(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM activity_logs");
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            return 0;
        }
    }
}   