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
        // 'role' => 'super_admin',
        User::create([
            'name' => 'adli Admin',
            'email' => 'adli@example.com',
            'password' => Hash::make('password123'),
            'role' => 1,
        ]);

        User::create([
            'name' => 'Hanif Superadmin',
            'email' => 'hanif@example.com',
            'password' => Hash::make('password123'),
            'role' => 1,
        ]);

        User::create([
            // 'role' => 'kepala_ro',
            'name' => 'Kepala RO',
            'email' => 'kepalaro@example.com',
            'password' => Hash::make('password123'),
            'role' => 2,
        ]);

        User::create([
            // 'role' => 'kepala_gudang',
            'name' => 'Kepala Gudang',
            'email' => 'kepalagudang@example.com',
            'password' => Hash::make('password123'),
            'role' => 3,
        ]);

        User::create([
            // 'role' => 'user',
            'name' => 'User Biasa',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role' => 4,
        ]);
    }
}
