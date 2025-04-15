<?php

namespace Database\Seeders;

use App\Models\AcademicYears;
use App\Models\Classes;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYears = AcademicYears::all();
        $teachers = Teacher::all();

        if ($teachers->isEmpty() || $academicYears->isEmpty()) {
            return;
        }

        $grades = ["10", "11", "12"];
        $sections = ["A", "B", "C"];

        foreach ($academicYears as $year) {
            foreach ($grades as $grade) {
                foreach ($sections as $section) {
                    Classes::create([
                        'name' => "$grade$section $year->name",
                        'academic_year_id' => $year->id,
                        'code' => Str::random(10),
                        'teacher_id' => $teachers->random()->id,
                    ]);
                }
            }
        }
    }
}
