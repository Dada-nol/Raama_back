<?php

namespace Database\Seeders;

use App\Models\Entry;
use App\Models\MemoryType;
use App\Models\Souvenir;
use App\Models\SouvenirUser;
use App\Models\User;
use Database\Factories\SouvenirFactory;
use Illuminate\Database\Seeder;

class SouvenirSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $entryFactory = Entry::factory()->count(30);
    $souvenirUser = SouvenirUser::factory()->count(1, 4);

    Souvenir::factory()->count(10)
      ->has($entryFactory)
      ->has($souvenirUser, 'users')
      ->create();
  }
}
