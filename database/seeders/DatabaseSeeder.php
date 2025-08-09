<?php

namespace Database\Seeders;

use App\Models\MemoryType;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'firstname' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => 'password',
            'role' => 'admin',
            'personal_points' => 100
        ]);

        MemoryType::factory()->create([
            'title' => 'One per day',
            'description' => 'Partagez une photo par jour avec vos proches',
            'isAvailable' => true
        ]);

        MemoryType::factory()->create([
            'title' => 'Simple album',
            'description' => 'Partagez vos souvenirs librement'
        ]);

        MemoryType::factory()->create([
            'title' => "Mysteries's Box",
            'description' => 'Remontons le temps un peu'
        ]);

        $this->call(SouvenirSeeder::class);
    }
}
