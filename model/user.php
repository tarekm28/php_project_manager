<?php
require_once __DIR__ . '/../core/database.php';

class User extends Model
{

    public function getAll() : array{
        $stmt = $this->db->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }


    public function createUser(string $username, string $password, string $role): void {
        $stmt = $this->db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $role]);
    }


    public function deleteUser(int $userId): void {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
    }


}