<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {

        User::create([
            'name' => 'Ammar Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'profile' => null,
            'remember_token' => Str::random(10),
        ]);

        for ($i = 1; $i <= 4; $i++) {
            User::create([
                'name' => "Admin {$i}",
                'email' => "admin{$i}@example.com",
                'password' => Hash::make('password'),
                'status' => 'active',
                'profile' => null,
                'remember_token' => Str::random(10),
            ]);
        }
    }
}
