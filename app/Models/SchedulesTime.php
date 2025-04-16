<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchedulesTime extends Model
{
    protected $table = 'schedules_times';

    protected $fillable = [
        'start_time',
        'end_time',
        'day'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

}
