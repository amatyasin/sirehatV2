<?php

namespace App\Filament\Resources\GarasiActivities\Pages;

use App\Filament\Resources\GarasiActivities\GarasiActivityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGarasiActivity extends EditRecord
{
    protected static string $resource = GarasiActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
