<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_list_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function authenticated_user_can_create_category()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/categories', [
                'name' => 'New Category',
                'color' => '#FF5733',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'New Category');

        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'color' => '#FF5733',
        ]);
    }

    /** @test */
    public function category_name_must_be_unique()
    {
        Category::factory()->create(['name' => 'Work']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/categories', [
                'name' => 'Work',
                'color' => '#FF5733',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function category_color_must_be_valid_hex_code()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/categories', [
                'name' => 'Invalid Color',
                'color' => 'invalid-color',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['color']);
    }
}
