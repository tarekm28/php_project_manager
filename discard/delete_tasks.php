<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $conn = new mysqli("localhost", "root", "", "new_proj");

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

     ;
    $conn->close();

    header("Location: admin/admin_interface.php?page=task_management");
    exit();
}
?>