<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "new_proj");

    // Delete the task from the database
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    // Redirect back to the main page
    header("Location: index.php");
    exit();
}
?>