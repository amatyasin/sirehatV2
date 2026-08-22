<?php

namespace App\Filament\Resources\GarasiActivities\Pages;

use App\Filament\Resources\GarasiActivities\GarasiActivityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGarasiActivities extends ListRecords
{
    protected static string $resource = GarasiActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
