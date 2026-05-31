<?php

namespace App\Filament\Resources\RekapPemeriksaans\Pages;

use App\Filament\Resources\RekapPemeriksaans\RekapPemeriksaanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditRekapPemeriksaan extends EditRecord
{
    protected static string $resource = RekapPemeriksaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
