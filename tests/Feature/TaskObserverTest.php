<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TaskObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_created_is_logged()
    {
        Log::spy();
        Log::shouldReceive('channel')->with('tasks')->andReturnSelf();

        $task = Task::factory()->create([
            'title' => 'Test Task',
            'priority' => 'medium',
            'status' => 'pending',
        ]);

        Log::shouldHaveReceived('info')
            ->once()
            ->with(
                'Task Created',
                \Mockery::on(fn ($context) =>
                    isset($context['task_id']) &&
                    $context['task_id'] === $task->id &&
                    $context['title'] === 'Test Task'
                )
            );

        $this->assertTrue(true);
    }

    public function test_task_updated_is_logged()
    {
        $task = Task::factory()->create(['status' => 'pending']);

        Log::spy();
        Log::shouldReceive('channel')->with('tasks')->andReturnSelf();

        $task->update(['status' => 'completed']);

        Log::shouldHaveReceived('info')
            ->with(
                'Task Updated',
                \Mockery::on(fn ($context) =>
                    isset($context['changes']) &&
                    is_array($context['changes']) &&
                    array_key_exists('status', $context['changes'])
                )
            )
            ->once();

        $this->assertTrue(true);
    }

    public function test_task_deleted_is_logged()
    {
        Log::spy();
        Log::shouldReceive('channel')->with('tasks')->andReturnSelf();

        $task = Task::factory()->create();

        $task->delete();

        Log::shouldHaveReceived('warning')
            ->once()
            ->with(
                'Task Deleted',
                \Mockery::on(fn ($context) =>
                    isset($context['task_id']) && $context['task_id'] === $task->id
                )
            );

        $this->assertTrue(true);
    }
}