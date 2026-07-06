<section id="project-overview">
            <h2>Project Overview</h2>
            <div id="project-list">
                <?php
                        $conn = new mysqli("localhost", "root", "", "new_proj");
                        $result = $conn->query("SELECT * FROM tasks ORDER BY updated_at DESC");

                        while ($row = $result->fetch_assoc()) {
                            if (isset($row['employee_responsible']) && !empty($row['employee_responsible'])) {
                                $employee_responsible = $row['employee_responsible'];
                            } else {
                                $employee_responsible = 'Unassigned';
                            }
                            echo "<li>" . $row['task'] . " - " . $employee_responsible . "-" . $row['assigned_to'] . "-" . $row['status'] . "</li>";
                        }

                        $conn->close();
                ?>
            </div>
        </section>