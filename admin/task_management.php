<section id="task-management">
            <h2>Task Management</h2>
                <div id="task-list">
                <div class="container">
                    <form action="../add_tasks.php" method="POST">
                        <input type="text" name="task" placeholder="Enter a new task" required>
                        <label for="role">Role:</label>
                        <select name="role" id="role" required>
                            <option value="developers">Developer</option>
                            <option value="hr">HR</option>
                            <option value="accounting">Accounting</option>
                            <option value="user">User</option>
                        </select>
                        <button type="submit">Add Task</button>
                    </form>
                        <?php
                        $conn = new mysqli("localhost", "root", "", "new_proj");
                        $result = $conn->query("SELECT * FROM tasks ORDER BY updated_at DESC");

                        while ($row = $result->fetch_assoc()) {
                            echo "<li>" . $row['task'] . 
                            " <a href='../delete_tasks.php?id=" . $row['id'] . "'>Delete</a></li>";
                        }

                        $conn->close();
                        ?>
                    </ul>
                </div>  
            </div>
        </section>