<?php

namespace App\Filament\Resources\PemeriksaanMatas\Pages;

use App\Filament\Resources\PemeriksaanMatas\PemeriksaanMataResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemeriksaanMatas extends ListRecords
{
    protected static string $resource = PemeriksaanMataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
