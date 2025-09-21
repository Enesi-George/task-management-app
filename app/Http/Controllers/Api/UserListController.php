<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserListResource;
use App\Models\UserList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserListController extends Controller
{
    public function index()
    {
        $lists = UserList::with(['task', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return UserListResource::collection($lists);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['user_id'] = Auth::id();

        $userList = UserList::create($validated);
        
        return new UserListResource($userList->load(['task', 'user']));
    }

    public function show(UserList $userList)
    {
        if ($userList->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this list.');
        }

        return new UserListResource($userList->load(['task', 'user']));
    }

    public function update(Request $request, UserList $userList)
    {
        logger('userList: ' . $userList);
        logger('auth id: ' . Auth::id());
        if ($userList->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this list.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $userList->update($validated);

        return new UserListResource($userList->load(['task', 'user']));
    }

    public function destroy(UserList $userList)
    {
        if ($userList->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this list.');
        }

        $userList->delete();

        return response()->json(['message' => 'List deleted successfully']);
    }
}