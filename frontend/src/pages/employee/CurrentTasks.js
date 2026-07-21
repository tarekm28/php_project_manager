import React, { useState, useEffect } from 'react';
import { Button, Alert } from 'react-bootstrap';
import api from '../../api/client';
import DataTable from '../../components/DataTable';
import PageLoader from '../../components/PageLoader';
import StatusBadge from '../../components/StatusBadge';

export default function CurrentTasks() {
    const [tasks, setTasks] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [completingId, setCompletingId] = useState(null);

    useEffect(() => {
        loadTasks();
    }, []);

    async function loadTasks() {
        setLoading(true);
        try {
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
                task.id === taskId ? { ...task, status: 'Completed' } : task
            ));
        } catch (err) {
            setError(err.message);
        } finally {
            setCompletingId(null);
        }
    }

    if (loading) return <PageLoader />;

    const activeTasks = tasks.filter(t => t.status !== 'Completed');
    const completedTasks = tasks.filter(t => t.status === 'Completed');

    return (
        <section>
            <h2 className="h4 mb-3">My Current Tasks</h2>
            
            {error && <Alert variant="danger" className="py-2">{error}</Alert>}

            {activeTasks.length === 0 ? (
                <div className="alert alert-light border text-muted">No active tasks assigned to you.</div>
            ) : (
                <DataTable id="currentTasksTable" refreshKey={activeTasks.length}>
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Status</th>
                            <th style={{ width: '160px' }}>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {activeTasks.map(task => (
                            <tr key={task.id}>
                                <td className="fw-medium">{task.task}</td>
                                <td><StatusBadge status={task.status} /></td>
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
                </DataTable>
            )}

            {completedTasks.length > 0 && (
                <>
                    <h4 className="mt-4 text-muted h5">Completed Tasks</h4>
                    <div className="opacity-75">
                        <DataTable id="completedTasksTable" refreshKey={completedTasks.length}>
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Status</th>
                                    <th>Completed</th>
                                </tr>
                            </thead>
                            <tbody>
                                {completedTasks.map(task => (
                                    <tr key={task.id}>
                                        <td className="fw-medium">{task.task}</td>
                                        <td><StatusBadge status={task.status} /></td>
                                        <td>{task.updated_at}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </DataTable>
                    </div>
                </>
            )}
        </section>
    );
}