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
