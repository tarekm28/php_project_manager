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
            <?php
            foreach ($tasks as $task) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($task['task']) . '</td>';
                echo '<td>' . htmlspecialchars($task['employee_responsible']) . ' - ' . htmlspecialchars($task['role']) . '</td>';
                echo '<td>' . htmlspecialchars($task['status']) . '</td>';
                echo '<td>' . htmlspecialchars($task['created_at']) . '</td>';
                echo '<td>' . htmlspecialchars($task['updated_at']) . '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</section>