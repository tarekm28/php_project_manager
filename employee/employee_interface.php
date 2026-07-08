<?php
require_once '../auth.php';
require_login();

$page = $_GET['page'] ?? 'project_overview';
$allowedPages = ['project_overview', 'team_tasks', 'current_tasks'];

if (!in_array($page, $allowedPages, true)) {
    $page = 'project_overview';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['take_task'], $_POST['task_id'])) {
    $task_id = (int) $_POST['task_id'];
    $employee_name = $_SESSION['username'];
    take_responsibility($task_id, $employee_name);
    header("Location: employee_interface.php?page=team_tasks");
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
    header("Location: employee_interface.php?page=current_tasks");
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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
    <nav class="navbar navbar-expand bg-body-tertiary px-3">
        <ul class="navbar-nav d-flex flex-row gap-3">
            <li class="nav-item">
                <a class="nav-link<?= $page === 'project_overview' ? ' active' : '' ?>" href="employee_interface.php?page=project_overview">Project Overview</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?= $page === 'team_tasks' ? ' active' : '' ?>" href="employee_interface.php?page=team_tasks">Team Tasks</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?= $page === 'current_tasks' ? ' active' : '' ?>" href="employee_interface.php?page=current_tasks">Current Tasks</a>
            </li>
        </ul>
    </nav>

    <div class="container py-4">
        <?php
        switch ($page) {
            case 'team_tasks':
                include 'team_tasks.php';
                break;
            case 'current_tasks':
                include 'current_tasks.php';
                break;
            case 'project_overview':
                include 'project_overview.php';
                break;
            default:
                include 'project_overview.php';
                break;
        }
        ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
    $(document).ready(function () {
        if ($('#teamTasksTable').length) {
            $('#teamTasksTable').DataTable();
        }
        if ($('#currentTasksTable').length) {
            $('#currentTasksTable').DataTable();
        }
        if ($('#projectOverviewTable').length) {
            $('#projectOverviewTable').DataTable();
        }
    });
    </script>
    </body>
</html>