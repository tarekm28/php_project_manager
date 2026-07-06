<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task = $_POST['task'];

    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "new_proj");

    // Insert the task into the database
    $stmt = $conn->prepare("INSERT INTO tasks (task) VALUES (?)");
    $stmt->bind_param("s", $task);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    // Redirect back to the main page
    header("Location: index.php");
    exit();
}
?>