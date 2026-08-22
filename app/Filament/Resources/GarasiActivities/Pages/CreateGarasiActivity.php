<?php

namespace App\Filament\Resources\GarasiActivities\Pages;

use App\Filament\Resources\GarasiActivities\GarasiActivityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGarasiActivity extends CreateRecord
{
    protected static string $resource = GarasiActivityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
