<?php

namespace App\Filament\Teacher\Resources\TaskResource\Pages;

use App\Filament\Teacher\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    public function getRedirectURL(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
