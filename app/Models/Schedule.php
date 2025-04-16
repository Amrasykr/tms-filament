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
        'schedule_time_id',
        'is_repeating',
        'number_of_sessions',
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

    public function classSessions()
    {
        return $this->hasMany(ClassSessions::class);
    }

    protected static function booted()
    {
        static::created(function ($schedule) {
            $schedule->generateClassSessions();
        });

    }

    public function generateClassSessions()
    {
        $academicYear = $this->academicYear;
        $scheduleTime = $this->scheduleTime;

        if (!$academicYear || !$scheduleTime) {
            return;
        }

        $startDate = \Carbon\Carbon::parse($academicYear->start_date);
        $day = strtolower($scheduleTime->day); // contoh: "monday"

        // Cari hari pertama yang cocok dari start_date academic year
        $firstSessionDate = $startDate->copy()->next($day);
        if ($startDate->isSameDay($firstSessionDate)) {
            $firstSessionDate = $startDate;
        }

        $sessionCount = $this->is_repeating ? $this->number_of_sessions : 1;

        for ($i = 0; $i < $sessionCount; $i++) {
            \App\Models\ClassSessions::create([
                'schedule_id' => $this->id,
                'session_number' => $i + 1,
                'session_date' => $firstSessionDate->copy()->addWeeks($i)->toDateString(),
                'status' => 'pending',
            ]);
        }
    }

}
