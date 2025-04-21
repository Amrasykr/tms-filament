<?php

namespace App\Filament\Teacher\Resources\ScheduleResource\Pages;

use App\Filament\Teacher\Resources\ScheduleResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;


class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $teacher = Filament::auth()->user();
    
        return static::getResource()::getEloquentQuery()
            ->where('teacher_id', $teacher->id)
            ->withCount('classSessions');
    }
}
