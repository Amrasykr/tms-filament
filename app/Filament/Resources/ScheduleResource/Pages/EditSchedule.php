<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;


class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;


    protected function beforeSave(): void
    {
        $data = $this->form->getState();
        $recordId = $this->record->id;
        $existingScheduleTimeId = $this->record->schedule_time_id;
        $existingClassId = $this->record->class_id;
        $existingTeacherId = $this->record->teacher_id;
        $existingAcademicYearId = $this->record->academic_year_id;

        // Cek apakah ada perubahan pada schedule_time_id, class_id, teacher_id, atau academic_year_id
        $isScheduleTimeChanged = $data['schedule_time_id'] != $existingScheduleTimeId;
        $isClassChanged = $data['class_id'] != $existingClassId;
        $isTeacherChanged = $data['teacher_id'] != $existingTeacherId;
        $isAcademicYearChanged = $data['academic_year_id'] != $existingAcademicYearId;

        // Lanjut validasi hanya kalau ada perubahan
        if ($isScheduleTimeChanged || $isClassChanged || $isAcademicYearChanged) {
            // Cek konflik jadwal kelas
            $conflictClass = \App\Models\Schedule::where('schedule_time_id', $data['schedule_time_id'])
                ->where('class_id', $data['class_id'])
                ->where('academic_year_id', $data['academic_year_id'])
                ->where('id', '!=', $recordId)
                ->exists();

            if ($conflictClass) {
                Notification::make()
                    ->title('Kelas ini sudah memiliki jadwal di waktu tersebut untuk tahun ajaran ini.')
                    ->danger()
                    ->send();

                throw ValidationException::withMessages([
                    'schedule_time_id' => 'Kelas ini sudah memiliki jadwal di waktu tersebut untuk tahun ajaran ini.'
                ]);
            }
        }

        // Lanjut validasi hanya kalau ada perubahan teacher
        if ($isScheduleTimeChanged || $isTeacherChanged || $isAcademicYearChanged) {
            // Cek konflik jadwal guru
            $conflictTeacher = \App\Models\Schedule::where('schedule_time_id', $data['schedule_time_id'])
                ->where('teacher_id', $data['teacher_id'])
                ->where('academic_year_id', $data['academic_year_id'])
                ->where('id', '!=', $recordId)
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
}