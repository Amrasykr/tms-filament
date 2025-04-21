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

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYears::where('status', 'active')->first();
        if (!$academicYear) return;

        $classes = Classes::where('academic_year_id', $academicYear->id)->get();
        $scheduleTimes = SchedulesTime::whereIn('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'])->get();
        $subjects = Subject::all();
        $teachers = Teacher::all();

        $usedSchedule = [];

        foreach ($classes as $class) {
            $students = DB::table('student_classes')
                ->where('class_id', $class->id)
                ->pluck('student_id');

            if ($students->isEmpty()) continue;

            foreach ($scheduleTimes as $availableTime) {
                $teacher = $teachers->random();
                $subject = $subjects->random();

                if (
                    in_array("{$class->id}_{$availableTime->id}", $usedSchedule) ||
                    in_array("{$teacher->id}_{$availableTime->id}", $usedSchedule)
                ) continue;

                $sessionCount = 4;

                $existingSchedule = Schedule::where([
                    'class_id' => $class->id,
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'academic_year_id' => $academicYear->id,
                    'schedule_time_id' => $availableTime->id,
                ])->first();

                if ($existingSchedule) continue;

                $schedule = Schedule::create([
                    'class_id' => $class->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'academic_year_id' => $academicYear->id,
                    'schedule_time_id' => $availableTime->id,
                    'is_repeating' => true,
                    'number_of_sessions' => $sessionCount,
                ]);

                $usedSchedule[] = "{$class->id}_{$availableTime->id}";
                $usedSchedule[] = "{$teacher->id}_{$availableTime->id}";

                $startDate = Carbon::parse($academicYear->start_date)->next(Carbon::parse($availableTime->day)->dayOfWeek);

                for ($i = 1; $i <= $sessionCount; $i++) {
                    $sessionDate = (clone $startDate)->copy()->addWeeks($i - 1);

                    if (ClassSessions::where('schedule_id', $schedule->id)->where('session_number', $i)->exists()) {
                        continue;
                    }

                    $session = ClassSessions::create([
                        'schedule_id' => $schedule->id,
                        'description' => null,
                        'session_number' => $i,
                        'session_date' => $sessionDate,
                        'status' => 'pending',
                    ]);

                    foreach ($students as $studentId) {
                        Attendance::create([
                            'student_id' => $studentId,
                            'class_session_id' => $session->id,
                            'status' => null,
                            'notes' => null,
                        ]);
                    }
                }

                foreach ($students as $studentId) {
                    if (!Grade::where('student_id', $studentId)->where('schedule_id', $schedule->id)->exists()) {
                        Grade::create([
                            'student_id' => $studentId,
                            'schedule_id' => $schedule->id,
                            'attendance_score' => 0,
                            'task_score' => 0,
                            'midterm_score' => 0,
                            'final_exam_score' => 0,
                        ]);
                    }
                }
            }
        }
    }
}
