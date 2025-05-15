<?php

namespace App\Filament\Teacher\Resources\TaskResource\Pages;

use App\Filament\Teacher\Resources\TaskResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

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
            ->whereHas('schedule', function (Builder $query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            });
    }
}