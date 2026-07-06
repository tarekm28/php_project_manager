<section id="user-management">
            <h2>User Management</h2>
            <div id="user-list">
                <div class="container">
                    <form action="../add_user.php" method="POST">
                        <input type="text" name="username" placeholder="Enter username" required>
                        <input type="password" name="password" placeholder="Enter password" required>
                        <label for="role">Role:</label>
                        <select name="role" id="role" required>
                            <option value="admin">Admin</option>
                            <option value="developers">Developer</option>
                            <option value="hr">HR</option>
                            <option value="accounting">Accounting</option>
                            <option value="user">User</option>
                        </select>
                        <button type="submit">Add User</button>
                    </form>

                <?php
                        $conn = new mysqli("localhost", "root", "", "new_proj");
                        $result = $conn->query("SELECT * FROM users ORDER BY id DESC"); 

                        while ($row = $result->fetch_assoc()) {
                            echo "<li>" . $row['username'] . " - " . $row['role'] . 
                            " <a href='../delete_user.php?id=" . $row['id'] . "'>Delete</a></li>";
                        }

                        $conn->close();
                ?>
            </div>
        </section>