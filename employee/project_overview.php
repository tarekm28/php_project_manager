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
                    echo '</tr>';
                }
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</section>