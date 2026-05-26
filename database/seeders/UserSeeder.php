<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin SentinelOps',
            'email' => 'admin@sentinelops.com',
            'password' => Hash::make('AdminPassword123!'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // SOC Analysts
        User::create([
            'name' => 'John Analyst',
            'email' => 'john@sentinelops.com',
            'password' => Hash::make('AnalystPassword123!'),
            'role' => 'analyst',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Sarah Analyst',
            'email' => 'sarah@sentinelops.com',
            'password' => Hash::make('AnalystPassword123!'),
            'role' => 'analyst',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Mike Analyst',
            'email' => 'mike@sentinelops.com',
            'password' => Hash::make('AnalystPassword123!'),
            'role' => 'analyst',
            'is_active' => true,
        ]);

        // Viewer users
        User::create([
            'name' => 'Manager Viewer',
            'email' => 'manager@sentinelops.com',
            'password' => Hash::make('ViewerPassword123!'),
            'role' => 'viewer',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Director Viewer',
            'email' => 'director@sentinelops.com',
            'password' => Hash::make('ViewerPassword123!'),
            'role' => 'viewer',
            'is_active' => true,
        ]);
    }
}
