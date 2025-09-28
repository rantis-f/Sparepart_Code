<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeederRegion extends Seeder
{
    public function run()
    {
        // Daftar region, silakan tambahkan sesuai 9 region kamu
        $regions = ['CLG', 'BKS', 'JKT'];

        foreach ($regions as $region) {
            for ($i = 1; $i <= 2; $i++) {
                User::updateOrCreate(
                    [
                        'email' => "user{$i}_{$region}@example.com"
                    ],
                    [
                        'name' => "User {$region} {$i}",
                        'password' => Hash::make('password123'),
                        'region' => $region,
                        'role' => 4, // 4 = user biasa
                    ]
                );
            }
        }
    }
}
