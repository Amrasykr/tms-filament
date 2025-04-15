<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 10; $i++) {
            Teacher::create([
                'name' => $faker->name,
                'email' => "teacher{$i}@example.com",
                'password' => Hash::make('password'), // default password
                'status' => 'active',
                'profile' => null,
                'nip' => 'T' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'remember_token' => Str::random(10),
            ]);
        }
    }
}
