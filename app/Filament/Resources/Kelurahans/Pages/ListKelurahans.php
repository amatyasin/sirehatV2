<?php

namespace App\Filament\Resources\Kelurahans\Pages;

use App\Filament\Resources\Kelurahans\KelurahanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKelurahans extends ListRecords
{
    protected static string $resource = KelurahanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
