<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('majors')->insert([
            ['name' => 'IPA', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'IPS', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}