<?php

namespace App\Filament\Resources\PemeriksaanGigis\Pages;

use App\Filament\Resources\PemeriksaanGigis\PemeriksaanGigiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemeriksaanGigis extends ListRecords
{
    protected static string $resource = PemeriksaanGigiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
