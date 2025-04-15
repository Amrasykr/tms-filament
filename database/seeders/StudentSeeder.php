<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 30; $i++) {
            Student::create([
                'name' => $faker->name,
                'email' => "student{$i}@example.com",
                'password' => Hash::make('password'), // default password
                'status' => 'active',
                'profile' => null,
                'nis' => 'S' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'remember_token' => Str::random(10),
            ]);
        }
    }
}
