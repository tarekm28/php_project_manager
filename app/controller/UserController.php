<?php
require_once __DIR__ . '/../core/Controller.php';

use OpenApi\Attributes as OA;

class UserController extends Controller
{
    private User $user;

    public function __construct()
    {
        $this->user = $this->model('User');
    }

    #[OA\Get(
        path: "/users",
        summary: "Get all users",
        tags: ["Users"],
        security: [["sessionAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of users",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/User"))
            )
        ]
    )]
    public function index(): void
    {
        $users = $this->user->getAll();
        Response::json($users);
    }

    #[OA\Post(
        path: "/users",
        summary: "Create a new user",
        tags: ["Users"],
        security: [["sessionAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: "username", type: "string", example: "john_doe"),
                new OA\Property(property: "password", type: "string", example: "secret"),
                new OA\Property(property: "role", type: "string", example: "user")
            ])
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User created",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "success", type: "boolean", example: true)
                ])
            ),
            new OA\Response(
                response: 400,
                description: "Bad request",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "error", type: "string", example: "All fields required")
                ])
            )
        ]
    )]
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

    #[OA\Delete(
        path: "/users",
        summary: "Delete a user",
        tags: ["Users"],
        security: [["sessionAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                in: "path",
                required: true,
                description: "ID of the user to delete",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "User deleted",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "success", type: "boolean", example: true)
                ])
            ),
            new OA\Response(
                response: 400,
                description: "Bad request",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "error", type: "string", example: "User ID required")
                ])
            )
        ]
    )]
    public function delete(): void
    {
        $data = $this->getInput();
        $userId = (int)($data['user_id'] ?? 0);

        if (!$userId) {
            Response::json(['error' => 'User ID required'], 400);
            return;
        }

        $this->user->deleteUser($userId);
        Response::json(['success' => true], 200);
    }

    #[OA\Patch(
        path: "/users",
        summary: "Update a user",
        tags: ["Users"],
        security: [["sessionAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "user_id",
                in: "path",
                required: true,
                description: "ID of the user to update",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "username", type: "string", example: "john_doe"),
                    new OA\Property(property: "password", type: "string", example: "new_secret"),
                    new OA\Property(property: "role", type: "string", example: "developers")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "User updated",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "success", type: "boolean", example: true)
                ])
            ),
            new OA\Response(
                response: 400,
                description: "Bad request",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "error", type: "string", example: "User ID and update data required")
                ])
            )
        ]
    )]
    public function edit(): void
    {
        $data = $this->getInput();
        $userId = (int)($data['user_id'] ?? 0);
        $updateData = [];
        $allowedFields = ['username', 'password', 'role'];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== '') {
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateData) && !empty($data['update_data'])) {
            $updateData = $data['update_data'];
        }

        if (!$userId || empty($updateData)) {
            Response::json(['error' => 'User ID and update data required'], 400);
            return;
        }

        $success = $this->user->updateUser($userId, $updateData);
        if ($success) {
            Response::json([
                'success' => true,
                'message' => 'User updated successfully',
                'updated_user' => $this->user->getUserById($userId)
                ], 200);
        } else {
            Response::json(['error' => 'Update failed'], 500);
        }
    }
}