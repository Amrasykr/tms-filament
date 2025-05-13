<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    protected $guarded = [];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function classSession()
    {
        return $this->belongsTo(ClassSessions::class, 'class_session_id');
    }


    public function studentTasks()
    {
        return $this->hasMany(StudentTask::class, 'task_id');
    }

    protected static function booted()
    {
        static::created(function (Task $task) {
            $task->generateStudentTasks();
        });

        static::deleted(function ($task) {
            $scheduleId = $task->schedule_id;

            // Ambil semua student dari kelas terkait
            $studentIds = \App\Models\StudentClass::where('class_id', $task->schedule->class_id)->pluck('student_id');

            foreach ($studentIds as $studentId) {
                // Hitung ulang task_score untuk setiap siswa
                $averageScore = \App\Models\StudentTask::whereHas('task', function ($query) use ($scheduleId) {
                    $query->where('schedule_id', $scheduleId);
                })->where('student_id', $studentId)->avg('score') ?? 0;

                // Update nilai task_score di tabel grades
                \App\Models\Grade::where('student_id', $studentId)
                    ->where('schedule_id', $scheduleId)
                    ->update(['task_score' => $averageScore]);
            }
        });
    }

    

    public function generateStudentTasks()
    {
        // Ambil schedule yang terkait dengan task ini
        $schedule = $this->schedule;

        if (!$schedule) {
            return;
        }

        // Ambil semua student_id dari kelas yang terkait dengan schedule ini
        $studentIds = StudentClass::where('class_id', $schedule->class_id)
            ->pluck('student_id');

        // Buat student_task untuk setiap murid
        foreach ($studentIds as $studentId) {
            \App\Models\StudentTask::create([
                'student_id' => $studentId,
                'task_id' => $this->id,
            ]);
        }
    }
}
