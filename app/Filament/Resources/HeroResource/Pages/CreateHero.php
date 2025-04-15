<?php

namespace App\Filament\Resources\HeroResource\Pages;

use App\Filament\Resources\HeroResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHero extends CreateRecord
{
    protected static string $resource = HeroResource::class;

        public function getRedirectURL(): string
        {
            return $this->getResource()::getUrl('index');
        }

        protected function beforeSave(): void
        {
            if ($this->data['image_file']) {
                $this->data['image'] = basename($this->data['image_file']);
            }
        }
}
