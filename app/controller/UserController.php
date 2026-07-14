<?php

class UserController extends Controller
{
    private User $user;

    public function __construct()
    {
        $this->user = $this->model('User');
    }

    public function index(): void
    {
        $users = $this->user->getAll();
        Response::json($users);
    }

    public function create(): void
    {
        $data = $this->getInput();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        $role = $data['role'] ?? '';

        if (!$username || !$password || !$role) {
            Response::json(['error' => 'All fields required'], 400);
            return;
        }

        $this->user->createUser($username, $password, $role);
        Response::json(['success' => true], 201);
    }

    public function delete(): void
    {
        $data = $this->getInput();
        $userId = (int)($data['user_id'] ?? 0);

        if (!$userId) {
            Response::json(['error' => 'User ID required'], 400);
            return;
        }

        $this->user->deleteUser($userId);
        Response::json(['success' => true]);
    }
}