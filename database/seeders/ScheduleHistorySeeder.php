<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\AcademicYears;
use App\Models\Classes;
use App\Models\ClassSessions;
use App\Models\Grade;
use App\Models\SchedulesTime;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ScheduleHistorySeeder extends Seeder
{
    public function run(): void
    {
        $academicYears = AcademicYears::where('status', '!=', 'active')->get();
        if ($academicYears->isEmpty()) return;

        $scheduleTimes = SchedulesTime::whereIn('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])->get();
        $subjects = Subject::all();
        $teachers = Teacher::all();

        foreach ($academicYears as $academicYear) {
            $classes = Classes::where('academic_year_id', $academicYear->id)->get();

            foreach ($classes as $class) {
                $students = DB::table('student_classes')
                    ->where('class_id', $class->id)
                    ->pluck('student_id');

                if ($students->isEmpty()) continue;

                foreach ($scheduleTimes as $availableTime) {
                    $teacher = $teachers->random();
                    $subject = $subjects->random();
                    $sessionCount = 3;

                    $schedule = Schedule::create([
                        'class_id' => $class->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'academic_year_id' => $academicYear->id,
                        'schedule_time_id' => $availableTime->id,
                        'is_repeating' => true,
                        'number_of_sessions' => $sessionCount,
                    ]);

                    $startDate = Carbon::parse($academicYear->start_date)->next(Carbon::parse($availableTime->day)->dayOfWeek);

                    for ($i = 1; $i <= $sessionCount; $i++) {
                        $sessionDate = (clone $startDate)->addWeeks($i - 1);
                        $session = ClassSessions::create([
                            'schedule_id' => $schedule->id,
                            'description' => "Materi sesi $i",
                            'session_number' => $i,
                            'session_date' => $sessionDate,
                            'status' => 'completed',
                        ]);

                        foreach ($students as $studentId) {
                            Attendance::create([
                                'student_id' => $studentId,
                                'class_session_id' => $session->id,
                                'status' => 'present',
                                'notes' => null,
                            ]);
                        }
                    }

                    foreach ($students as $studentId) {
                        Grade::create([
                            'student_id' => $studentId,
                            'schedule_id' => $schedule->id,
                            'attendance_score' => rand(70, 100),
                            'task_score' => rand(70, 100),
                            'midterm_score' => rand(70, 100),
                            'final_exam_score' => rand(70, 100),
                        ]);
                    }
                }
            }
        }
    }
}
