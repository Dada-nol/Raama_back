<?php

namespace Database\Factories;

use App\Models\Souvenir;
use App\Models\SouvenirInvite;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SouvenirInviteFactory extends Factory
{
    protected $model = SouvenirInvite::class;

    public function definition(): array
    {
        return [
            'souvenir_id' => Souvenir::factory(),
            'token' => Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn() => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function valid(): static
    {
        return $this->state(fn() => [
            'expires_at' => now()->addDay(),
        ]);
    }
}
