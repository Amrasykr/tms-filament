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
}
