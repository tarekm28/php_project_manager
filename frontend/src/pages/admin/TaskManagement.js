import React, { useState, useEffect } from 'react';
import { Button, Modal, Form, Table } from 'react-bootstrap';
import api from '../../api/client';

export default function TaskManagement() {
    const [tasks, setTasks] = useState([]);
    const [users, setUsers] = useState([]);
    const [showAdd, setShowAdd] = useState(false);
    const [showEdit, setShowEdit] = useState(false);
    const [editingTask, setEditingTask] = useState(null);
    const [refresh, setRefresh] = useState(0);
    const [showDelete, setShowDelete] = useState(false);
    const [taskToDelete, setTaskToDelete] = useState(null);

    useEffect(() => {
        loadData();
    }, [refresh]);

    async function loadData() {
        const [tasksData, usersData] = await Promise.all([
            api('/tasks'),
            api('/users')
        ]);
        setTasks(tasksData);
        setUsers(usersData);
    }

    async function handleCreate(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        await api('/tasks', {
            method: 'POST',
            body: JSON.stringify({
                task: formData.get('task'),
                role: formData.get('role')
            })
        });
        setShowAdd(false);
        setRefresh(r => r + 1);
    }

    async function handleEdit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = {
            task_id: editingTask.id,
            task: formData.get('task'),
            status: formData.get('status'),
            assigned_to: formData.get('assigned_to'),
            employee_responsible: formData.get('employee_responsible') || null
        };
        await api('/tasks', {
            method: 'PATCH',
            body: JSON.stringify(data)
        });
        setShowEdit(false);
        setEditingTask(null);
        setRefresh(r => r + 1);
    }

    function handleDelete(taskId) {
    setTaskToDelete(taskId);
    setShowDelete(true);
    }

    async function confirmDelete() {
    if (!taskToDelete) return;

    await api('/tasks', {
        method: 'DELETE',
        body: JSON.stringify({ task_id: taskToDelete })
    });

    setShowDelete(false);
    setTaskToDelete(null);
    setRefresh(r => r + 1);
    }

    function cancelDelete() {
    setShowDelete(false);
    setTaskToDelete(null);
    }

    function openEdit(task) {
        setEditingTask(task);
        setShowEdit(true);
    }

    const userOptions = users.map(u => 
        <option key={u.id} value={u.username}>{u.username} ({u.role})</option>
    );

    return (
        <section>
            <h2>Task Management</h2>
            <Button onClick={() => setShowAdd(true)} className="mb-3">Add Task</Button>

            {/* Add Modal */}
            <Modal show={showAdd} onHide={() => setShowAdd(false)}>
                <Modal.Header closeButton>
                    <Modal.Title>Add Task</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <Form onSubmit={handleCreate}>
                        <Form.Group className="mb-3">
                            <Form.Control name="task" placeholder="Enter task" required />
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Select name="role" required>
                                <option value="admin">Admin</option>
                                <option value="developers">Developer</option>
                                <option value="hr">HR</option>
                                <option value="accounting">Accounting</option>
                            </Form.Select>
                        </Form.Group>
                        <Button type="submit">Create</Button>
                    </Form>
                </Modal.Body>
            </Modal>

            {/* Edit Modal */}
            <Modal show={showEdit} onHide={() => setShowEdit(false)}>
                <Modal.Header closeButton>
                    <Modal.Title>Edit Task</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    {editingTask && (
                        <Form onSubmit={handleEdit}>
                            <input type="hidden" name="task_id" value={editingTask.id} />
                            <Form.Group className="mb-3">
                                <Form.Label>Task Name</Form.Label>
                                <Form.Control 
                                    name="task" 
                                    defaultValue={editingTask.task} 
                                    required 
                                />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Status</Form.Label>
                                <Form.Select name="status" defaultValue={editingTask.status}>
                                    <option>Pending</option>
                                    <option>In Progress</option>
                                    <option>Completed</option>
                                </Form.Select>
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Assigned To (Role)</Form.Label>
                                <Form.Select name="assigned_to" defaultValue={editingTask.assigned_to}>
                                    <option value="admin">Admin</option>
                                    <option value="developers">Developer</option>
                                    <option value="hr">HR</option>
                                    <option value="accounting">Accounting</option>
                                </Form.Select>
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Employee Responsible</Form.Label>
                                <Form.Select name="employee_responsible" defaultValue={editingTask.employee_responsible || ''}>
                                    <option value="">Unassigned</option>
                                    {userOptions}
                                </Form.Select>
                            </Form.Group>
                            <Button type="submit">Update</Button>
                        </Form>
                    )}
                </Modal.Body>
            </Modal>
            {/* Delete Confirmation Modal */}
            <Modal show={showDelete} onHide={cancelDelete} centered>
                <Modal.Header closeButton>
                    <Modal.Title>Delete Task</Modal.Title>
                </Modal.Header>

                <Modal.Body>
                    Are you sure you want to delete this task?
                    <br />
                    <strong>This action cannot be undone.</strong>
                </Modal.Body>

                <Modal.Footer>
                    <Button variant="secondary" onClick={cancelDelete}>
                        Cancel
                    </Button>

                    <Button variant="danger" onClick={confirmDelete}>
                        Delete
                    </Button>
                </Modal.Footer>
            </Modal>

            <Table striped>
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Assigned to</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {tasks.map(task => (
                        <tr key={task.id}>
                            <td>{task.task}</td>
                            <td>{task.employee_responsible || task.assigned_to || 'Unassigned'}</td>
                            <td>{task.status}</td>
                            <td>{task.created_at}</td>
                            <td>{task.updated_at}</td>
                            <td>
                                <Button size="sm" variant="warning" onClick={() => openEdit(task)}>Edit</Button>{' '}
                                <Button size="sm" variant="danger" onClick={() => handleDelete(task.id)}>Delete</Button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </Table>
        </section>
    );
}