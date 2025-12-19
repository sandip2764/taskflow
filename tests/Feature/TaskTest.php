<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authentication_is_required_for_tasks()
    {
        $this->getJson('/api/tasks')->assertStatus(401);
    }

    /** @test */
    public function user_can_create_task()
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/tasks', [
                'title' => 'Test Task',
                'priority' => 'high',
                'status' => 'pending',
                'categories' => [$category->id],
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Test Task');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function task_validation_works()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/tasks', [
                'priority' => 'invalid',
                'status' => 'invalid',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'priority', 'status']);
    }

    /** @test */
    public function user_can_see_only_own_tasks()
    {
        Task::factory()->create(['user_id' => $this->user->id]);
        Task::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function filtering_by_status_works()
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/tasks?status=pending');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.status', 'pending');
    }

    /** @test */
    public function user_can_update_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Updated',
                'status' => 'completed',
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function user_can_delete_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/tasks/{$task->id}")
            ->assertStatus(200);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function user_cannot_access_another_users_task()
    {
        $task = Task::factory()->create();

        $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/tasks/{$task->id}")
            ->assertStatus(404);
    }

    /** @test */
    public function statistics_endpoint_works()
    {
        Task::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/tasks/statistics')
            ->assertStatus(200)
            ->assertJsonStructure([
                'total_tasks',
                'by_status',
                'overdue_tasks',
                'due_this_week',
            ]);
    }
}
