<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Angela',
            'email' => 'angela@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('adminpassword'),
            'role' => 'admin',
        ]);
    }
}
