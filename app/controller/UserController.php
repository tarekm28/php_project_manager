<?php

require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/Response.php';

class UserController extends Controller
{
    private User $user;

    public function __construct()
    {
        Auth::requireAdmin();

        $this->user = $this->model('User');
    }


    public function index(): void
    {
        $users = $this->user->getAll();

        $this->view('admin/user_management', [
            'users' => $users
        ]);
    }


    public function create(): void
    {
        $this->user->createUser(
            $_POST['username'],
            $_POST['password'],
            $_POST['role']
        );

        Response::redirect('index.php?route=/&page=user_management');
    }


    public function delete(): void
    {
        $this->user->deleteUser(
            (int)$_POST['user_id']
        );

        Response::redirect('index.php?route=/&page=user_management');
    }
}