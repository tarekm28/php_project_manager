<?php
require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../core/Model.php';

use OpenApi\Attributes as OA;
#[OA\Schema(
    schema: "User",
    type: "object",
    properties: [
        new OA\Property(property: "id", type: "integer", description: "Unique identifier for the user"),
        new OA\Property(property: "username", type: "string", description: "Username of the user"),
        new OA\Property(property: "role", type: "string", description: "Role of the user (Admin, Manager, Employee)"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", description: "Timestamp when the user was created")
    ]
)]

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