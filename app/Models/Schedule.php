<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules';

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'class_id',
        'academic_year_id',
        'schedule_time_id'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYears::class);
    }

    public function scheduleTime()
    {
        return $this->belongsTo(SchedulesTime::class);
    }
}
