<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('creator')->orderBy('created_at', 'desc')->get();
        
        return view('admin.dashboard', compact('tasks'));
    }

 
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        $validated['created_by'] = Auth::id();

        Task::create($validated);


        return redirect()->route('dashboard')->with('success', 'Task created successfully.');
    }


    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        $task->update($validated);

        return redirect()->route('dashboard')->with('success', 'Task updated successfully.');
    }

       public function destroy(Task $task)
    {
        $task->delete();
        
        return redirect()->route('dashboard')->with('success', 'Task deleted successfully.');
    }
}
