<?php

namespace App\Filament\Resources\GarasiFollowUps\Pages;

use App\Filament\Resources\GarasiFollowUps\GarasiFollowUpResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGarasiFollowUp extends EditRecord
{
    protected static string $resource = GarasiFollowUpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
