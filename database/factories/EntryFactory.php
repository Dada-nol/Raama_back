<?php

namespace Database\Factories;

use App\Models\Souvenir;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entry>
 */
class EntryFactory extends Factory
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
      'image_path' => $this->faker->randomElement(['image-1', 'image-2', 'image-3']),
      'caption' => fake()->sentence(),
    ];
  }
}
