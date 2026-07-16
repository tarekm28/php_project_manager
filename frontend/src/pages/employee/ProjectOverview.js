import React, { useState, useEffect } from 'react';
import {Table } from 'react-bootstrap';
import api from '../../api/client';


export default function ProjectOverview() {
    const [tasks, setTasks] = useState([]);
    const [users, setUsers] = useState([]);
    const [refresh, setRefresh] = useState(0);


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
return(
    <Table striped>
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
                            <td>{task.task}</td>
                            <td>{task.employee_responsible || task.assigned_to || 'Unassigned'}</td>
                            <td>{task.status}</td>
                            <td>{task.created_at}</td>
                            <td>{task.updated_at}</td>
                        </tr>
                    ))}
                </tbody>
            </Table>
);
}