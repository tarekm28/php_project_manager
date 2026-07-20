import React, { useState, useEffect } from 'react';
import { Button, Badge } from 'react-bootstrap';
import api from '../../api/client';
import DataTable from '../../components/DataTable';
import PageLoader from '../../components/PageLoader';
import StatusBadge from '../../components/StatusBadge';

export default function TeamTasks() {
    const [tasks, setTasks] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refresh, setRefresh] = useState(0);

    useEffect(() => {
        loadTasks();
    }, [refresh]);

    async function loadTasks() {
        setLoading(true);
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

    if (loading) return <PageLoader />;

    return (
        <section>
            <h2 className="h4 mb-3">Team Overview</h2>
            <DataTable id="teamTasksTable" refreshKey={refresh}>
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
                                <td className="fw-medium">{task.task}</td>
                                <td>
                                    {task.employee_responsible ? task.employee_responsible : <Badge bg="secondary">Unassigned</Badge>}
                                </td>
                                <td>
                                    {isUnassigned && task.status !== 'Completed' ? (
                                        <Button size="sm" variant="primary" onClick={() => takeTask(task.id)}>Take Task</Button>
                                    ) : (
                                        <span className="text-muted">—</span>
                                    )}
                                </td>
                                <td><StatusBadge status={task.status} /></td>
                                <td>{task.created_at}</td>
                                <td>{task.updated_at}</td>
                            </tr>
                        );
                    })}
                </tbody>
            </DataTable>
        </section>
    );
}
