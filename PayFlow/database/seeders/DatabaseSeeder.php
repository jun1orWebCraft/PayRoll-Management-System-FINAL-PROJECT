<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // ✅ Add this line

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Option 1: Factory example (optional)
        // User::factory(10)->create();

        // Option 2: Create or update admin user
        User::firstOrCreate(
            ['email' => 'iandolorito16@gmail.com'],
            [
                'name' => 'HR',
                'password' => Hash::make('iandolorito'),
                'email_verified_at' => now(),
            ]
        );
 

       
    }
}
