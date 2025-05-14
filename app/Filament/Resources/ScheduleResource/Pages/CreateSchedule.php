<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;


class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;


protected function beforeCreate(): void
{
    $data = $this->form->getState();

    // Cek konflik jadwal kelas
    $conflictClass = \App\Models\Schedule::where('schedule_time_id', $data['schedule_time_id'])
        ->where('class_id', $data['class_id'])
        ->where('academic_year_id', $data['academic_year_id'])
        ->exists();

    if ($conflictClass) {
        Notification::make()
            ->title('Kelas ini sudah memiliki jadwal di waktu tersebut untuk tahun ajaran ini.')
            ->danger()
            ->send();

        // Throw ValidationException untuk batalin create tanpa error 500
        throw ValidationException::withMessages([
            'schedule_time_id' => 'Kelas ini sudah memiliki jadwal di waktu tersebut untuk tahun ajaran ini.'
        ]);
    }

    // Cek konflik jadwal guru
    $conflictTeacher = \App\Models\Schedule::where('schedule_time_id', $data['schedule_time_id'])
        ->where('teacher_id', $data['teacher_id'])
        ->where('academic_year_id', $data['academic_year_id'])
        ->exists();

    if ($conflictTeacher) {
        Notification::make()
            ->title('Guru ini sudah memiliki jadwal di waktu tersebut untuk tahun ajaran ini.')
            ->danger()
            ->send();

        throw ValidationException::withMessages([
            'schedule_time_id' => 'Guru ini sudah memiliki jadwal di waktu tersebut untuk tahun ajaran ini.'
        ]);
    }
}
}