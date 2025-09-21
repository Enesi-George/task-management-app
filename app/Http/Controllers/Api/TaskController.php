<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('creator')->orderBy('created_at', 'desc')->get();
        return TaskResource::collection($tasks);
    }

    public function show(Task $task)
    {
        return new TaskResource($task->load('creator'));
    }
}