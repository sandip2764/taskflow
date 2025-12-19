<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * List tasks with filters
     */
    public function index(Request $request)
    {
        $query = Task::with('categories')
            ->where('user_id', $request->user()->id);

        if ($request->filled('status')) {
            $query->status($request->status);
        }

        if ($request->filled('priority')) {
            $query->priority($request->priority);
        }

        if ($request->filled('category')) {
            $query->category($request->category);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $allowedSorts = ['created_at', 'due_date', 'priority', 'status'];
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $tasks = $query->paginate($request->get('per_page', 15));

        return new TaskCollection($tasks);
    }

    /**
     * Store task
     */
    public function store(StoreTaskRequest $request)
    {
        $task = $request->user()->tasks()->create(
            $request->validated()
        );

        if ($request->filled('categories')) {
            $task->categories()->sync($request->categories);
        }

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Show task
     */
    public function show(Request $request, $id)
    {
        $task = Task::with('categories')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return new TaskResource($task);
    }

    /**
     * Update task
     */
    public function update(UpdateTaskRequest $request, $id)
    {
        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);

        $task->update($request->validated());

        if ($request->has('categories')) {
            $task->categories()->sync($request->categories ?? []);
        }

        return new TaskResource($task);
    }

    /**
     * Soft delete task
     */
    public function destroy(Request $request, $id)
    {
        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'],200);
    }

    /**
     * Statistics
     */
    public function statistics(Request $request)
    {
        $userId = $request->user()->id;

        return response()->json([
            'total_tasks'   => Task::where('user_id', $userId)->count(),
            'by_status'    => [
                'pending'     => Task::where('user_id', $userId)->status('pending')->count(),
                'in_progress' => Task::where('user_id', $userId)->status('in_progress')->count(),
                'completed'   => Task::where('user_id', $userId)->status('completed')->count(),
            ],
            'overdue_tasks'=> Task::where('user_id', $userId)->overdue()->count(),
            'due_this_week'=> Task::where('user_id', $userId)->dueThisWeek()->count(),
        ],200);
    }

    /**
     * Restore task
     */
    public function restore(Request $request, $id)
    {
        $task = Task::withTrashed()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        $task->restore();

        return (new TaskResource($task))
            ->additional(['message' => 'Task restored successfully']);
    }
}
