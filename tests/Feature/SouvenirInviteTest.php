<?php

namespace Tests\Feature\Api;

use App\Models\Souvenir;
use App\Models\SouvenirInvite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SouvenirInviteTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_can_create_an_invitation_token()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $souvenir = Souvenir::factory()->create();
    $souvenir->users()->attach($user->id, ['role' => 'admin']);

    SouvenirInvite::factory()->for($souvenir)->create();

    $response = $this->postJson("/api/souvenirs/{$souvenir->id}/invite");
    $response->assertStatus(201);
  }

  /** @test */
  public function member_cannot_create_an_invitation_token()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $souvenir = Souvenir::factory()->create();
    $souvenir->users()->attach($user->id, ['role' => 'member']);

    SouvenirInvite::factory()->for($souvenir)->create();

    $response = $this->postJson("/api/souvenirs/{$souvenir->id}/invite");
    $response->assertStatus(403);
  }

  /** @test */
  public function user_can_join_a_souvenir()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $souvenir = Souvenir::factory()->create();

    $invite = SouvenirInvite::factory()->for($souvenir)->create();

    $response = $this->getJson("/api/invite/{$invite->token}");
    $response->assertStatus(302);
    $response->assertRedirect(route('souvenir.show', $souvenir->id));
    $this->assertTrue($souvenir->users()->where('user_id', $user->id)->exists());
  }

  /** @test */
  public function guest_is_redirected_to_login_when_joining_a_souvenir()
  {
    $souvenir = Souvenir::factory()->create();
    $invite = SouvenirInvite::factory()->for($souvenir)->create();

    $response = $this->get("/api/invite/{$invite->token}");

    $response->assertRedirect(route('login'));

    $this->assertEquals(
      $invite->token,
      session('pending_invite_token')
    );
  }

  /** @test */
  public function it_attaches_user_to_souvenir_if_pending_invite_exists_in_session()
  {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $souvenir = Souvenir::factory()->create();
    $invite = SouvenirInvite::factory()->for($souvenir)->create();

    $response = $this->withSession(['pending_invite_token' => $invite->token])->get('/api/recent');

    $response->assertStatus(200);

    $this->assertTrue(
      $souvenir->fresh()->users()->where('user_id', $user->id)->exists()
    );
  }
}
