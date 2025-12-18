<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Work', 'color' => '#3B82F6'],
            ['name' => 'Personal', 'color' => '#10B981'],
            ['name' => 'Urgent', 'color' => '#EF4444'],
            ['name' => 'Shopping', 'color' => '#F59E0B'],
            ['name' => 'Health', 'color' => '#8B5CF6'],
            ['name' => 'Finance', 'color' => '#EC4899'],
            ['name' => 'Education', 'color' => '#06B6D4'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
