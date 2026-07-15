let currentPage = 'project_overview';

document.addEventListener('DOMContentLoaded', async function() {
    const user = await checkAuth();
    if (!user) return;

    renderApp();
});

function renderApp() {
    const app = document.getElementById('app');
    const user = getUser();

    if (isAdmin()) {
        app.innerHTML = renderAdminLayout();
    } else {
        app.innerHTML = renderEmployeeLayout();
    }

    loadPageContent(currentPage);
}


function renderAdminLayout() {
    const isActive = (page) => currentPage === page ? ' active' : '';
    const isCurrent = (page) => currentPage === page ? ' aria-current="page"' : '';

    return `
    <nav class="navbar navbar-expand bg-body-tertiary px-3">
        <div class="container-fluid p-0">
            <ul class="navbar-nav d-flex flex-row gap-3">
                <li class="nav-item">
                    <a class="nav-link${isActive('project_overview')}" href="#" onclick="navigateTo('project_overview'); return false;"${isCurrent('project_overview')}>
                        Project Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link${isActive('task_management')}" href="#" onclick="navigateTo('task_management'); return false;"${isCurrent('task_management')}>
                        Task Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link${isActive('user_management')}" href="#" onclick="navigateTo('user_management'); return false;"${isCurrent('user_management')}>
                        User Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link${isActive('activity_logs')}" href="#" onclick="navigateTo('activity_logs'); return false;"${isCurrent('activity_logs')}>
                        Activity Logs
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container py-4">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Project Manager Dashboard</h1>
            <button onclick="logout()" class="btn btn-outline-secondary">Logout</button>
        </header>
        <div id="page-content"></div>
    </div>`;
}

function renderEmployeeLayout() {
    const isActive = (page) => currentPage === page ? ' active' : '';

    return `
    <header>
        <h1>Employee Dashboard</h1>
        <div style="position:absolute; top:16px; right:16px;">
            <button onclick="logout()" class="btn btn-outline-secondary">Logout</button>
        </div>
    </header>
    <nav class="navbar navbar-expand bg-body-tertiary px-3">
        <ul class="navbar-nav d-flex flex-row gap-3">
            <li class="nav-item">
                <a class="nav-link${isActive('project_overview')}" href="#" onclick="navigateTo('project_overview'); return false;">Project Overview</a>
            </li>
            <li class="nav-item">
                <a class="nav-link${isActive('team_tasks')}" href="#" onclick="navigateTo('team_tasks'); return false;">Team Tasks</a>
            </li>
            <li class="nav-item">
                <a class="nav-link${isActive('current_tasks')}" href="#" onclick="navigateTo('current_tasks'); return false;">Current Tasks</a>
            </li>
        </ul>
    </nav>

    <div class="container py-4">
        <div id="page-content"></div>
    </div>`;
}

function navigateTo(page) {
    currentPage = page;
    renderApp();
}

async function loadPageContent(page) {
    const container = document.getElementById('page-content');
    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border" role="status"></div></div>';

    try {
        switch (page) {
            case 'project_overview':
                await loadProjectOverview(container);
                break;
            case 'task_management':
                await loadTaskManagement(container);
                break;
            case 'user_management':
                await loadUserManagement(container);
                break;
            case 'team_tasks':
                await loadTeamTasks(container);
                break;
            case 'current_tasks':
                await loadCurrentTasks(container);
                break;
            case 'activity_logs':
                await loadActivityLogs(container);
                break;
            default:
                container.innerHTML = '<p>Page not found</p>';
        }
    } catch (error) {
        container.innerHTML = `<div class="alert alert-danger">Error loading page: ${error.message}</div>`;
    }
}

async function loadProjectOverview(container) {
    const tasks = await api('/tasks/all');

    container.innerHTML = `
    <section id="project-overview">
        <h2>Project Overview</h2>
        <div id="project-list">
            <table id="projectOverviewTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Assigned to</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    ${tasks.map(task => {
                        const assigned = task.employee_responsible || task.assigned_to || 'Unassigned';
                        return `
                        <tr>
                            <td>${escapeHtml(task.task || '')}</td>
                            <td>${escapeHtml(assigned)}</td>
                            <td>${escapeHtml(task.status || 'Pending')}</td>
                            <td>${escapeHtml(task.created_at || '')}</td>
                            <td>${escapeHtml(task.updated_at || '')}</td>
                        </tr>`;
                    }).join('')}
                </tbody>
            </table>
        </div>
    </section>`;

    initDataTable('#projectOverviewTable');
}

async function loadTaskManagement(container) {
    const [tasks, users] = await Promise.all([
        api('/tasks'),
        api('/users')
    ]);

    // Build options once, reuse in both modals if needed
    const userOptions = users.map(user => 
        `<option value="${escapeHtml(user.username)}">${escapeHtml(user.username)} (${escapeHtml(user.role)})</option>`
    ).join('');
    
    const allUserOptions = `<option value="">Unassigned</option>` + userOptions;

    container.innerHTML = `
    <section id="task-management">
        <h2>Task Management</h2>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            Add Task
        </button>

        <!-- Add Task Modal -->
        <div class="modal fade" id="addTaskModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="add-task-form">
                            <div class="mb-3">
                                <input type="text" name="task" class="form-control" placeholder="Enter task" required>
                            </div>
                            <div class="mb-3">
                                <select name="role" class="form-select" required>
                                    <option value="admin">Admin</option>
                                    <option value="developers">Developer</option>
                                    <option value="hr">HR</option>
                                    <option value="accounting">Accounting</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Create Task</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Task Modal -->
        <div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-task-form">
                            <input type="hidden" name="task_id" id="edit-task-id">
                            <div class="mb-3">
                                <label>Task Name</label>
                                <input type="text" name="task" id="edit-task-name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Status</label>
                                <select name="status" id="edit-task-status" class="form-select">
                                    <option value="Pending">Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Assigned To (Role)</label>
                                <select name="assigned_to" id="edit-task-assigned" class="form-select">
                                    <option value="admin">Admin</option>
                                    <option value="developers">Developer</option>
                                    <option value="hr">HR</option>
                                    <option value="accounting">Accounting</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Employee Responsible</label>
                                <select name="employee_responsible" id="edit-task-employee" class="form-select">
                                    ${allUserOptions}
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Update Task</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="task-list" class="mt-3">
            <table id="taskManagementTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Assigned to</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${tasks.map(task => `
                    <tr>
                        <td>${escapeHtml(task.task || '')}</td>
                        <td>${escapeHtml(task.assigned_to || task.employee_responsible || 'Unassigned')}</td>
                        <td>${escapeHtml(task.status || 'Pending')}</td>
                        <td>${escapeHtml(task.created_at || '')}</td>
                        <td>${escapeHtml(task.updated_at || '')}</td>
                        <td>
                            <button onclick="openEditModal(${task.id})" class="btn btn-sm btn-warning">Edit</button>
                            <button onclick="deleteTask(${task.id})" class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>`).join('')}
                </tbody>
            </table>
        </div>
    </section>`;

    initDataTable('#taskManagementTable');

    document.getElementById('add-task-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        try {
            await apiForm('/tasks', formData);
            bootstrap.Modal.getInstance(document.getElementById('addTaskModal')).hide();
            this.reset();
            loadPageContent('task_management');
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });

    document.getElementById('edit-task-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        try {
            await api('/tasks', {
                method: 'PATCH',
                body: JSON.stringify(data)
            });
            bootstrap.Modal.getInstance(document.getElementById('editTaskModal')).hide();
            loadPageContent('task_management');
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });
}
async function openEditModal(taskId) {
    const tasks = await api('/tasks');
    const task = tasks.find(t => t.id === taskId);
    
    if (!task) {
        alert('Task not found');
        return;
    }
    
    document.getElementById('edit-task-id').value = task.id;
    document.getElementById('edit-task-name').value = task.task || '';
    document.getElementById('edit-task-status').value = task.status || 'Pending';
    document.getElementById('edit-task-assigned').value = task.assigned_to || 'admin';
    document.getElementById('edit-task-employee').value = task.employee_responsible || '';
    
    const modal = new bootstrap.Modal(document.getElementById('editTaskModal'));
    modal.show();
}

async function deleteTask(taskId) {
    if (!confirm('Are you sure you want to delete this task?')) return;
    
    try {
        await api('/tasks', {
            method: 'DELETE',
            body: JSON.stringify({ task_id: taskId })
        });
        loadPageContent('task_management');
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

async function loadUserManagement(container) {
    const users = await api('/users');

    container.innerHTML = `
    <section id="user-management">
        <h2>User Management</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            Add User
        </button>

        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="add-user-form">
                            <div class="mb-3">
                                <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                            </div>
                            <div class="mb-3">
                                <select name="role" class="form-select" required>
                                    <option value="admin">Admin</option>
                                    <option value="developers">Developer</option>
                                    <option value="hr">HR</option>
                                    <option value="accounting">Accounting</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Create User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-user-form">
                            <div class="mb-3">
                                <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Enter password">
                            </div>
                            <div class="mb-3">
                                <select name="role" class="form-select" required>
                                    <option value="admin">Admin</option>
                                    <option value="developers">Developer</option>
                                    <option value="hr">HR</option>
                                    <option value="accounting">Accounting</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Update User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="user-list">
            <table id="userManagementTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    ${users.map(user => `
                    <tr>
                        <td>${escapeHtml(user.username)}</td>
                        <td>${escapeHtml(user.role)}</td>
                        <td>
                            <button onclick="openEditUserModal(${user.id})" class="btn btn-sm btn-warning">Edit</button>
                            <button onclick="deleteUser(${user.id})" class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>`).join('')}
                </tbody>
            </table>
        </div>
    </section>`;

    initDataTable('#userManagementTable');

    document.getElementById('add-user-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        try {
            await apiForm('/users', formData);
            const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
            modal.hide();
            loadPageContent('user_management');
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });

    document.getElementById('edit-user-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const userId = Number(form.dataset.userId);
        const username = form.querySelector('input[name="username"]').value.trim();
        const role = form.querySelector('select[name="role"]').value;
        const password = form.querySelector('input[name="password"]').value;
        const payload = { user_id: userId };

        if (username) payload.username = username;
        if (role) payload.role = role;
        if (password) payload.password = password;

        try {
            await api('/users', {
                method: 'PATCH',
                body: JSON.stringify(payload)
            });
            const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
            modal.hide();
            loadPageContent('user_management');
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });
}

async function openEditUserModal(userId) {
    const users = await api('/users');
    const user = users.find(u => u.id === userId);
    if (!user) {
        alert('User not found');
        return;
    }

    const form = document.getElementById('edit-user-form');
    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));

    form.dataset.userId = userId;
    form.querySelector('input[name="username"]').value = user.username || '';
    form.querySelector('input[name="password"]').value = '';
    form.querySelector('select[name="role"]').value = user.role || 'user';

    modal.show();
}

async function deleteUser(userId) {
    if (!confirm('Are you sure?')) return;
    try {
        await api('/users', {
            method: 'DELETE',
            body: JSON.stringify({ user_id: userId })
        });
        loadPageContent('user_management');
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

async function loadActivityLogs(container) {
    const page = parseInt(new URLSearchParams(window.location.search).get('log_page')) || 1;
    const response = await api(`/logs?page=${page}`);
    const { logs, page: currentPage, per_page, total, total_pages } = response;

    container.innerHTML = `
    <section id="activity-logs">
        <h2>Activity Logs</h2>
        <p class="text-muted">Total: ${total} entries</p>
        
        <table id="logsTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Entity</th>
                    <th>ID</th>
                    <th>Changes</th>
                </tr>
            </thead>
            <tbody>
                ${logs.map(log => {
                    const oldVals = log.old_values ? JSON.parse(log.old_values) : null;
                    const newVals = log.new_values ? JSON.parse(log.new_values) : null;
                    
                    let changesHtml = '';
                    if (oldVals && newVals) {
                        const changedFields = Object.keys(newVals).filter(k => oldVals[k] !== newVals[k]);
                        changesHtml = changedFields.map(field => 
                            `<div><strong>${field}:</strong> ${escapeHtml(String(oldVals[field] ?? 'null'))} → ${escapeHtml(String(newVals[field]))}</div>`
                        ).join('');
                    } else if (newVals) {
                        changesHtml = '<span class="text-success">Created</span>';
                    } else if (oldVals) {
                        changesHtml = '<span class="text-danger">Deleted</span>';
                    }
                    
                    const badgeColor = {
                        'create': 'success',
                        'update': 'primary',
                        'delete': 'danger',
                        'take': 'info',
                        'complete': 'warning'
                    }[log.action] || 'secondary';
                    
                    return `
                    <tr>
                        <td>${escapeHtml(log.created_at)}</td>
                        <td>${escapeHtml(log.username)}</td>
                        <td><span class="badge bg-${badgeColor}">${escapeHtml(log.action)}</span></td>
                        <td>${escapeHtml(log.entity_type)}</td>
                        <td>${log.entity_id}</td>
                        <td>${changesHtml}</td>
                    </tr>`;
                }).join('')}
            </tbody>
        </table>
        
        <nav>
            <ul class="pagination">
                ${currentPage > 1 ? `<li class="page-item"><a class="page-link" href="#" onclick="navigateTo('activity_logs'); window.history.pushState({}, '', '?log_page=${currentPage - 1}'); return false;">Previous</a></li>` : ''}
                <li class="page-item active"><span class="page-link">Page ${currentPage} of ${total_pages}</span></li>
                ${currentPage < total_pages ? `<li class="page-item"><a class="page-link" href="#" onclick="navigateTo('activity_logs'); window.history.pushState({}, '', '?log_page=${currentPage + 1}'); return false;">Next</a></li>` : ''}
            </ul>
        </nav>
    </section>`;

    initDataTable('#logsTable');
}

async function loadTeamTasks(container) {
    const tasks = await api('/tasks/team');

    container.innerHTML = `
    <section id="team-overview">
        <h2>Team Overview</h2>
        <table id="teamTasksTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Assigned To</th>
                    <th>Action</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                ${tasks.map(row => {
                    const assigned = row.employee_responsible || 'Unassigned';
                    const canTake = assigned === 'Unassigned';
                    return `
                    <tr>
                        <td>${escapeHtml(row.task || '')}</td>
                        <td>${escapeHtml(assigned)}</td>
                        <td>
                            ${canTake ? `<button onclick="takeTask(${row.id}, this)" class="btn btn-sm btn-primary">Take Task</button>` : ''}
                        </td>
                        <td>${escapeHtml(row.status || '')}</td>
                        <td>${escapeHtml(row.created_at || '')}</td>
                        <td>${escapeHtml(row.updated_at || '')}</td>
                    </tr>`;
                }).join('')}
            </tbody>
        </table>
    </section>`;

    initDataTable('#teamTasksTable');
}

async function takeTask(taskId, button) {
    button.disabled = true;
    button.textContent = 'Taking...';
    try {
        await api('/tasks/take', {
            method: 'POST',
            body: JSON.stringify({ task_id: taskId })
        });
        loadPageContent('team_tasks');
    } catch (error) {
        alert('Error: ' + error.message);
        button.disabled = false;
        button.textContent = 'Take Task';
    }
}

async function loadCurrentTasks(container) {
    const tasks = await api('/tasks/mine');

    container.innerHTML = `
    <section id="task-management">
        <h2>Task Undertaking</h2>
        <table id="currentTasksTable" class="table table-striped">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                ${tasks.map(row => `
                <tr>
                    <td>${escapeHtml(row.task || '')}</td>
                    <td>${escapeHtml(row.status || '')}</td>
                    <td>
                        <button onclick="completeTask(${row.id}, this)" class="btn btn-sm btn-success">Mark as Completed</button>
                    </td>
                </tr>`).join('')}
            </tbody>
        </table>
    </section>`;

    initDataTable('#currentTasksTable');
}

async function completeTask(taskId, button) {
    button.disabled = true;
    button.textContent = 'Updating...';
    try {
        await api('/tasks/complete', {
            method: 'POST',
            body: JSON.stringify({ task_id: taskId })
        });

        const row = button.closest('tr');
        row.querySelector('td:nth-child(2)').textContent = 'Completed';
        button.parentElement.innerHTML = '<span class="text-muted">Done</span>';
    } catch (error) {
        alert('Error: ' + error.message);
        button.disabled = false;
        button.textContent = 'Mark as Completed';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function initDataTable(selector) {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $(selector).DataTable();
    }
}