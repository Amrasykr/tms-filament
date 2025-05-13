<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTask extends Model
{
    protected $table = 'student_tasks';

    protected $fillable = [
        'student_id',
        'task_id',
        'status',
        'score',
        'file_path'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }


    protected static function booted()
    {
        static::saved(function ($studentTask) {
            // Cari Grade terkait
            $grade = Grade::where('student_id', $studentTask->student_id)
                ->where('schedule_id', $studentTask->task->schedule_id)
                ->first();

            if ($grade) {
                $grade->recalculateTaskScore();
            }
        });
    }
}
