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
}
