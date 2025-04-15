<?php

namespace Database\Seeders;

use App\Models\AcademicYears;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        AcademicYears::insert([
            [
                'name' => '2023/2024 Ganjil',
                'start_date' => Carbon::create(2023, 7, 1), 
                'end_date' => Carbon::create(2023, 12, 31),
                'semester' => 'ganjil',
                'status' => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2023/2024 Genap',
                'start_date' => Carbon::create(2024, 1, 1), 
                'end_date' => Carbon::create(2024, 6, 30),  
                'semester' => 'genap',
                'status' => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2024/2025 Ganjil',
                'start_date' => Carbon::create(2024, 7, 1), 
                'end_date' => Carbon::create(2024, 12, 31), 
                'semester' => 'ganjil',
                'status' => 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '2024/2025 Genap',
                'start_date' => Carbon::create(2025, 1, 1),
                'end_date' => Carbon::create(2025, 6, 30),  
                'semester' => 'genap',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
