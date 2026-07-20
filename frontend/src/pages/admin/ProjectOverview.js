import React, { useState, useEffect } from 'react';
import api from '../../api/client';
import DataTable from '../../components/DataTable';
import PageLoader from '../../components/PageLoader';
import StatusBadge from '../../components/StatusBadge';

export default function ProjectOverview() {
    const [tasks, setTasks] = useState([]);
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refresh, setRefresh] = useState(0);

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
            setUsers(usersData);
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    }

    if (loading) return <PageLoader />;

    return (
        <section>
            <h2 className="h4 mb-3">Project Overview</h2>
            <DataTable id="projectOverviewTable" refreshKey={refresh}>
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Assigned to</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
                    {tasks.map(task => (
                        <tr key={task.id}>
                            <td className="fw-medium">{task.task}</td>
                            <td>{task.employee_responsible || task.assigned_to || 'Unassigned'}</td>
                            <td><StatusBadge status={task.status} /></td>
                            <td>{task.created_at}</td>
                            <td>{task.updated_at}</td>
                        </tr>
                    ))}
                </tbody>
            </DataTable>
        </section>
    );
}