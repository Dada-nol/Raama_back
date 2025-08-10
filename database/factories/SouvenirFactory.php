<?php

namespace Database\Factories;

use App\Models\MemoryType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SouvenirFactory extends Factory
{

  public function definition(): array
  {
    return [
      'user_id' => User::factory(),
      'memory_type_id' => MemoryType::firstOrCreate(
        ['title' => 'One per day'],
        [
          'description' => 'Partagez une photo par jour avec vos proches',
          'isAvailable' => true
        ]
      )->id,
      'title' => $this->faker->name(),
    ];
  }
}
