<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task = $_POST['task'];
    $role = $_POST['role'];

    $conn = new mysqli("localhost", "root", "", "new_proj");

    $stmt = $conn->prepare("INSERT INTO tasks (task, assigned_to) VALUES (?, ?)");
    $stmt->bind_param("ss", $task, $role);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: admin/admin_interface.php");
    exit();
}
?>