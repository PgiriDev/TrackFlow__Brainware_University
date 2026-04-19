<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed system categories and rules
        $this->call([
            CategorySeeder::class,
            CategoryRuleSeeder::class,
        ]);

        // Create demo user (optional)
        if (app()->environment('local')) {
            User::factory()->create([
                'name' => 'Demo User',
                'email' => 'demo@trackflow.com',
                'password' => bcrypt('password'),
            ]);
        }
    }
}
