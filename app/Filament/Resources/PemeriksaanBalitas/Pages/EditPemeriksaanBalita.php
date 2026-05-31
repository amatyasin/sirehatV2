<?php

namespace App\Filament\Resources\PemeriksaanBalitas\Pages;

use App\Filament\Resources\PemeriksaanBalitas\PemeriksaanBalitaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPemeriksaanBalita extends EditRecord
{
    protected static string $resource = PemeriksaanBalitaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
