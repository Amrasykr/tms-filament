<?php

namespace App\Filament\Teacher\Resources\ScheduleResource\Pages;

use App\Filament\Teacher\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchedule extends EditRecord
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        $schedule = $this->getRecord();

        return [
            ScheduleResource::getUrl() => 'Jadwal',
            '' =>  ($schedule->subject->name   ?? 'Tanpa Nama'),
        ];
    }

}