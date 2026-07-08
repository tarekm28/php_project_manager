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
            <?php
            $conn = new mysqli("localhost", "root", "", "new_proj");
            $user = $_SESSION['username'];
            $result = $conn->query("SELECT * FROM tasks WHERE employee_responsible = '$user' AND status != 'Completed' ORDER BY created_at DESC");

            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['task']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='task_id' value='" . (int)$row['id'] . "'>
                                <button type='submit' name='complete_task' class='btn btn-sm btn-success'>Mark as Completed</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</section>