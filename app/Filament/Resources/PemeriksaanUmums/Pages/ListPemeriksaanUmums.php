<?php

namespace App\Filament\Resources\PemeriksaanUmums\Pages;

use App\Filament\Resources\PemeriksaanUmums\PemeriksaanUmumResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemeriksaanUmums extends ListRecords
{
    protected static string $resource = PemeriksaanUmumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
