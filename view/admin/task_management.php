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
                <?php foreach (($tasks ?? []) as $task): ?>
                    <tr>
                        <td><?= htmlspecialchars($task['task'] ?? '') ?></td>
                        <td><?= htmlspecialchars($task['assigned_to'] ?? $task['employee_responsible'] ?? 'Unassigned') ?></td>
                        <td><?= htmlspecialchars($task['status'] ?? 'Pending') ?></td>
                        <td><?= htmlspecialchars($task['created_at'] ?? '') ?></td>
                        <td><?= htmlspecialchars($task['updated_at'] ?? '') ?></td>
                        <td><a href="/proj1/delete_tasks.php?id=<?= (int) ($task['id'] ?? 0) ?>" class="btn btn-sm btn-danger">Delete</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>