<?php

namespace Tests\Feature;

use App\Models\MemoryType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemoryTypeApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_read_all_memory_type()
    {
        MemoryType::factory()->count(3)->create();

        $response = $this->getJson('/api/memory-type');

        $response->assertStatus(200)->assertJsonCount(3);
    }
}
