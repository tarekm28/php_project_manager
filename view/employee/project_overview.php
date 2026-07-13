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
            <?php foreach (($tasks ?? []) as $row): ?>
                <?php $employeeResponsible = !empty($row['employee_responsible']) ? $row['employee_responsible'] : 'Unassigned'; ?>
                <tr>
                    <td><?= htmlspecialchars($row['task'] ?? '') ?></td>
                    <td><?= htmlspecialchars($employeeResponsible . ' - ' . ($row['assigned_to'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['updated_at'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>