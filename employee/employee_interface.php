<?php
require_once '../auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['take_task'], $_POST['task_id'])) {
    $task_id = (int) $_POST['task_id'];
    $employee_name = $_SESSION['username'];
    take_responsibility($task_id, $employee_name);
    header("Location: employee/employee_interface.php");
    exit;
}

function take_responsibility($task_id, $employee_name) {
    $conn = db_connect();
    $stmt = $conn->prepare("
        UPDATE tasks
        SET employee_responsible = ?, status = 'In Progress'
        WHERE id = ?
          AND (employee_responsible IS NULL OR employee_responsible = '' OR employee_responsible = 'Unassigned')
    ");
    $stmt->bind_param("si", $employee_name, $task_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_task'], $_POST['task_id'])) {
    $task_id = (int) $_POST['task_id'];
    task_completed($task_id);
    header("Location: employee/employee_interface.php");
    exit;
}

function task_completed($task_id) {
    $conn = db_connect();
    $stmt = $conn->prepare("
        UPDATE tasks
        SET status = 'Completed'
        WHERE id = ?
          AND employee_responsible = ?
    ");
    $employee_name = $_SESSION['username'];
    $stmt->bind_param("is", $task_id, $employee_name);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <header>
        <h1>Employee Dashboard</h1>
        <div style="position:absolute; top:16px; right:16px;">
        <form method="POST" action="../logout.php">
            <button type="submit">Logout</button>
        </form>
    </div>
    </header>
    <main>
        <section id="project-overview">
            <h2>Project Overview</h2>
            <div id="project-list">
                <?php
                        $conn = new mysqli("localhost", "root", "", "new_proj");
                        $result = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");

                        while ($row = $result->fetch_assoc()) {
                            if (isset($row['employee_responsible']) && !empty($row['employee_responsible'])) {
                                $employee_responsible = $row['employee_responsible'];
                            } else {
                                $employee_responsible = 'Unassigned';
                            }
                            echo "<li>" . $row['task'] . " - " . $employee_responsible . "-" . $row['assigned_to'] . "-" . $row['status'] . "</li>";
                        }

                        $conn->close();
                ?>
            </div>
        </section>  
        <section id="team-overview">
            <h2>Team Overview</h2>
            <div id="task-list">
                        <?php
                        $conn = new mysqli("localhost", "root", "", "new_proj");
                        $role = $_SESSION['role'];
                        $result = $conn->query("SELECT * FROM tasks WHERE assigned_to = '$role' ORDER BY created_at DESC");

                        while ($row = $result->fetch_assoc()) {
                            $assigned = $row['employee_responsible'] ?? '';
                            if ($assigned === '') {
                                $assigned = 'Unassigned';
                            }

                            echo "<li>";
                            echo $row['task'] . " - " .$assigned;

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
        </section>
        <section id="task-management">
            <h2>Task Undertaking</h2>
            <?php
            $conn = new mysqli("localhost", "root", "", "new_proj");
            $user = $_SESSION['username'];
            $result = $conn->query("SELECT * FROM tasks WHERE employee_responsible = '$user' AND status != 'Completed' ORDER BY created_at DESC");
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li>" . $row['task'] .
                        "<form method='POST' style='display:inline;margin-left:10px;'>
                            <input type='hidden' name='task_id' value='" . (int)$row['id'] . "'>
                            <button type='submit' name='complete_task'>Mark as Completed</button>
                        </form>" .
                        "</li>";
            }       
            $conn->close();
            ?>
                
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>