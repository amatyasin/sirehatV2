<?php

namespace App\Filament\Resources\PemeriksaanGizis\Pages;

use App\Filament\Resources\PemeriksaanGizis\PemeriksaanGiziResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemeriksaanGizis extends ListRecords
{
    protected static string $resource = PemeriksaanGiziResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
