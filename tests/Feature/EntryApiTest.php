<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Entry;
use App\Models\Souvenir;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Mockery;

class EntryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_their_entries()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $souvenir = Souvenir::factory()->for($user, 'creator')->create();
        $souvenir->users()->attach($user->id, ['role' => 'admin']);

        Entry::factory()->create();

        $response = $this->getJson("/api/souvenir/{$souvenir->id}/entry");

        $response->assertStatus(200);
    }

    public function test_user_cannot_list_their_entries_if_there_is_no_souvenir()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $souvenir = Souvenir::factory()->for($user, 'creator')->create();

        Entry::factory()->create();

        $response = $this->getJson("/api/souvenir/{$souvenir->id}/entry");

        $response->assertStatus(404);
    }

    public function test_user_cannot_list_their_entries_if_there_is_no_entry()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $souvenir = Souvenir::factory()->for($user, 'creator')->create();

        $response = $this->getJson("/api/souvenir/{$souvenir->id}/entry");

        $response->assertStatus(404);
    }

    public function test_user_can_create_entry()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $souvenir = Souvenir::factory()->create();
        $souvenir->users()->attach($user->id, ['role' => 'admin']);

        $file = UploadedFile::fake()->image('image.jpg');

        $response = $this->postJson("/api/souvenir/{$souvenir->id}/entry", [
            'image_path' => $file,
            'caption' => 'Voici le contenu...',
        ]);

        $response->assertCreated();
        $this->assertTrue(
            Storage::disk('public')->exists('souvenirs/entries/' . $file->hashName()),
            'Le fichier n\'a pas été trouvé sur le disque fake.'
        );

        $this->assertDatabaseHas('entries', [
            'image_path' => 'souvenirs/entries/' . $file->hashName(),
            'caption' => 'Voici le contenu...',
            'souvenir_id' => $souvenir->id,
        ]);
    }

    public function test_user_cannot_create_entry_if_missing_img()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $souvenir = Souvenir::factory()->create();
        $souvenir->users()->attach($user->id, ['role' => 'admin']);

        $response = $this->postJson("/api/souvenir/{$souvenir->id}/entry", [
            'caption' => 'Voici le contenu...',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_cannot_upload_multiple_entries_per_day()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $souvenir = Souvenir::factory()->create();
        $souvenir->users()->attach($user->id, ['role' => 'admin']);

        $file1 = UploadedFile::fake()->image('image1.jpg');
        $response1 = $this->postJson("/api/souvenir/{$souvenir->id}/entry", [
            'image_path' => $file1,
            'caption' => 'Première image',
        ]);

        $response1->assertCreated();

        $file2 = UploadedFile::fake()->image('image2.jpg');
        $response2 = $this->postJson("/api/souvenir/{$souvenir->id}/entry", [
            'image_path' => $file2,
            'caption' => 'Deuxième image',
        ]);

        $response2->assertStatus(403);
        $response2->assertJson([
            'message' => 'Vous avez déjà uploadé une image aujourd\'hui.',
        ]);
    }

    public function test_user_can_upload_entry_if_previous_entry_was_before_today()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $souvenir = Souvenir::factory()->create();
        $souvenir->users()->attach($user->id, ['role' => 'admin']);

        Entry::factory()->create([
            'user_id' => $user->id,
            'souvenir_id' => $souvenir->id,
            'created_at' => now()->subDays(7),
        ]);

        $file = UploadedFile::fake()->image('new_image.jpg');
        $response = $this->postJson("/api/souvenir/{$souvenir->id}/entry", [
            'image_path' => $file,
            'caption' => 'Nouvelle image',
        ]);

        $response->assertCreated();
    }

    public function test_user_and_souvenir_get_points_when_creating_entry()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $souvenir = Souvenir::factory()->create();
        $souvenir->users()->attach($user->id, ['role' => 'admin']);

        $file = UploadedFile::fake()->image('image.jpg');

        $response = $this->postJson("/api/souvenir/{$souvenir->id}/entry", [
            'image_path' => $file,
            'caption' => 'Voici le contenu...',
        ]);

        $response->assertCreated();

        $user->refresh();
        $souvenir->refresh();

        $this->assertEquals(10, $user->personal_points);
        $this->assertEquals(50, $souvenir->memory_points);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'personal_points' => 10
        ]);

        $this->assertDatabaseHas('souvenirs', [
            'id' => $souvenir->id,
            'memory_points' => 50
        ]);
    }

    public function test_guest_cant_create_entry()
    {
        $souvenir = Souvenir::factory()->create();

        $file = UploadedFile::fake()->image('image.jpg');

        $response = $this->postJson("/api/souvenir/{$souvenir->id}/entry", [
            'image_path' => $file,
            'caption' => 'Voici le contenu...',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_cant_create_entry_if_he_is_not_on_the_souvenir()
    {
        $user1 = User::factory()->create();
        Sanctum::actingAs($user1);

        $user2 = User::factory()->create();

        $souvenir = Souvenir::factory()->create();
        $souvenir->users()->attach($user2->id, ['role' => 'admin']);

        $file = UploadedFile::fake()->image('image.jpg');

        $response = $this->postJson("/api/souvenir/{$souvenir->id}/entry", [
            'user_id' => $user2->id,
            'souvenir_id' => $souvenir->id,
            'image_path' => $file,
            'caption' => 'Voici le contenu...',
        ]);

        $response->assertStatus(404);
    }

    public function test_transaction_is_rolled_back_if_saving_entry_fails()
    {
        $user = User::factory()->create(['personal_points' => 0]);
        Sanctum::actingAs($user);

        $souvenir = Souvenir::factory()->create(['memory_points' => 0]);
        $souvenir->users()->attach($user->id, ['role' => 'admin']);

        $file = UploadedFile::fake()->image('image.jpg');

        $response = $this->postJson("/api/souvenir/{$souvenir->id}/entry", [
            'image_path' => $file,
            'force_error' => true,
        ]);

        $response->assertStatus(500);

        // Vérifier qu'aucune donnée n’a été modifiée
        $this->assertDatabaseMissing('entries', [
            'user_id' => $user->id,
            'souvenir_id' => $souvenir->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'personal_points' => 0,
        ]);

        $this->assertDatabaseHas('souvenirs', [
            'id' => $souvenir->id,
            'memory_points' => 0,
        ]);
    }

    /* public function test_user_can_view_single_entry()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);



        $entry = Entry::factory()->for($user)->create();

        $response = $this->getJson("/api/souvenir/{$souvenir->id}entry");

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $entry->id,
            ]);
    }

    public function test_user_can_update_entry()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $entry = Entry::factory()->for($user)->create([
            'title' => 'Ancien titre'
        ]);

        $response = $this->putJson("/api/entries/{$entry->id}", [
            'title' => 'Nouveau titre',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Nouveau titre']);

        $this->assertDatabaseHas('entries', ['id' => $entry->id, 'title' => 'Nouveau titre']);
    }

    public function test_user_can_delete_entry()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $entry = Entry::factory()->for($user)->create();

        $response = $this->deleteJson("/api/entries/{$entry->id}");

        $response->assertNoContent(); // 204

        $this->assertDatabaseMissing('entries', ['id' => $entry->id]);
    } */
}
