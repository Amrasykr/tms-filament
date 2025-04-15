<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYears extends Model
{
    protected $table = 'academic_years';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status'
    ];

    

    public function classes()
    {
        return $this->hasMany(Classes::class, 'academic_year_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'academic_year_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'academic_year_id');
    }
}
