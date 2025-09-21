@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    User Dashboard
</h2>
@endsection

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900">
        <div class="mb-6">
            <h3 class="text-lg font-medium mb-4">Available Tasks</h3>
            <div id="tasks-container">
                <!-- Tasks will be loaded here via API -->
            </div>
        </div>
        
        <div class="border-t pt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">My Lists</h3>
                <button onclick="openCreateListModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Create New List
                </button>
            </div>
            <div id="lists-container">
                <!-- User lists will be loaded here via API -->
            </div>
        </div>
    </div>
</div>

<!-- Create List Modal -->
<div id="createListModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Create New List</h3>
            <form id="createListForm">
                <div class="mb-4">
                    <label for="listTaskId" class="block text-sm font-medium text-gray-700">Select Task</label>
                    <select id="listTaskId" name="task_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Choose a task...</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="listTitle" class="block text-sm font-medium text-gray-700">List Title</label>
                    <input type="text" id="listTitle" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="listDescription" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="listDescription" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCreateListModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Create List
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit List Modal -->
<div id="editListModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit List</h3>
            <form id="editListForm">
                <input type="hidden" id="editListId" name="id">
                <div class="mb-4">
                    <label for="editListTitle" class="block text-sm font-medium text-gray-700">List Title</label>
                    <input type="text" id="editListTitle" name="title" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="editListDescription" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="editListDescription" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditListModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Update List
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// User Dashboard JavaScript - All API calls
let tasks = [];
let userLists = [];

// Load data when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadTasks();
    loadUserLists();
});

// Load tasks from API
async function loadTasks() {
    try {
        const response = await fetch('/api/tasks', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            tasks = data.data || data;
            displayTasks();
            populateTaskSelect();
        } else {
            console.error('Error loading tasks:', response.statusText);
            showNotification('Error loading tasks', 'error');
        }
    } catch (error) {
        console.error('Error loading tasks:', error);
        showNotification('Error loading tasks', 'error');
    }
}

// Load user lists from API
async function loadUserLists() {
    try {
        const response = await fetch('/api/user-lists', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            userLists = data.data || data;
            displayUserLists();
        } else {
            console.error('Error loading lists:', response.statusText);
            showNotification('Error loading lists', 'error');
        }
    } catch (error) {
        console.error('Error loading lists:', error);
        showNotification('Error loading lists', 'error');
    }
}

// Display tasks
function displayTasks() {
    const container = document.getElementById('tasks-container');
    
    if (tasks.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <p class="text-gray-500">No tasks available.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = tasks.map(task => `
        <div class="bg-gray-50 p-4 rounded-lg mb-4">
            <h4 class="text-lg font-medium text-gray-900">${task.title}</h4>
            <p class="text-gray-600 mt-2">${task.description}</p>
            <div class="mt-2 text-sm text-gray-500">
                <p>Created by: ${task.created_by.name}</p>
                <p>Created: ${new Date(task.created_at).toLocaleDateString()}</p>
            </div>
        </div>
    `).join('');
}

// Display user lists
function displayUserLists() {
    const container = document.getElementById('lists-container');
    
    if (userLists.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <p class="text-gray-500">No lists created yet. Create your first list!</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = userLists.map(list => `
        <div class="bg-blue-50 p-4 rounded-lg mb-4" data-list-id="${list.id}">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <h5 class="text-md font-medium text-gray-900">${list.title}</h5>
                    <p class="text-gray-600 mt-1">${list.description || 'No description'}</p>
                    <div class="mt-2 text-sm text-gray-500">
                        <p>Task: ${list.task.title}</p>
                        <p>Created: ${new Date(list.created_at).toLocaleDateString()}</p>
                    </div>
                </div>
                <div class="flex space-x-2 ml-4">
                    <button onclick="editList(${list.id})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                        Edit
                    </button>
                    <button onclick="deleteList(${list.id})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Populate task select dropdown
function populateTaskSelect() {
    const select = document.getElementById('listTaskId');
    select.innerHTML = '<option value="">Choose a task...</option>';
    
    tasks.forEach(task => {
        const option = document.createElement('option');
        option.value = task.id;
        option.textContent = task.title;
        select.appendChild(option);
    });
}

// Modal functions
function openCreateListModal() {
    document.getElementById('createListModal').classList.remove('hidden');
}

function closeCreateListModal() {
    document.getElementById('createListModal').classList.add('hidden');
    document.getElementById('createListForm').reset();
}

function openEditListModal() {
    document.getElementById('editListModal').classList.remove('hidden');
}

function closeEditListModal() {
    document.getElementById('editListModal').classList.add('hidden');
    document.getElementById('editListForm').reset();
}

// Create list via API
document.getElementById('createListForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        task_id: document.getElementById('listTaskId').value,
        title: document.getElementById('listTitle').value,
        description: document.getElementById('listDescription').value
    };
    
    try {
        const response = await fetch('/api/user-lists', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        });
        
        if (response.ok) {
            closeCreateListModal();
            showNotification('List created successfully!', 'success');
            loadUserLists(); // Reload lists
        } else {
            const errorData = await response.json();
            showNotification(errorData.message || 'Error creating list', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error creating list', 'error');
    }
});

// Edit list
function editList(listId) {
    const list = userLists.find(l => l.id === listId);

    if (list) {
        document.getElementById('editListId').value = list.id;
        document.getElementById('editListTitle').value = list.title;
        document.getElementById('editListDescription').value = list.description || '';
        openEditListModal();
    }
}

// Update list via API
document.getElementById('editListForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const listId = document.getElementById('editListId').value;
    const formData = {
        title: document.getElementById('editListTitle').value,
        description: document.getElementById('editListDescription').value
    };
    try {
        const response = await fetch(`/api/user-lists/${listId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        });
        
        if (response.ok) {
            closeEditListModal();
            showNotification('List updated successfully!', 'success');
            loadUserLists(); // Reload lists
        } else {
            const errorData = await response.json();
            showNotification(errorData.message || 'Error updating list', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error updating list', 'error');
    }
});

// Delete list via API
async function deleteList(listId) {
    if (confirm('Are you sure you want to delete this list?')) {
        try {
            const response = await fetch(`/api/user-lists/${listId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                showNotification('List deleted successfully!', 'success');
                loadUserLists(); // Reload lists
            } else {
                const errorData = await response.json();
                showNotification(errorData.message || 'Error deleting list', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error deleting list', 'error');
        }
    }
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection