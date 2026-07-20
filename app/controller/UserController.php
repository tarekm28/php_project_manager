<?php
require_once __DIR__ . '/../core/Controller.php';

use OpenApi\Attributes as OA;

class UserController extends Controller
{
    private User $user;
    private ActivityLog $log;

    public function __construct()
    {
        $this->user = $this->model('User');
        $this->log = $this->model('ActivityLog');
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
        $newUserId = (int) $this->user->getLastID();

        $user = Auth::user();
        $this->log->log(
            $user['id'],
            $user['username'],
            'create',
            'user',
            $newUserId,
            null,
            ['username' => $username, 'role' => $role]
        );

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

        $oldUser = $this->user->getUserById($userId);
        
        $this->user->deleteUser($userId);

        $user = Auth::user();
        $safeOld = $oldUser ?: [];
        unset($safeOld['password']);

        $this->log->log(
            $user['id'],
            $user['username'],
            'delete',
            'user',
            $userId,
            $safeOld,
            null
        );

        Response::json(['success' => true]);
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

        if (!$userId) {
            Response::json(['error' => 'User ID required'], 400);
            return;
        }

        $oldUser = $this->user->getUserById($userId);
        if (!$oldUser) {
            Response::json(['error' => 'User not found'], 404);
            return;
        }

        $updateData = [];
        $allowedFields = ['username', 'password', 'role'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field]) && $data[$field] !== '') {
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateData)) {
            Response::json(['error' => 'No fields to update'], 400);
            return;
        }

        $success = $this->user->updateUser($userId, $updateData);

        if ($success) {
            $user = Auth::user();
            $safeOld = $oldUser;
            unset($safeOld['password']);
            $safeNew = array_merge($safeOld, $updateData);
            unset($safeNew['password']);

            $this->log->log(
                $user['id'],
                $user['username'],
                'update',
                'user',
                $userId,
                $safeOld,
                $safeNew
            );

            Response::json(['success' => true, 'message' => 'User updated']);
        } else {
            Response::json(['error' => 'Update failed'], 500);
        }
    }

    public function getUserTasks(): void
    {
       Auth::requireAdmin();
        $username = $_GET['username'] ?? '';
        if (!$username) {
            Response::json(['error' => 'Username required'], 400);
            return;
        }
        
        $taskModel = $this->model('Task');  
        $tasks = $taskModel->getByEmployee($username);

        $ongoing = array_filter($tasks, fn($t) => ($t['status'] ?? '') !== 'Completed');
    
        Response::json([
            'username' => $username,
            'ongoing_count' => count($ongoing),
            'ongoing_tasks' => array_values($ongoing)
        ]);
    }   


    public function reassignTasks(): void
    {
        Auth::requireAdmin();
        $data = $this->getInput();
        
        $username = $data['username'] ?? '';
        $action = $data['action'] ?? '';
        $newRole = $data['new_role'] ?? '';
        
        if (!$username || !in_array($action, ['change_role', 'unassign'])) {
            Response::json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $taskModel = $this->model('Task');
        $tasks = $taskModel->getByEmployee($username);
        $ongoing = array_filter($tasks, fn($t) => ($t['status'] ?? '') !== 'Completed');
        
        $updated = 0;
        foreach ($ongoing as $task) {
            if ($action === 'change_role') {
                $taskModel->update($task['id'], [
                    'assigned_to' => $newRole,
                    'employee_responsible' => $username
                ]);
            } else {
                $taskModel->update($task['id'], [
                    'employee_responsible' => null,
                    'status' => 'Pending'
                ]);
            }
            $updated++;
        }
        
        Response::json([
            'success' => true,
            'action' => $action,
            'updated_count' => $updated
        ]);
    }
}