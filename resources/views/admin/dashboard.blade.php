@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    Admin Dashboard
</h2>
@endsection

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-medium">Manage Tasks</h3>
            <button onclick="openCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Create New Task
            </button>
        </div>

        <!-- Loading State -->
        <div id="loading-container" class="text-center py-8 hidden">
            <p class="text-gray-500">Loading tasks...</p>
        </div>

        <!-- Tasks List -->
        <div id="tasks-container">
            <!-- Tasks will be loaded here -->
        </div>
    </div>
</div>

<!-- Create Task Modal -->
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Create New Task</h3>
            <form id="createTaskForm">
                @csrf
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" id="title" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCreateModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Task</h3>
            <form id="editTaskForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editTaskId" name="id">
                <div class="mb-4">
                    <label for="editTitle" class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" id="editTitle" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="editDescription" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="editDescription" name="description" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Update Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Admin Dashboard JavaScript - FIXED VERSION
let tasks = [];

// Load tasks when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadTasks();
});

// Load all tasks via API
async function loadTasks() {
    const loadingContainer = document.getElementById('loading-container');
    const tasksContainer = document.getElementById('tasks-container');
    
    try {
        // Show loading state
        loadingContainer.classList.remove('hidden');
        tasksContainer.innerHTML = '';
        
        // Fetch from API endpoint, not web route
        const response = await fetch('/api/tasks', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            tasks = data.data || data; // Handle both resource collection and direct array
            displayTasks(tasks);
        } else {
            console.error('Error loading tasks:', response.statusText);
            showNotification('Error loading tasks', 'error');
            displayTasks([]); // Show empty state
        }
    } catch (error) {
        console.error('Error loading tasks:', error);
        showNotification('Error loading tasks', 'error');
        displayTasks([]); // Show empty state
    } finally {
        // Hide loading state
        loadingContainer.classList.add('hidden');
    }
}

// Display tasks
function displayTasks(tasksData) {
    const container = document.getElementById('tasks-container');
    
    if (!tasksData || tasksData.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <p class="text-gray-500">No tasks found. Create your first task!</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = tasksData.map(task => `
        <div class="bg-gray-50 p-4 rounded-lg mb-4" data-task-id="${task.id}">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h4 class="text-lg font-medium text-gray-900">${escapeHtml(task.title)}</h4>
                    <p class="text-gray-600 mt-2">${escapeHtml(task.description)}</p>
                    <div class="mt-2 text-sm text-gray-500">
                        <p>Created by: ${task.created_by ? escapeHtml(task.created_by.name) : 'Unknown'}</p>
                        <p>Created: ${new Date(task.created_at).toLocaleDateString()}</p>
                        <p>Updated: ${new Date(task.updated_at).toLocaleDateString()}</p>
                    </div>
                </div>
                <div class="flex space-x-2 ml-4">
                    <button onclick="editTask(${task.id})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                        Edit
                    </button>
                    <button onclick="deleteTask(${task.id})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text ? text.toString().replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
}

// Modal functions
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.getElementById('createTaskForm').reset();
}

function openEditModal() {
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editTaskForm').reset();
}

// Create task - Using web route for form submission
document.getElementById('createTaskForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('/tasks', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            closeCreateModal();
            showNotification('Task created successfully!', 'success');
            loadTasks(); // Reload tasks from API
        } else {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            showNotification('Error creating task', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error creating task', 'error');
    }
});

// Edit task
function editTask(taskId) {
    const task = tasks.find(t => t.id === taskId);
    if (task) {
        document.getElementById('editTaskId').value = task.id;
        document.getElementById('editTitle').value = task.title;
        document.getElementById('editDescription').value = task.description;
        openEditModal();
    }
}

// Update task - Using web route for form submission
document.getElementById('editTaskForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const taskId = document.getElementById('editTaskId').value;
    const formData = new FormData(this);
    
    try {
        const response = await fetch(`/tasks/${taskId}`, {
            method: 'POST', // Using POST with method spoofing
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            closeEditModal();
            showNotification('Task updated successfully!', 'success');
            loadTasks(); // Reload tasks from API
        } else {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            showNotification('Error updating task', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error updating task', 'error');
    }
});

// Delete task - Using web route
async function deleteTask(taskId) {
    if (confirm('Are you sure you want to delete this task?')) {
        try {
            const response = await fetch(`/tasks/${taskId}`, {
                method: 'POST', 
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    _method: 'DELETE'
                })
            });
            
            if (response.ok) {
                showNotification('Task deleted successfully!', 'success');
                loadTasks(); // Reload tasks from API
            } else {
                const errorText = await response.text();
                console.error('Error response:', errorText);
                showNotification('Error deleting task', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error deleting task', 'error');
        }
    }
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md text-white z-50 transition-opacity duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Fade out and remove
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
</script>
@endsection