<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'firstname' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => 'password',
            'role' => 'admin',
            'personal_points' => 100
        ]);

        User::factory()->create([
            'name' => 'Doe',
            'firstname' => 'Jhon',
            'email' => 'jhondoe@gmail.com',
            'password' => 'password',
            'personal_points' => 0
        ]);

        User::factory()->create([
            'name' => 'Test',
            'firstname' => 'Test',
            'email' => 'test@test.com',
            'password' => 'password',
            'personal_points' => 0
        ]);
    }
}
