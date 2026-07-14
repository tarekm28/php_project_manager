<?php

class AuthController extends Controller
{
    public function me(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::json(['error' => 'Not authenticated'], 401);
            return;
        }
        Response::json($user);
    }

    public function authenticate(): void
    {
        $data = $this->getInput();
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password']) {
            Auth::login($user);
            Response::json([
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ]
            ]);
            return;
        }

        Response::json(['error' => 'Invalid credentials'], 401);
    }

    public function logout(): void
    {
        Auth::logout();
        Response::json(['success' => true]);
    }
}