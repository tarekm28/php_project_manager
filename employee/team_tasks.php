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
            <?php
            $conn = new mysqli("localhost", "root", "", "new_proj");
            $role = $_SESSION['role'];
            $result = $conn->query("SELECT * FROM tasks WHERE assigned_to = '$role' ORDER BY created_at DESC");

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $assigned = $row['employee_responsible'] ?? '';
                    if ($assigned === '') {
                        $assigned = 'Unassigned';
                    }

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['task']) . "</td>";
                    echo "<td>" . htmlspecialchars($assigned) . "</td>";
                    echo "<td>";
                    if ($assigned === 'Unassigned') {
                        echo "<form method='POST' style='display:inline;'>
                                <input type='hidden' name='task_id' value='" . (int)$row['id'] . "'>
                                <button type='submit' name='take_task' class='btn btn-sm btn-primary'>Take Task</button>
                              </form>";
                    }
                    echo "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                     echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['updated_at']) . "</td>";
                    echo "</tr>";
                }
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</section>