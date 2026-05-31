<?php

namespace App\Filament\Resources\Posyandus\Pages;

use App\Filament\Resources\Posyandus\PosyanduResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPosyandu extends EditRecord
{
    protected static string $resource = PosyanduResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
