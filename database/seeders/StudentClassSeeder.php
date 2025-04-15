<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua siswa secara acak
        $students = Student::inRandomOrder()->get();

        // Ambil semua kelas dan kelompokkan berdasarkan academic_year_id
        $classesByYear = Classes::all()->groupBy('academic_year_id');

        foreach ($classesByYear as $academicYearId => $classes) {
            $availableStudents = $students->shuffle(); // Acak ulang siswa untuk tiap tahun ajaran

            foreach ($classes as $class) {
                // Ambil 3-4 siswa dari daftar yang tersedia
                $studentCount = rand(3, 4);
                $selectedStudents = $availableStudents->splice(0, $studentCount);

                foreach ($selectedStudents as $student) {
                    DB::table('student_classes')->insert([
                        'student_id' => $student->id,
                        'class_id' => $class->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
