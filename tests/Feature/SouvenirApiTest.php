<?php

namespace Tests\Feature\Api;

use App\Models\MemoryType;
use App\Models\Souvenir;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SouvenirApiTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_can_store_a_souvenir()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $memoryType = MemoryType::factory()->create();

    $response = $this->postJson('/api/souvenir', [
      'user_id' => $user->id,
      'memory_type' => $memoryType->id,
      'title' => 'Mon titre',
      'cover_image' => UploadedFile::fake()->image('image-1.jpg'),
      'memory_points' => 0
    ]);

    $response->assertStatus(201);
  }

  /** @test */
  public function it_cannot_store_a_souvenir_if_missing_data()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/souvenir', [
      'memory_points' => 0

    ]);

    $response->assertStatus(422);
  }

  /** @test */
  public function it_cannot_store_a_souvenir_if_unauthenticated()
  {
    $memoryType = MemoryType::factory()->create();

    $response = $this->postJson('/api/souvenir', [
      'memory_type' => $memoryType->id,
      'title' => 'Mon titre',
      'cover_image' => UploadedFile::fake()->image('image-1.jpg'),
      'memory_points' => 0
    ]);

    $response->assertStatus(401);
  }

  /** @test */
  public function it_can_read_all_souvenirs()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $souvenir = Souvenir::factory()->for($user, 'creator')->count(10)->create();
    foreach ($souvenir as $item) {
      $item->users()->attach($user->id, ['role' => 'admin']);
    }

    $response = $this->getJson('/api/souvenirs');

    $response->assertStatus(200)->assertJsonCount(10);
  }

  /** @test */
  public function it_can_read_a_souvenir()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $souvenir = Souvenir::factory()->for($user, 'creator')->create();
    $souvenir->users()->attach($user->id, ['role' => 'admin']);

    $response = $this->getJson("/api/souvenir/{$souvenir->id}");

    $response->assertStatus(200);
  }

  /** @test */
  public function unauthenticate_user_cant_read_a_souvenir()
  {
    $souvenir = Souvenir::factory()->count(10)->create();
    $response = $this->getJson('/api/souvenirs');

    $response->assertStatus(401);
  }

  /** @test */
  public function it_can_update_a_souvenir()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $souvenir = Souvenir::factory()->create();
    $souvenir->users()->attach($user->id, ['role' => 'admin']);

    $response = $this->putJson("/api/souvenir/{$souvenir->id}", [
      'title' => 'Mon titre',
      'cover_image' => UploadedFile::fake()->image('image-1.jpg'),
    ]);

    $response->assertStatus(200);
  }

  /** @test */
  public function it_cannot_update_a_souvenir_with_role_member()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $souvenir = Souvenir::factory()->create();
    $souvenir->users()->attach($user->id, ['role' => 'member']);

    $response = $this->putJson("/api/souvenir/{$souvenir->id}", [
      'title' => 'Mon titre',
      'cover_image' => UploadedFile::fake()->image('image-1.jpg'),
    ]);

    $response->assertStatus(403);
  }

  /** @test */
  public function it_cannot_update_a_souvenir_if_unauthenticate()
  {
    $souvenir = Souvenir::factory()->create();
    $response = $this->putJson("/api/souvenir/{$souvenir->id}", [
      'title' => 'Mon titre',
      'cover_image' => UploadedFile::fake()->image('image-1.jpg'),
    ]);

    $response->assertStatus(401);
  }

  /** @test */
  public function it_can_delete_a_souvenir()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $souvenir = Souvenir::factory()->create();
    $souvenir->users()->attach($user->id, ['role' => 'admin']);

    $response = $this->deleteJson("/api/souvenir/{$souvenir->id}");

    $response->assertStatus(200);
  }

  /** @test */
  public function it_can_delete_a_souvenir_if_unauthenticate()
  {
    $souvenir = Souvenir::factory()->create();
    $response = $this->deleteJson("/api/souvenir/{$souvenir->id}");

    $response->assertStatus(401);
  }

  /** @test */
  public function it_cannot_delete_a_souvenir_with_role_member()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $souvenir = Souvenir::factory()->create();
    $souvenir->users()->attach($user->id, ['role' => 'member']);

    $response = $this->deleteJson("/api/souvenir/{$souvenir->id}");

    $response->assertStatus(403);
  }
}
