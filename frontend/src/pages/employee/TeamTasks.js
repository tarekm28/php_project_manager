import React, { useState, useEffect } from 'react';
import { Button, Table, Badge } from 'react-bootstrap';
import api from '../../api/client';

export default function TeamTasks() {
    const [tasks, setTasks] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refresh, setRefresh] = useState(0);

    useEffect(() => {
        loadTasks();
    }, [refresh]);

    async function loadTasks() {
        try {
            const data = await api('/tasks/team');
            setTasks(data);
        } catch (error) {
            alert('Error loading tasks: ' + error.message);
        } finally {
            setLoading(false);
        }
    }

    async function takeTask(taskId) {
        try {
            await api('/tasks/take', {
                method: 'POST',
                body: JSON.stringify({ task_id: taskId })
            });
            setRefresh(r => r + 1);
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }

    if (loading) return <div>Loading...</div>;

    return (
        <section>
            <h2>Team Overview</h2>
            <Table striped hover>
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Assigned To</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    {tasks.map(task => {
                        const isUnassigned = !task.employee_responsible || task.employee_responsible === 'Unassigned';
                        
                        return (
                            <tr key={task.id}>
                                <td>{task.task}</td>
                                <td>
                                    {task.employee_responsible ? (
                                        task.employee_responsible
                                    ) : (
                                        <Badge bg="secondary">Unassigned</Badge>
                                    )}
                                </td>
                                <td>
                                    {isUnassigned && task.status !== 'Completed' ? (
                                        <Button 
                                            size="sm" 
                                            variant="primary"
                                            onClick={() => takeTask(task.id)}
                                        >
                                            Take Task
                                        </Button>
                                    ) : (
                                        <span className="text-muted">—</span>
                                    )}
                                </td>
                                <td>
                                    <StatusBadge status={task.status} />
                                </td>
                                <td>{task.created_at}</td>
                                <td>{task.updated_at}</td>
                            </tr>
                        );
                    })}
                </tbody>
            </Table>
        </section>
    );
}

function StatusBadge({ status }) {
    const variants = {
        'Pending': 'warning',
        'In Progress': 'info',
        'Completed': 'success'
    };
    
    return (
        <Badge bg={variants[status] || 'secondary'}>
            {status}
        </Badge>
    );
}