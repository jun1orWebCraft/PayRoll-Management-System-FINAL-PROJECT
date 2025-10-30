<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            [
                'position_name' => 'Employee',
                'salary_rate' => 45000,
                'description' => 'Oversees operations and manages team performance.',
            ],
            [
                'position_name' => 'Registrar',
                'salary_rate' => 30000,
                'description' => 'Supervises daily operations and assists the manager.',
            ],
            [
                'position_name' => 'Guard',
                'salary_rate' => 18000,
                'description' => 'Handles customer transactions and payments.',
            ],
            [
                'position_name' => 'Cashier',
                'salary_rate' => 28000,
                'description' => 'Manages employee records and recruitment.',
            ],
            [
                'position_name' => 'Staff',
                'salary_rate' => 15000,
                'description' => 'Performs assigned tasks and assists customers.',
            ],
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate(
                ['position_name' => $position['position_name']],
                $position
            );
        }
    }
}
