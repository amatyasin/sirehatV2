<?php

namespace App\Filament\Resources\Kelurahans\Pages;

use App\Filament\Resources\Kelurahans\KelurahanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKelurahan extends EditRecord
{
    protected static string $resource = KelurahanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
