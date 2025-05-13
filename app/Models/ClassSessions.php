<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSessions extends Model
{
    protected $table = 'class_sessions';

    protected $fillable = [
        'description',
        'session_number',
        'session_date',
        'schedule_id',
        'status'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function attendances()
    {
        return $this->hasMany(\App\Models\Attendance::class, 'class_session_id');
    }
    
    protected static function booted()
    {
        static::created(function (ClassSessions $session) {
            // Ambil semua student yang terdaftar di kelas terkait schedule ini
            $studentIds = StudentClass::whereHas('class', function ($query) use ($session) {
                $query->whereHas('schedules', function ($query) use ($session) {
                    $query->where('id', $session->schedule_id);
                });
            })->pluck('student_id');

            // Buat attendance untuk setiap murid di sesi ini
            foreach ($studentIds as $studentId) {
                Attendance::create([
                    'student_id' => $studentId,
                    'class_session_id' => $session->id,
                    'status' => null,
                    'notes' => null,
                ]);
            }
        });

        static::deleted(function (ClassSessions $session) {
            // Ambil semua student yang memiliki attendance di sesi ini
            $studentIds = Attendance::where('class_session_id', $session->id)->pluck('student_id');

            foreach ($studentIds as $studentId) {
                // Update attendance score di tabel grade
                Grade::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'schedule_id' => $session->schedule_id,
                    ],
                    []
                )->updateAttendanceScore();
            }
        });
    }
    
}
