import React, { useState, useEffect } from 'react';
import { Button, Table, Alert } from 'react-bootstrap';
import api from '../../api/client';

export default function CurrentTasks() {
    const [tasks, setTasks] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [completingId, setCompletingId] = useState(null);

    useEffect(() => {
        loadTasks();
    }, []);

    async function loadTasks() {
        try {
            setLoading(true);
            const data = await api('/tasks/mine');
            setTasks(data);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    }

    async function handleComplete(taskId) {
        setCompletingId(taskId);
        setError('');

        try {
            await api('/tasks/complete', {
                method: 'POST',
                body: JSON.stringify({ task_id: taskId })
            });

            setTasks(prev => prev.map(task => 
                task.id === taskId 
                    ? { ...task, status: 'Completed' }
                    : task
            ));
        } catch (err) {
            setError(err.message);
        } finally {
            setCompletingId(null);
        }
    }

    if (loading) return <div>Loading tasks...</div>;

    const activeTasks = tasks.filter(t => t.status !== 'Completed');

    return (
        <section>
            <h2>My Current Tasks</h2>
            
            {error && <Alert variant="danger">{error}</Alert>}

            {activeTasks.length === 0 ? (
                <p className="text-muted">No active tasks assigned to you.</p>
            ) : (
                <Table striped>
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {activeTasks.map(task => (
                            <tr key={task.id}>
                                <td>{task.task}</td>
                                <td>
                                    <span className={`badge bg-${getStatusColor(task.status)}`}>
                                        {task.status}
                                    </span>
                                </td>
                                <td>
                                    <Button
                                        size="sm"
                                        variant="success"
                                        disabled={completingId === task.id}
                                        onClick={() => handleComplete(task.id)}
                                    >
                                        {completingId === task.id ? 'Updating...' : 'Mark as Completed'}
                                    </Button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </Table>
            )}

            {tasks.some(t => t.status === 'Completed') && (
                <>
                    <h4 className="mt-4 text-muted">Completed</h4>
                    <Table striped className="opacity-75">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Status</th>
                                <th>Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            {tasks
                                .filter(t => t.status === 'Completed')
                                .map(task => (
                                    <tr key={task.id}>
                                        <td>{task.task}</td>
                                        <td>
                                            <span className="badge bg-success">Completed</span>
                                        </td>
                                        <td>{task.updated_at}</td>
                                    </tr>
                                ))}
                        </tbody>
                    </Table>
                </>
            )}
        </section>
    );
}

function getStatusColor(status) {
    switch (status) {
        case 'Pending': return 'warning';
        case 'In Progress': return 'info';
        case 'Completed': return 'success';
        default: return 'secondary';
    }
}