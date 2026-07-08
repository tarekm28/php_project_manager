<?php
$db = Database::getConnection();


function getAll() {
    $stmt = $db->query("SELECT * FROM tasks ORDER BY created_at DESC");
    return $stmt->fetchAll();
}


function createUser($username, $password, $role) {
    $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $role]);
    $stmt->close();
}


function deleteUser($userId) {
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $stmt->close();
}


?>