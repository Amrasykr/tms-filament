<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser;

class Student extends Authenticatable implements FilamentUser
{
    protected $table = 'students';

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $panel->getId() === 'student';
    }

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function studentClasses()
    {
        return $this->hasMany(StudentClass::class);
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'student_classes', 'student_id', 'class_id');
    }


    public function studentTasks()
    {
        return $this->hasMany(StudentTask::class);
    }

    public function currentClass()
    {
        return $this->hasOneThrough(
            Classes::class,
            StudentClass::class,
            'student_id',
            'id',
            'id',
            'class_id'
        )->whereHas('academicYear', function ($query) {
            $query->where('status', 'active');
        });
    }
    
}
