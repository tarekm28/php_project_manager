<section id="project-overview">
    <h2>Project Overview</h2>
    <div id="project-list">
        <table id="projectOverviewTable" class="table table-striped">
        <thead>
            <tr>
                <th>Task</th>
                <th>Assigned to</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (($tasks ?? []) as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['task'] ?? '') ?></td>
                    <td><?= htmlspecialchars($task['employee_responsible'] ?? $task['assigned_to'] ?? 'Unassigned') ?></td>
                    <td><?= htmlspecialchars($task['status'] ?? 'Pending') ?></td>
                    <td><?= htmlspecialchars($task['created_at'] ?? '') ?></td>
                    <td><?= htmlspecialchars($task['updated_at'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>