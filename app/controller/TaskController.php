<?php
require_once __DIR__ . '/../core/Controller.php';

use OpenApi\Attributes as OA;

class TaskController extends Controller
{
    private ActivityLog $log;

    public function __construct()
    {
        Auth::requireLogin();
        $this->task = $this->model('Task');
        $this->log = $this->model('ActivityLog');
    }

    #[OA\Get(
        path: "/tasks/all",
        summary: "Get all tasks",
        tags: ["Tasks"],
        security: [["sessionAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of tasks",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Task"))
            )
        ]
    )]
    public function index(): void
    {
        $tasks = $this->task->getAll();
        Response::json($tasks);
    }

    #[OA\Post(
        path: "/tasks",
        summary: "Create a new task (admin only)",
        tags: ["Tasks"],
        security: [["sessionAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: "task", type: "string", example: "Complete the project"),
                new OA\Property(property: "role", type: "string", example: "Developer")
            ])
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Task created",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "success", type: "boolean", example: true),
                    new OA\Property(property: "message", type: "string", example: "Task created")
                ]),
            ),
            new OA\Response(
                response: 400,
                description: "Bad request",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "error", type: "string", example: "Task and role required")
                ])
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden (admin access required)",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "error", type: "string", example: "Forbidden")
                ])
            )
        ]
    )]
    public function create(): void
    {
        Auth::requireAdmin();
        
        $data = $this->getInput();
        $task = $data['task'] ?? '';
        $role = $data['role'] ?? '';

        if (!$task || !$role) {
            Response::json(['error' => 'Task and role required'], 400);
            return;
        }

        $this->task->create($task, $role);
        $newTaskId = (int) $this->db->lastInsertId();

        $user = Auth::user();
        $this->log->log(
            $user['id'],
            $user['username'],
            'create',
            'task',
            $newTaskId,
            null,
            ['task' => $task, 'assigned_to' => $role, 'status' => 'Pending']
        );

        Response::json(['success' => true, 'message' => 'Task created', 'task_id' => $newTaskId], 201);
    }

    #[OA\Delete(
        path: "/tasks/{task_id}",
        summary: "Delete a task (admin only)",
        tags: ["Tasks"],
        security: [["sessionAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "task_id",
                in: "path",
                required: true,
                description: "ID of the task to delete",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Task deleted",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "success", type: "boolean", example: true)
                ])
            ),
            new OA\Response(
                response: 400,
                description: "Bad request",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "error", type: "string", example: "Task ID required")
                ])
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden (admin access required)",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "error", type: "string", example: "Forbidden")
                ])
            )
        ]
    )]
    public function delete(): void
    {
        Auth::requireAdmin();
        
        $data = $this->getInput();
        $taskId = (int)($data['task_id'] ?? 0);

        if (!$taskId) {
            Response::json(['error' => 'Task ID required'], 400);
            return;
        }

        $oldTask = $this->task->findById($taskId);
        
        $this->task->delete($taskId);

        $user = Auth::user();
        $this->log->log(
            $user['id'],
            $user['username'],
            'delete',
            'task',
            $taskId,
            $oldTask ?: null,
            null
        );

        Response::json(['success' => true]);
    }

    #[OA\Post(
        path: "/tasks/{task_id}/take",
        summary: "Take a task",
        tags: ["Tasks"],
        security: [["sessionAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "task_id",
                in: "path",
                required: true,
                description: "ID of the task to take",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Task taken",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "success", type: "boolean", example: true)
                ])
            ),
            new OA\Response(
                response: 400,
                description: "Bad request",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "error", type: "string", example: "Task ID required")
                ])
            )
        ]
    )]
    public function take(): void
    {
        $data = $this->getInput();
        $taskId = (int)($data['task_id'] ?? 0);
        $username = Auth::user()['username'] ?? '';

        if (!$taskId) {
            Response::json(['error' => 'Task ID required'], 400);
            return;
        }

        $oldTask = $this->task->findById($taskId);

        $this->task->take($taskId, $username);

        $user = Auth::user();
        $this->log->log(
            $user['id'],
            $user['username'],
            'take',
            'task',
            $taskId,
            $oldTask ?: null,
            array_merge($oldTask ?: [], ['employee_responsible' => $username, 'status' => 'In Progress'])
        );

        Response::json(['success' => true]);
    }

    #[OA\Post(
        path: "/tasks/{task_id}/complete",
        summary: "Complete a task",
        tags: ["Tasks"],
        security: [["sessionAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "task_id",
                in: "path",
                required: true,
                description: "ID of the task to complete",
                schema: new OA\Schema(type: "integer", example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Task completed",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "success", type: "boolean", example: true)
                ])
            ),
            new OA\Response(
                response: 400,
                description: "Bad request",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "error", type: "string", example: "Task ID required")
                ])
            )
        ]
    )]
    public function complete(): void
    {
        $data = $this->getInput();
        $taskId = (int)($data['task_id'] ?? 0);
        $username = Auth::user()['username'] ?? '';

        if (!$taskId) {
            Response::json(['error' => 'Task ID required'], 400);
            return;
        }

        $task = $this->task->findById($taskId);
        if (!$task || ($task['employee_responsible'] ?? '') !== $username) {
            Response::json(['error' => 'Not authorized'], 403);
            return;
        }

        $this->task->complete($taskId, $username);

        $user = Auth::user();
        $this->log->log(
            $user['id'],
            $user['username'],
            'complete',
            'task',
            $taskId,
            $task,
            array_merge($task, ['status' => 'Completed'])
        );

        Response::json(['success' => true, 'task_id' => $taskId, 'status' => 'Completed']);
    }

    #[OA\Get(
        path: "/tasks/my",
        summary: "Get tasks assigned to the current user",
        tags: ["Tasks"],
        security: [["sessionAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of tasks",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Task"))
            )
        ]
    )]
    public function myTasks(): void
    {
        $username = Auth::user()['username'] ?? '';
        $tasks = $this->task->getByEmployee($username);
        Response::json($tasks);
    }

    #[OA\Get(
        path: "/tasks/team",
        summary: "Get tasks assigned to the current user's team",
        tags: ["Tasks"],
        security: [["sessionAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of tasks",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Task"))
            )
        ]
    )]
    public function teamTasks(): void
    {
        $role = Auth::user()['role'] ?? '';
        $tasks = $this->task->getByRole($role);
        Response::json($tasks);
    }

    #[OA\Get(
        path: "/tasks",
        summary: "Get all tasks",
        tags: ["Tasks"],
        security: [["sessionAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of tasks",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Task"))
            )
        ]
    )]
    public function allTasks(): void
    {
        $tasks = $this->task->getAll();
        Response::json($tasks);
    }

    #[OA\Patch(
        path: "/tasks",
        summary: "Update a task",
        tags: ["Tasks"],
        security: [["sessionAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID of the task to update",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "task", type: "string"),
                    new OA\Property(property: "assigned_to", type: "string"),
                    new OA\Property(property: "employee_responsible", type: "string"),
                    new OA\Property(property: "status", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Task updated successfully",
                content: new OA\JsonContent(type:"object", properties:[
                    new OA\Property(property:"success", type:"boolean"),
                    new OA\Property(property:"message", type:"string"),
                    new OA\Property(property:"task_id", type:"integer")
                ])
            ),
            new OA\Response(
                response: 400,
                description: "Invalid input data"
            ),
            new OA\Response(
                response: 404,
                description: "Task not found"
            )
        ]
    )]
    public function edit(): void
    {
        Auth::requireAdmin();
        
        $data = $this->getInput();
        $taskId = (int)($data['task_id'] ?? 0);

        if (!$taskId) {
            Response::json(['error' => 'Task ID required'], 400);
            return;
        }

        $oldTask = $this->task->findById($taskId);
        if (!$oldTask) {
            Response::json(['error' => 'Task not found'], 404);
            return;
        }

        $updateData = [];
        $allowedFields = ['task', 'assigned_to', 'employee_responsible', 'status', 'priority', 'due_date'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateData)) {
            Response::json(['error' => 'No fields to update'], 400);
            return;
        }

        $success = $this->task->update($taskId, $updateData);

        if ($success) {
            $user = Auth::user();
            $this->log->log(
                $user['id'],
                $user['username'],
                'update',
                'task',
                $taskId,
                $oldTask,
                array_merge($oldTask, $updateData)
            );

            Response::json(['success' => true, 'message' => 'Task updated', 'task_id' => $taskId]);
        } else {
            Response::json(['error' => 'Update failed'], 500);
        }
    }
}