<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $conn = new mysqli("localhost", "root", "", "new_proj");

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);    
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header("Location: admin/admin_interface.php?page=user_management");
    exit();
}