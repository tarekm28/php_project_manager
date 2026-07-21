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
                in: "query",
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
        path: "/tasks",
        summary: "Update a task (admin only)",
        tags: ["Tasks"],
        security: [["sessionAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "task_id", type: "integer", example: 1),
                    new OA\Property(property: "task", type: "string"),
                    new OA\Property(property: "assigned_to", type: "string"),
                    new OA\Property(property: "employee_responsible", type: "string"),
                    new OA\Property(property: "status", type: "string", enum: ["Pending", "In Progress", "Completed"])
                ],
                required: ["task_id"]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Task updated successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean"),
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "task_id", type: "integer")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid input or role mismatch",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden (admin access required)",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Forbidden")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Task not found"
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

    #[OA\Get(
        path: "/users/tasks",
        summary: "Get tasks for a user",
        tags: ["Users"],
        security: [["sessionAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "username",
                in: "query",
                required: true,
                description: "Username of the user",
                schema: new OA\Schema(type: "string", example: "john_doe")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of tasks for the user",
                content: new OA\JsonContent(type: "object", properties: [
                    new OA\Property(property: "username", type: "string", example: "john_doe"),
                    new OA\Property(property: "ongoing_count", type: "integer", example: 5),
                    new OA\Property(property: "ongoing_tasks", type: "array", items: new OA\Items(ref: "#/components/schemas/Task"))
                ])
            ),
            new OA\Response(
                response: 400,
                description: "Bad request",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "error", type: "string", example: "Username required")
                ])
            )
        ]
    )]
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


   #[OA\Post(
        path: "/users/reassign-tasks",
        summary: "Reassign or unassign ongoing tasks when changing a user's role (admin only)",
        tags: ["Users"],
        security: [["sessionAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "username", type: "string", example: "john_doe"),
                    new OA\Property(property: "action", type: "string", enum: ["change_role", "unassign"]),
                    new OA\Property(property: "new_role", type: "string", example: "HR")
                ],
                required: ["username", "action"]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Tasks reassigned successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "action", type: "string", example: "change_role"),
                        new OA\Property(property: "updated_count", type: "integer", example: 3),
                        new OA\Property(property: "updated_ids", type: "array", items: new OA\Items(type: "integer"))
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Invalid request",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Invalid request")
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden (admin access required)",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Forbidden")
                    ]
                )
            )
        ]
    )]
    public function reassignTasks(): void
    {
    Auth::requireAdmin();
    $data = $this->getInput();
    
    $username = $data['username'] ?? '';
    $action   = $data['action']   ?? ''; 
    $newRole  = $data['new_role'] ?? '';
    
    if (!$username || !in_array($action, ['change_role', 'unassign'])) {
        Response::json(['error' => 'Invalid request'], 400);
        return;
    }
}

    
}