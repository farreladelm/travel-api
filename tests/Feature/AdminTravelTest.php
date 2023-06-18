<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTravelTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_cannot_access_add_new_travel(): void
    {
        $response = $this->postJson('/api/v1/admin/travels');

        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_adding_value(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels');

        $response->assertStatus(403);
    }

    public function test_saves_travel_data_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => 'Travel name',
        ]);

        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'name' => 'Travel name',
            'is_public' => 1,
            'description' => 'Travel name',
            'number_of_days' => 4,
        ]);

        $response->assertStatus(201);

        $response = $this->get('/api/v1/travels');

        $response->assertJsonFragment(['name' => 'Travel name']);
    }

    public function test_public_user_cannot_access_update_travel(): void
    {
        $travel = Travel::factory()->create();
        $response = $this->putJson('/api/v1/admin/travels/'.$travel->id);

        $response->assertStatus(401);
    }

    public function test_update_travel_data_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $travel = Travel::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id, [
            'name' => 'Travel name',
        ]);

        $response->assertStatus(422);

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/'.$travel->id, [
            'name' => 'Travel name updated',
            'is_public' => 1,
            'description' => 'Travel name',
            'number_of_days' => 4,
        ]);

        $response->assertStatus(200);

        $response = $this->get('/api/v1/travels');

        $response->assertJsonFragment(['name' => 'Travel name updated']);
    }
}
