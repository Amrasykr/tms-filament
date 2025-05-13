<?php

namespace Database\Seeders;

use App\Models\AcademicYears;
use App\Models\Classes;
use App\Models\Major;
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
        $majors = Major::whereIn('name', ['IPA', 'IPS'])->get();

        if ($teachers->isEmpty() || $academicYears->isEmpty() || $majors->isEmpty()) {
            return;
        }

        $grades = ["10", "11", "12"];
        $sections = ["A", "B"];
        $majorsList = ['IPA', 'IPS'];

        foreach ($academicYears as $year) {
            foreach ($grades as $grade) {
                foreach ($sections as $section) {
                    foreach ($majorsList as $majorName) {
                        $major = $majors->where('name', $majorName)->first();

                        Classes::create([
                            'name' => "$grade$section $majorName $year->name",
                            'academic_year_id' => $year->id,
                            'code' => Str::random(10),
                            'teacher_id' => $teachers->random()->id,
                            'major_id' => $major->id,
                        ]);
                    }
                }
            }
        }
    }
}