<?php
require_once __DIR__ . '/../auth.php';
require_admin();

$page = $_GET['page'] ?? 'project_overview';
$allowedPages = ['project_overview', 'task_management', 'user_management'];

if (!in_array($page, $allowedPages, true)) {
    $page = 'project_overview';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Manager Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body>
    <nav class="navbar navbar-expand bg-body-tertiary px-3">
        <div class="container-fluid p-0">
            <ul class="navbar-nav d-flex flex-row gap-3">
                <li class="nav-item">
                    <a class="nav-link<?= $page === 'project_overview' ? ' active' : '' ?>" href="admin_interface.php?page=project_overview"<?= $page === 'project_overview' ? ' aria-current="page"' : '' ?>>
                        Project Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $page === 'task_management' ? ' active' : '' ?>" href="admin_interface.php?page=task_management"<?= $page === 'task_management' ? ' aria-current="page"' : '' ?>>
                        Task Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= $page === 'user_management' ? ' active' : '' ?>" href="admin_interface.php?page=user_management"<?= $page === 'user_management' ? ' aria-current="page"' : '' ?>>
                        User Management
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container py-4">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Project Manager Dashboard</h1>
            <form method="POST" action="../logout.php">
                <button type="submit" class="btn btn-outline-secondary">Logout</button>
            </form>
        </header>

        <?php
        switch ($page) {
            case 'task_management':
                include 'task_management.php';
                break;
            case 'user_management':
                include 'user_management.php';
                break;
            case 'project_overview':
            default:
                include 'project_overview.php';
                break;
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
    $(document).ready(function () {
        if ($('#taskManagementTable').length) {
            $('#taskManagementTable').DataTable();
        }
        if ($('#userManagementTable').length) {
            $('#userManagementTable').DataTable();
        }
        if ($('#projectOverviewTable').length) {
            $('#projectOverviewTable').DataTable();
        }
    });
    </script>
</body>
</html>