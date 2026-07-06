<?php
require_once 'auth.php';
require_login();
?>

<!DOCTYPE html>
<html lang="en">
    <div class="container">
        <nav>
            <ul>
                <?php if ($_SESSION['role'] === 'admin'): 
                    header("Location: admin/admin_interface.php");
                elseif ($_SESSION['role'] !== 'admin'): 
                    header("Location: employee/employee_interface.php");
                endif; ?>
            </ul>
        </nav>
        <form method="POST" action="logout.php">
            <button type="submit">Logout</button>
        </form>
    </div>
</html>