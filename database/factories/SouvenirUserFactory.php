<?php

namespace Database\Factories;

use App\Models\Souvenir;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SouvenirUser>
 */
class SouvenirUserFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'souvenir_id' => Souvenir::factory(),
      'user_id' => User::factory(),
      'pseudo' => $this->faker->name(),
      'role' => $this->faker->randomElement(['member', 'admin']),
    ];
  }
}
