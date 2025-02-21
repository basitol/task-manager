<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); // Default 10 items per page
        $sortBy = $request->query('sort_by', 'created_at'); // Default sort by creation date
        $sortOrder = $request->query('sort_order', 'desc'); // Default newest first
        $status = $request->query('status'); // Optional status filter

        $query = Task::where('user_id', Auth::id());

        // Apply status filter if provided
        if ($status) {
            $query->where('status', strtolower($status));
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Get paginated results
        $tasks = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'tasks' => $tasks->items(),
                'pagination' => [
                    'current_page' => $tasks->currentPage(),
                    'per_page' => $tasks->perPage(),
                    'total' => $tasks->total(),
                    'last_page' => $tasks->lastPage(),
                    'from' => $tasks->firstItem(),
                    'to' => $tasks->lastItem(),
                ],
            ],
            'message' => 'Tasks retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_name' => 'required|string',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['pending', 'in-progress', 'completed'])],
            'due_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $task = Task::create([
            'task_name' => $request->task_name,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'user_id' => Auth::id(),
        ]);

        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create task'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'data' => $task,
            'message' => 'Task created successfully'
        ], 201);
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to update this task'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'task_name' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in-progress,completed',
            'due_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $task->update($validator->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => $task->fresh(),
            'message' => 'Task updated successfully'
        ]);
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to delete this task'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Task deleted successfully'
        ]);
    }

    public function markAsCompleted(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to update this task'
            ], 403);
        }

        $task->update(['status' => 'completed']);
        
        return response()->json([
            'status' => 'success',
            'data' => $task->fresh(),
            'message' => 'Task marked as completed successfully'
        ]);
    }

    public function show(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to view this task'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $task,
            'message' => 'Task retrieved successfully'
        ]);
    }
}