import React, { useState, useEffect } from 'react';
import { Button, Modal, Form, Alert } from 'react-bootstrap';
import api from '../../api/client';
import DataTable from '../../components/DataTable';
import PageLoader from '../../components/PageLoader';
import ConfirmModal from '../../components/ConfirmModal';

export default function UserManagement() {
    const [users, setUsers] = useState([]);
    const [showAdd, setShowAdd] = useState(false);
    const [showEdit, setShowEdit] = useState(false);
    const [editingUser, setEditingUser] = useState(null);
    const [editForm, setEditForm] = useState({
        username: '',
        password: '',
        role: 'admin'
    });
    const [refresh, setRefresh] = useState(0);
    const [showDelete, setShowDelete] = useState(false);
    const [userToDelete, setUserToDelete] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [showCascade, setShowCascade] = useState(false);
    const [cascadeInfo, setCascadeInfo] = useState(null);
    const [cascadeLoading, setCascadeLoading] = useState(false);

    useEffect(() => {
        loadData();
    }, [refresh]);

    async function loadData() {
        setLoading(true);
        try {
            const usersData = await api('/users');
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
        await api('/users', {
            method: 'POST',
            body: JSON.stringify({
                username: formData.get('username'),
                password: formData.get('password'),
                role: formData.get('role')
            })
        });
        setShowAdd(false);
        setRefresh(r => r + 1);
    }

    async function handleEdit(e) {
        e.preventDefault();
        setError('');
        
        const newRole = editForm.role;
        const oldRole = editingUser.role;
        
        if (newRole !== oldRole) {
            try {
                const info = await api(`/users/tasks?username=${encodeURIComponent(editingUser.username)}`);
                if (info.ongoing_count > 0) {
                    setCascadeInfo(info);
                    setShowCascade(true);
                    return;
                }
            } catch (err) {
                setError(err.message);
                return;
            }
        }
        
        await doUpdate({
            user_id: editingUser.id,
            username: editForm.username,
            password: editForm.password,
            role: newRole
        });
    }

    async function doUpdate(data) {
        try {
            await api(`/users`, {
                method: 'PATCH',
                body: JSON.stringify(data)
            });
            closeEdit();
            setRefresh(r => r + 1);
        } catch (err) {
            setError(err.message);
        }
    }

    async function handleCascadeAction(action) {
        if (!cascadeInfo || !editingUser) return;
        
        setCascadeLoading(true);
        try {
            await api('/users/reassign-tasks', {
                method: 'POST',
                body: JSON.stringify({
                    username: cascadeInfo.username,
                    action: action,
                    new_role: editForm.role
                })
            });
            
            await doUpdate({
                user_id: editingUser.id,
                username: editForm.username,
                password: editForm.password,
                role: editForm.role
            });
            
            setShowCascade(false);
            setCascadeInfo(null);
        } catch (err) {
            setError(err.message);
        } finally {
            setCascadeLoading(false);
        }
    }

    function handleDelete(userID) {
        setUserToDelete(userID);
        setShowDelete(true);
    }

    async function confirmDelete() {
        if (!userToDelete) return;
        await api(`/users`, {
            method: 'DELETE',
            body: JSON.stringify({ user_id: userToDelete })
        });
        setShowDelete(false);
        setUserToDelete(null);
        setRefresh(r => r + 1);
    }

    function openEdit(user) {
        setEditingUser(user);
        setError('');
        setEditForm({
            username: user.username || '',
            password: user.password || '',
            role: user.role || 'admin'
        });
        setShowEdit(true);
    }

    function closeEdit() {
        setShowEdit(false);
        setError('');
        setEditingUser(null);
        setEditForm({
            username: '',
            password: '',
            role: 'admin'
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
                <h2 className="h4 mb-0">User Management</h2>
                <Button onClick={() => setShowAdd(true)}>Add User</Button>
            </div>

            <Modal show={showAdd} onHide={() => setShowAdd(false)} backdrop="static">
                <Modal.Header closeButton>
                    <Modal.Title>Add User</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <Form onSubmit={handleCreate}>
                        <Form.Group className="mb-3">
                            <Form.Label>Username</Form.Label>
                            <Form.Control name="username" placeholder="Enter username" required />
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Password</Form.Label>
                            <Form.Control name="password" placeholder="Enter password" required />
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Role</Form.Label>
                            <Form.Select name="role" required>
                                <option value="admin">Admin</option>
                                <option value="developers">Developer</option>
                                <option value="hr">HR</option>
                                <option value="accounting">Accounting</option>
                                <option value="user">User</option>
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
                    <Modal.Title>Edit User</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    {error && (
                        <Alert variant="danger" className="d-flex align-items-center">
                            <span className="me-2">⚠️</span>
                            <div>{error}</div>
                        </Alert>
                    )}
                    <Form onSubmit={handleEdit}>
                        <input type="hidden" name="user_id" value={editingUser?.id || ''} />
                        <Form.Group className="mb-3">
                            <Form.Label>Username</Form.Label>
                            <Form.Control
                                value={editForm.username}
                                onChange={e => handleFormChange('username', e.target.value)}
                                required
                            />
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Password</Form.Label>
                            <Form.Control
                                value={editForm.password}
                                onChange={e => handleFormChange('password', e.target.value)}
                                required
                            />
                        </Form.Group>
                        <Form.Group className="mb-3">
                            <Form.Label>Role</Form.Label>
                            <Form.Select 
                                value={editForm.role}
                                onChange={e => handleFormChange('role', e.target.value)}
                            >
                                <option value="Admin">Admin</option>
                                <option value="Developers">Developer</option>
                                <option value="HR">HR</option>
                                <option value="Accounting">Accounting</option>
                                <option value="User">User</option>
                            </Form.Select>                        
                        </Form.Group>
                        <div className="d-flex gap-2 justify-content-end">
                            <Button variant="secondary" onClick={closeEdit}>Cancel</Button>
                            <Button type="submit" variant="primary">Update</Button>
                        </div>
                    </Form>
                </Modal.Body>
            </Modal>

            <Modal show={showCascade} onHide={() => { setShowCascade(false); setCascadeInfo(null); }} backdrop="static" centered>
                <Modal.Header className="bg-warning text-dark">
                    <Modal.Title>⚠️ Role Change Warning</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <p>
                        <strong>{cascadeInfo?.username}</strong> has <strong>{cascadeInfo?.ongoing_count}</strong> ongoing task(s).
                    </p>
                    <p className="text-muted">
                        Changing their role from <strong>{editingUser?.role}</strong> to <strong>{editForm?.role}</strong> will leave these tasks mismatched.
                    </p>
                    <p className="mb-0">Choose how to handle the ongoing tasks:</p>
                </Modal.Body>
                <Modal.Footer className="d-flex flex-column gap-2">
                    <Button 
                        variant="primary" 
                        className="w-100"
                        disabled={cascadeLoading}
                        onClick={() => handleCascadeAction('change_role')}
                    >
                        {cascadeLoading ? 'Processing...' : `📋 Change task roles to "${editForm?.role}" and keep assigned`}
                    </Button>
                    <Button 
                        variant="outline-danger" 
                        className="w-100"
                        disabled={cascadeLoading}
                        onClick={() => handleCascadeAction('unassign')}
                    >
                        {cascadeLoading ? 'Processing...' : '🔓 Unassign from all tasks and set to Pending'}
                    </Button>
                    <Button 
                        variant="link" 
                        className="w-100 text-muted"
                        disabled={cascadeLoading}
                        onClick={() => { setShowCascade(false); setCascadeInfo(null); }}
                    >
                        Cancel — don't change role
                    </Button>
                </Modal.Footer>
            </Modal>

            <ConfirmModal 
                show={showDelete}
                onHide={() => { setShowDelete(false); setUserToDelete(null); }}
                onConfirm={confirmDelete}
                title="Delete User"
                body={<>Are you sure you want to delete this user?<br /><strong>This action cannot be undone.</strong></>}
            />

            <DataTable id="userManagementTable" refreshKey={refresh}>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th style={{ width: '120px' }}>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {users.map(user => (
                        <tr key={user.id}>
                            <td className="fw-medium">{user.username}</td>
                            <td><span className="badge bg-secondary text-capitalize">{user.role}</span></td>
                            <td>
                                <Button size="sm" variant="warning" onClick={() => openEdit(user)}>Edit</Button>{' '}
                                <Button size="sm" variant="danger" onClick={() => handleDelete(user.id)}>Delete</Button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </DataTable>
        </section>
    );
}