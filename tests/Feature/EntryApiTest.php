<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Entry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class EntryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_their_entries()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Entry::factory()->count(3)->for($user)->create();

        $response = $this->getJson('/api/entries');

        $response->assertOk()
            ->assertJsonCount(3);
    }

    public function test_user_can_view_single_entry()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $entry = Entry::factory()->for($user)->create();

        $response = $this->getJson("/api/entries/{$entry->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $entry->id,
            ]);
    }

    public function test_user_can_create_entry()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $data = [
            'title' => 'Un souvenir marquant',
            'content' => 'Voici le contenu...',
            // Ajoute ici les champs requis par ta validation
        ];

        $response = $this->postJson('/api/entries', $data);

        $response->assertCreated()
            ->assertJsonFragment(['title' => 'Un souvenir marquant']);

        $this->assertDatabaseHas('entries', ['title' => 'Un souvenir marquant']);
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
    }
}
