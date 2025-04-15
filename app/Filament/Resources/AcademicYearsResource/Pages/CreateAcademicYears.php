<?php

namespace App\Filament\Resources\AcademicYearsResource\Pages;

use App\Filament\Resources\AcademicYearsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicYears extends CreateRecord
{
    protected static string $resource = AcademicYearsResource::class;

    public function getRedirectURL(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
