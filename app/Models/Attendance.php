<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';

    protected $fillable = [
        'status',
        'notes',
        'class_session_id',
        'student_id',
    ];

    public function classSession()
    {
        return $this->belongsTo(ClassSessions::class, 'class_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }    


     protected static function booted()
    {
        static::saved(function (Attendance $attendance) {
            // Update attendance score di tabel grade
            Grade::updateOrCreate(
                [
                    'student_id' => $attendance->student_id,
                    'schedule_id' => $attendance->classSession->schedule_id,
                ],
                []
            )->updateAttendanceScore();
        });

        static::deleted(function (Attendance $attendance) {
            // Update attendance score di tabel grade
            Grade::updateOrCreate(
                [
                    'student_id' => $attendance->student_id,
                    'schedule_id' => $attendance->classSession->schedule_id,
                ],
                []
            )->updateAttendanceScore();
        });
    }

    
}
