<?php

namespace App\Filament\Resources\SchedulesTimeResource\Pages;

use App\Filament\Resources\SchedulesTimeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSchedulesTimes extends ManageRecords
{
    protected static string $resource = SchedulesTimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
