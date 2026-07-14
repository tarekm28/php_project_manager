<?php
$page = $page ?? ($_GET['page'] ?? 'project_overview');
$tasks = $tasks ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body>
    <header>
        <h1>Employee Dashboard</h1>
        <div style="position:absolute; top:16px; right:16px;">
            <form method="POST" action="index.php?route=/logout">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>
    <nav class="navbar navbar-expand bg-body-tertiary px-3">
        <ul class="navbar-nav d-flex flex-row gap-3">
            <li class="nav-item">
                <a class="nav-link<?= $page === 'project_overview' ? ' active' : '' ?>" href="index.php?route=/&page=project_overview">Project Overview</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?= $page === 'team_tasks' ? ' active' : '' ?>" href="index.php?route=/&page=team_tasks">Team Tasks</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?= $page === 'current_tasks' ? ' active' : '' ?>" href="index.php?route=/&page=current_tasks">Current Tasks</a>
            </li>
        </ul>
    </nav>

    <div class="container py-4">
        <?php
        switch ($page) {
            case 'team_tasks':
                require __DIR__ . '/team_tasks.php';
                break;
            case 'current_tasks':
                require __DIR__ . '/current_tasks.php';
                break;
            case 'project_overview':
            default:
                require __DIR__ . '/project_overview.php';
                break;
        }
        ?>
    </div>

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