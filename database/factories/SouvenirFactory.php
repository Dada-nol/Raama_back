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
      'memory_type_id' => 1,
      'title' => $this->faker->name(),
    ];
  }
}
