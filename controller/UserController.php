<?php

class UserController extends Controller
{
    private User $user;

    public function __construct()
    {
        Auth::requireLogin();

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
        $this->user->create(
            $_POST['username'],
            $_POST['password'],
            $_POST['role']
        );

        header('Location: /users');
        exit;
    }


    public function delete(): void
    {
        $this->user->delete(
            (int)$_POST['user_id']
        );

        header('Location: /users');
        exit;
    }
}