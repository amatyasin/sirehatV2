<?php

namespace App\Filament\Resources\RekapPemeriksaans\Pages;

use App\Filament\Resources\RekapPemeriksaans\RekapPemeriksaanResource;
use Filament\Resources\Pages\ListRecords;

class ListRekapPemeriksaans extends ListRecords
{
    protected static string $resource =
        RekapPemeriksaanResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
