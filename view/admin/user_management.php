<section id="user-management">
    <h2>User Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        Add User
    </button>

    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../add_user.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                        </div>
                        <div class="mb-3">
                            <select name="role" class="form-select" required>
                                <option value="admin">Admin</option>
                                <option value="developers">Developer</option>
                                <option value="hr">HR</option>
                                <option value="accounting">Accounting</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Create User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="user-list">
        <table id="userManagementTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($users as $user) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($user['username']) . '</td>';
                    echo '<td>' . htmlspecialchars($user['role']) . '</td>';
                    echo '<td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="' . (int)$user['id'] . '">
                                <button type="submit" name="delete_user" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                          </td>';
                    echo '</tr>';
                }?>
            </tbody>
        </table>
    </div>

</section>