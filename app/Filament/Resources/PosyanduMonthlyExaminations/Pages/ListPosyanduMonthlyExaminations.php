<?php

namespace App\Filament\Resources\PosyanduMonthlyExaminations\Pages;

use App\Filament\Resources\PosyanduMonthlyExaminations\PosyanduMonthlyExaminationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPosyanduMonthlyExaminations extends ListRecords
{
    protected static string $resource = PosyanduMonthlyExaminationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Pemeriksaan Bulanan')
                ->icon('heroicon-o-plus'),
        ];
    }
}
