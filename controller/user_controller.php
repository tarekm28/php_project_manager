<?php
require_once __DIR__ . '/../Models/User.php';

class UserController
{
    public function index()
    {
        $users = User::getAll();
        require __DIR__ . '/../Views/admin/user_management.php';
    }

    public function create($username, $password, $role)
    {
        User::create($username, $password, $role);
        header('Location: admin_interface.php?page=user_management');
        exit();
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
            $userId = (int)$_POST['user_id'];
            User::delete($userId);
        }
        header('Location: admin_interface.php?page=user_management');
        exit();
    }
}