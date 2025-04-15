<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            // Kelas 10
            ['name' => 'Matematika Wajib', 'code' => 'MATH10'],
            ['name' => 'Bahasa Indonesia', 'code' => 'BINDO10'],
            ['name' => 'Fisika', 'code' => 'FIS10'],
            ['name' => 'Sejarah Indonesia', 'code' => 'SEJ10'],

            // Kelas 11
            ['name' => 'Matematika Peminatan', 'code' => 'MATH11'],
            ['name' => 'Bahasa Inggris', 'code' => 'BING11'],
            ['name' => 'Kimia', 'code' => 'KIM11'],
            ['name' => 'Sosiologi', 'code' => 'SOS11'],

            // Kelas 12
            ['name' => 'Matematika Peminatan', 'code' => 'MATH12'],
            ['name' => 'Bahasa Indonesia', 'code' => 'BINDO12'],
            ['name' => 'Biologi', 'code' => 'BIO12'],
            ['name' => 'Ekonomi', 'code' => 'EKO12'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
