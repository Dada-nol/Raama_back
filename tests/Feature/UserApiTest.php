<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_register_a_user()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Doe',
            'firstname' => 'John',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'user',
            'personal_points' => 0
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    /** @test */
    public function it_cant_register_a_user_if_confirmed_password_not_match()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Doe',
            'firstname' => 'John',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'rvbr',
            'role' => 'user',
            'personal_points' => 0
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_can_login_a_user_and_return_token()
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'jane@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    /** @test */
    public function it_cant_login_a_user_if_datas_not_match()
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'jane@example.com',
            'password' => 'blabla',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_get_authenticated_user_profile()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'email' => $user->email,
                'name' => $user->name,
            ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_profile()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_update_a_user_datas()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson("/api/user/{$user->id}", [
            'name' => 'test',
            'firstname' => 'test',
            'email' => 'test@test.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'email' => 'test@test.com',
                'name' => 'test',
            ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_logout_a_user_and_remove_token()
    {
        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->postJson('/api/login', [
            'email' => 'jane@example.com',
            'password' => 'secret123',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJsonMissingPath('token');
    }

    /** @test */
    public function it_can_remove_a_user_profil()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/user');

        $response->assertStatus(200);
    }
}
