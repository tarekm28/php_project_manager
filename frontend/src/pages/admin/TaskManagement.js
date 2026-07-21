import React, { useState, useEffect } from 'react';
import { Button, Modal, Form, Alert } from 'react-bootstrap';
import api from '../../api/client';
import DataTable from '../../components/DataTable';
import PageLoader from '../../components/PageLoader';
import StatusBadge from '../../components/StatusBadge';
import ConfirmModal from '../../components/ConfirmModal';

export default function TaskManagement() {
    const [tasks, setTasks] = useState([]);
    const [users, setUsers] = useState([]);
    const [showAdd, setShowAdd] = useState(false);
    const [showEdit, setShowEdit] = useState(false);
    const [editingTask, setEditingTask] = useState(null);
    const [refresh, setRefresh] = useState(0);
    const [showDelete, setShowDelete] = useState(false);
    const [taskToDelete, setTaskToDelete] = useState(null);
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(true);

    const [editForm, setEditForm] = useState({
        task: '',
        status: 'Pending',
        assigned_to: 'Admin',
        employee_responsible: ''
    });

    useEffect(() => {
        loadData();
    }, [refresh]);

    async function loadData() {
        setLoading(true);
        try {
            const [tasksData, usersData] = await Promise.all([
                api('/tasks'),
                api('/users')
            ]);
            setTasks(tasksData);
            console.log(tasksData)
            setUsers(usersData);
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
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
        setError(''); 
        
        const data = {
            task_id: editingTask.id,
            task: editForm.task,
            status: editForm.status,
            assigned_to: editForm.assigned_to,
            employee_responsible: editForm.employee_responsible || null
        };
        
        try {
            await api('/tasks', {
                method: 'PATCH',
                body: JSON.stringify(data)
            });
            closeEdit();
            setRefresh(r => r + 1);
        } catch (err) {
            setError(err.message);
        }
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

    function openEdit(task) {
        console.log('OPENING EDIT FOR TASK:', task);
        setError('');
        setEditingTask(task);
        setEditForm({
            task: task.task || '',
            status: task.status || 'Pending',
            assigned_to: task.assigned_to || 'Admin',
            employee_responsible: task.employee_responsible || ''
        });
        setShowEdit(true);
    }

    function closeEdit() {
        setShowEdit(false);
        setError('');
        setEditingTask(null);
        setEditForm({
            task: '',
            status: 'Pending',
            assigned_to: 'Admin',
            employee_responsible: ''
        });
    }

    function handleFormChange(field, value) {
        setEditForm(prev => ({
            ...prev,
            [field]: value
        }));
    }

    if (loading) return <PageLoader />;

    return (
        <section>
            <div className="d-flex justify-content-between align-items-center mb-3">
                <h2 className="h4 mb-0">Task Management</h2>
                <Button onClick={() => setShowAdd(true)}>Add Task</Button>
            </div>

            <Modal show={showAdd} onHide={() => setShowAdd(false)} backdrop="static">
                <Modal.Header closeButton>
                    <Modal.Title>Add Task</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <Form onSubmit={handleCreate}>
                        <Form.Group className="mb-3">
                            <Form.Label>Task Description</Form.Label>
                            <Form.Control name="task" placeholder="Enter task" required />
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Role</Form.Label>
                            <Form.Select name="role" required>
                                <option value="admin">Admin</option>
                                <option value="developers">Developer</option>
                                <option value="hr">HR</option>
                                <option value="accounting">Accounting</option>
                            </Form.Select>
                        </Form.Group>
                        <div className="d-flex gap-2 justify-content-end">
                            <Button variant="secondary" onClick={() => setShowAdd(false)}>Cancel</Button>
                            <Button type="submit" variant="primary">Create</Button>
                        </div>
                    </Form>
                </Modal.Body>
            </Modal>

            <Modal show={showEdit} onHide={closeEdit} backdrop="static">
                <Modal.Header closeButton>
                    <Modal.Title>Edit Task</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    {error && (
                        <Alert variant="danger" className="d-flex align-items-center">
                            <span className="me-2">⚠️</span>
                            <div>{error}</div>
                        </Alert>
                    )}
                    <Form onSubmit={handleEdit}>
                        <input type="hidden" name="task_id" value={editingTask?.id || ''} />
                        <Form.Group className="mb-3">
                            <Form.Label>Task Name</Form.Label>
                            <Form.Control 
                                name="task" 
                                value={editForm.task}
                                onChange={e => handleFormChange('task', e.target.value)}
                                required 
                            />
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Status</Form.Label>
                            <Form.Select 
                                name="status" 
                                value={editForm.status}
                                onChange={e => handleFormChange('status', e.target.value)}
                            >
                                <option>Pending</option>
                                <option>In Progress</option>
                                <option>Completed</option>
                            </Form.Select>
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Assigned To (Role)</Form.Label>
                            <Form.Select 
                                name="assigned_to" 
                                value={editForm.assigned_to}
                                onChange={e => handleFormChange('assigned_to', e.target.value)}
                            >
                                <option value="Admin">Admin</option>
                                <option value="Developers">Developer</option>
                                <option value="HR">HR</option>
                                <option value="Accounting">Accounting</option>
                            </Form.Select>
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Employee Responsible</Form.Label>
                            <Form.Select 
                                name="employee_responsible" 
                                value={editForm.employee_responsible}
                                onChange={e => handleFormChange('employee_responsible', e.target.value)}>
                                <option value="">Unassigned</option>
                                {users.map(u => (
                                    <option key={u.id} value={u.username}>
                                        {u.username} ({u.role})
                                    </option>
                                ))}
                            </Form.Select>
                        </Form.Group>
                        <div className="d-flex gap-2 justify-content-end">
                            <Button variant="secondary" onClick={closeEdit}>Cancel</Button>
                            <Button type="submit" variant="primary">Update</Button>
                        </div>
                    </Form>
                </Modal.Body>
            </Modal>

            <ConfirmModal 
                show={showDelete}
                onHide={() => { setShowDelete(false); setTaskToDelete(null); }}
                onConfirm={confirmDelete}
                title="Delete Task"
                body={<>Are you sure you want to delete this task?<br /><strong>This action cannot be undone.</strong></>}
            />

            <DataTable id="taskManagementTable" refreshKey={refresh}>
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Assigned to</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th style={{ width: '120px' }}>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {tasks.map(task => (
                        <tr key={task.id}>
                            <td className="fw-medium">{task.task}</td>
                            <td>{task.employee_responsible ? `${task.employee_responsible} - ${task.assigned_to}` : `Unassigned - ${task.assigned_to}`}</td>
                            <td><StatusBadge status={task.status} /></td>
                            <td>{task.created_at}</td>
                            <td>{task.updated_at}</td>
                            <td>
                                <Button size="sm" variant="warning" onClick={() => openEdit(task)}>Edit</Button>{' '}
                                <Button size="sm" variant="danger" onClick={() => handleDelete(task.id)}>Delete</Button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </DataTable>
        </section>
    );
}