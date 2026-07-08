<?php 

$db = Database::getConnection();

function getAll() {
    $stmt = $db->query("SELECT * FROM tasks ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function getByRole($role) {
    $stmt = $db->query("SELECT * FROM tasks WHERE assigned_to = ? ORDER BY created_at DESC", [$role]);
    return $stmt->fetchAll();
}

function getByEmployee($username) {
    $stmt = $db->query("SELECT * FROM tasks WHERE employee_responsible = ? AND status != 'Completed' ORDER BY created_at DESC", [$username]);
    return $stmt->fetchAll();
}


function takeTask($taskId, $username) {
    $stmt = $db->prepare("UPDATE tasks SET employee_responsible = ? WHERE id = ?");
    $stmt->execute([$username, $taskId]);
}

function completeTask($taskId, $username) {
    $stmt = $db->prepare("UPDATE tasks SET status = 'Completed' WHERE id = ? AND employee_responsible = ?");
    $stmt->execute([$taskId, $username]);
}


function createTask($task, $assignedTo) {
    $stmt = $db->prepare("INSERT INTO tasks (task, assigned_to, status, created_at, updated_at) VALUES (?, ?, 'Pending', NOW(), NOW())");
    $stmt->execute([$task, $assignedTo]);
}


function deleteTask($taskId) {
    $stmt = $db->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);
}   


?>