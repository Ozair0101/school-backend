<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default developer user
        User::firstOrCreate(
            ['email' => 'dev@dev.com'],
            [
                'name' => 'Developer',
                'username' => 'developer',
                'email' => 'dev@dev.com',
                'password' => Hash::make('dev'),
                'role' => 'admin',
            ]
        );
    }
}

