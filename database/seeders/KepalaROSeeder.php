<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KepalaROSeeder extends Seeder
{
    public function run()
    {
        $regions = ['JTM','CLG','BKS','PWK','TGR','CBN','BGR','SKBM','KDR'];

        foreach ($regions as $region) {
            User::updateOrCreate(
                ['email' => strtolower("kepala.$region@example.com")],
                [
                    'name' => "Kepala RO $region",
                    'password' => Hash::make('password123'), // bisa diganti
                    'role' => 2, // role kepala RO
                    'region' => $region,
                ]
            );
        }
    }
}
