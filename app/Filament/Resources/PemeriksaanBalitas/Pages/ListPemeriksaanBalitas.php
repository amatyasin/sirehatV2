<?php

namespace App\Filament\Resources\PemeriksaanBalitas\Pages;

use App\Filament\Resources\PemeriksaanBalitas\PemeriksaanBalitaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemeriksaanBalitas extends ListRecords
{
    protected static string $resource = PemeriksaanBalitaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
