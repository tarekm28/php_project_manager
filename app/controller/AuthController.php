<?php
require_once __DIR__ . '/../core/Controller.php';

use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: "/me",
        summary: "Get current user",
        tags: ["Authentication"],
        security: [["sessionAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Current user information",
                content: new OA\JsonContent(ref: "#/components/schemas/User")
            ),
            new OA\Response(
                response: 401,
                description: "Not authenticated",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "error", type: "string", example: "Not authenticated")
                ])
            )
        ]
    )]
    public function me(): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::json(['error' => 'Not authenticated'], 401);
            return;
        }
        Response::json($user);
    }

    #[OA\Post(
        path: "/login",
        summary: "Authenticate user",
        tags: ["Authentication"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: "username", type: "string", example: "john_doe"),
                new OA\Property(property: "password", type: "string", example: "secret")
            ])
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Authentication successful",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "success", type: "boolean", example: true),
                    new OA\Property(property: "user", ref: "#/components/schemas/User")
                ])
            ),
            new OA\Response(
                response: 401,
                description: "Invalid credentials",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "error", type: "string", example: "Invalid credentials")
                ])
            )
        ]
    )]

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

    #[OA\Post(
        path: "/logout",
        summary: "Logout user",
        tags: ["Authentication"],
        security: [["sessionAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logout successful",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "success", type: "boolean", example: true)
                ])
            )
        ]
    )]
    public function logout(): void
    {
        Auth::logout();
        Response::json(['success' => true]);
    }
}