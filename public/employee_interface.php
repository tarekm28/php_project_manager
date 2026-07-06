<?php
require_once 'auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['take_task'], $_POST['task_id'])) {
    $task_id = (int) $_POST['task_id']; 
    $employee_name = $_SESSION['username'];

    $conn = db_connect();
    $stmt = $conn->prepare("
        UPDATE tasks
        SET employee_responsible = ?
        WHERE id = ?
          AND (employee_responsible IS NULL OR employee_responsible = '' OR employee_responsible = 'Unassigned')
    ");
    $stmt->bind_param("si", $employee_name, $task_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: employee_interface.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Manager Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Employee Dashboard</h1>
    </header>
    <main>  
        <section id="project-overview">
            <h2>Team Overview</h2>
            <div id="task-list">
                        <?php
                        $conn = new mysqli("localhost", "root", "", "new_proj");
                        $result = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");

                        while ($row = $result->fetch_assoc()) {
                            $assigned = $row['employee_responsible'] ?? '';
                            if ($assigned === '') {
                                $assigned = 'Unassigned';
                            }

                            echo "<li>";
                            echo htmlspecialchars($row['task']) . " - " . htmlspecialchars($assigned);

                            if ($assigned === 'Unassigned') {
                                echo "<form method='POST' style='display:inline;margin-left:10px;'>
                                        <input type='hidden' name='task_id' value='" . (int)$row['id'] . "'>
                                        <button type='submit' name='take_task'>Take Task</button>
                                      </form>";
                            }

                            echo "</li>";
                        }

                        $conn->close();
                        ?>
                </div> 
        <?php
        function take_responsibility($task_id, $employee_name) {
            $conn = new mysqli("localhost", "root", "", "new_proj");
            $stmt = $conn->prepare("UPDATE tasks SET employee_responsible = ? WHERE id = ?");
            $stmt->bind_param("si", $employee_name, $task_id);
            $stmt->execute();
            $stmt->close();
            $conn->close();
        }
        ?>
        </section>
        <section id="task-management">
            <h2>Task Undertaking</h2>
                
            </div>
        </section>
    </main>
</body>
</html>