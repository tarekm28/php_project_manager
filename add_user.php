<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $conn = new mysqli("localhost", "root", "", "new_proj");

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();

    $stmt->close();
    $conn->close();


    header("Location: admin/admin_interface.php?page=user_management");
    exit();
}