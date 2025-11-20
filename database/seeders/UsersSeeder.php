<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => 'admin12345', 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            ['name' => 'Staff', 'password' => 'staff12345', 'role' => 'staff']
        );
    }
}