<?php

namespace App\Filament\Resources\StudentClassHistories\Pages;

use App\Filament\Resources\StudentClassHistories\StudentClassHistoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentClassHistory extends EditRecord
{
    protected static string $resource = StudentClassHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
