<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $table = 'grades';

    protected $fillable = [
        'student_id',
        'schedule_id',
        'academic_year_id',
        'attendance_score',
        'task_score',
        'midterm_score',
        'final_exam_score',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYears::class, 'academic_year_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function studentTasks()
    {
        return $this->hasMany(StudentTask::class, 'student_id', 'student_id')
            ->whereHas('task', function ($query) {
                $query->where('schedule_id', $this->schedule_id);
            });
    }

    public function recalculateTaskScore()
    {
        $averageScore = $this->studentTasks()->avg('score') ?? 0;
        $this->update(['task_score' => $averageScore]);
    }
    
}
