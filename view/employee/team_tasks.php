<section id="team-overview">
    <h2>Team Overview</h2>

    <table id="teamTasksTable" class="table table-striped">
        <thead>
            <tr>
                <th>Task</th>
                <th>Assigned To</th>
                <th>Action</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (($tasks ?? []) as $row): ?>
                <?php $assigned = !empty($row['employee_responsible']) ? $row['employee_responsible'] : 'Unassigned'; ?>
                <tr>
                    <td><?= htmlspecialchars($row['task'] ?? '') ?></td>
                    <td><?= htmlspecialchars($assigned) ?></td>
                    <td>
                        <?php if ($assigned === 'Unassigned'): ?>
                            <form method='POST' action='index.php?route=/employee/tasks/take' style='display:inline;'>
                                <input type='hidden' name='task_id' value='<?= (int) ($row['id'] ?? 0) ?>'>
                                <button type='submit' name='take_task' class='btn btn-sm btn-primary'>Take Task</button>
                            </form>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['updated_at'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>