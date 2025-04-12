<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->tasks()->latest()->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'boolean'
        ]);

        $data['user_id'] = $request->user()->id;

        $task = Task::create($data);

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        // Optional ownership check for safety
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'boolean'
        ]);

        $task->update($data);

        return response()->json($task);
    }
    public function markComplete(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->update(['status' => true]);

        return response()->json(['message' => 'Task marked as completed']);
    }


    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
