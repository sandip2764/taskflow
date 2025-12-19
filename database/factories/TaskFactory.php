<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'priority' => $this->faker->randomElement(['low','medium','high']),
            'status' => $this->faker->randomElement(['pending','in_progress','completed']),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 week'),
        ];
    }
}
