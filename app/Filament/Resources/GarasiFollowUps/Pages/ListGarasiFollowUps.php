<?php

namespace App\Filament\Resources\GarasiFollowUps\Pages;

use App\Filament\Resources\GarasiFollowUps\GarasiFollowUpResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGarasiFollowUps extends ListRecords
{
    protected static string $resource = GarasiFollowUpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Evaluasi Follow-up')
                ->icon('heroicon-o-plus'),
        ];
    }
}
