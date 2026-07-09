<section id="task-management">
    <h2>Task Undertaking</h2>

    <table id="currentTasksTable" class="table table-striped">
        <thead>
            <tr>
                <th>Task</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (($tasks ?? []) as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['task'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                    <td>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='task_id' value='<?= (int) ($row['id'] ?? 0) ?>'>
                            <button type='submit' name='complete_task' class='btn btn-sm btn-success'>Mark as Completed</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>