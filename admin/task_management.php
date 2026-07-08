<section id="task-management">
    <h2>Task Management</h2>

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
        Add Task
    </button>

    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTaskModalLabel">Add Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../add_tasks.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="task" class="form-control" placeholder="Enter task" required>
                        </div>
                        <div class="mb-3">
                            <select name="role" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="developers">Developer</option>
                                <option value="hr">HR</option>
                                <option value="accounting">Accounting</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Create Task</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="task-list">
        <table id="taskManagementTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Assigned to</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $conn = new mysqli("localhost", "root", "", "new_proj");
                $result = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");

                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        if (isset($row['employee_responsible']) && !empty($row['employee_responsible'])) {
                            $employeeResponsible = $row['employee_responsible'];
                        } else {
                            $employeeResponsible = 'Unassigned';
                        }

                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($row['task']) . '</td>';
                        echo '<td>' . htmlspecialchars($employeeResponsible) . ' - ' . htmlspecialchars($row['assigned_to']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['updated_at']) . '</td>';
                        echo '<td><a href="../delete_tasks.php?id=' . (int) $row['id'] . '" class="btn btn-sm btn-danger">Delete</a></td>';
                        echo '</tr>';
                    }
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</section>