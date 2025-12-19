<?php

namespace App\Observers;

use App\Models\Task;
use Illuminate\Support\Facades\Log;

class TaskObserver
{
    public function created(Task $task): void
    {
        Log::channel('tasks')->info('Task Created', [
            'task_id' => $task->id,
            'user_id' => $task->user_id,
            'title' => $task->title,
            'priority' => $task->priority,
            'status' => $task->status,
            'created_at' => $task->created_at->toDateTimeString(),
        ]);
    }

    public function updated(Task $task): void
    {
        Log::channel('tasks')->info('Task Updated', [
            'task_id' => $task->id,
            'user_id' => $task->user_id,
            'title' => $task->title,
            'changes' => $task->getChanges(),
            'updated_at' => $task->updated_at->toDateTimeString(),
        ]);
    }

    public function deleted(Task $task): void
    {
        Log::channel('tasks')->warning('Task Deleted', [
            'task_id' => $task->id,
            'user_id' => $task->user_id,
            'title' => $task->title,
            'deleted_at' => now()->toDateTimeString(),
        ]);
    }

    public function restored(Task $task): void
    {
        Log::channel('tasks')->info('Task Restored', [
            'task_id' => $task->id,
            'user_id' => $task->user_id,
            'title' => $task->title,
            'restored_at' => now()->toDateTimeString(),
        ]);
    }

    public function forceDeleted(Task $task): void
    {
        Log::channel('tasks')->critical('Task Permanently Deleted', [
            'task_id' => $task->id,
            'user_id' => $task->user_id,
            'title' => $task->title,
            'force_deleted_at' => now()->toDateTimeString(),
        ]);
    }
}
