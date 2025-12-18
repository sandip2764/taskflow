<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        // Sample tasks for each user
        $tasks = [
            [
                'title' => 'Complete Laravel Assignment',
                'description' => 'Build task management system with Laravel 12',
                'priority' => 'high',
                'status' => 'in_progress',
                'due_date' => now()->addDays(2),
            ],
            [
                'title' => 'Buy Groceries',
                'description' => 'Milk, Eggs, Bread, Vegetables',
                'priority' => 'medium',
                'status' => 'pending',
                'due_date' => now()->addDay(),
            ],
            [
                'title' => 'Gym Workout',
                'description' => 'Evening workout session',
                'priority' => 'low',
                'status' => 'pending',
                'due_date' => now(),
            ],
            [
                'title' => 'Client Meeting',
                'description' => 'Discuss project requirements',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => now()->subDays(1),
            ],
            [
                'title' => 'Pay Electricity Bill',
                'description' => 'Due date approaching',
                'priority' => 'high',
                'status' => 'pending',
                'due_date' => now()->addDays(3),
            ],
            [
                'title' => 'Learn Vue.js',
                'description' => 'Complete Vue.js tutorial',
                'priority' => 'medium',
                'status' => 'in_progress',
                'due_date' => now()->addWeek(),
            ],
        ];

        foreach ($users as $user) {
            foreach ($tasks as $taskData) {
                $task = $user->tasks()->create($taskData);
                
                // Attach random categories (1-3 categories per task)
                $randomCategories = $categories->random(rand(1, 3));
                $task->categories()->attach($randomCategories);
            }
        }
    }
}
