<?php

namespace App\Filament\Resources\PosyanduMonthlyExaminations\Pages;

use App\Filament\Resources\PosyanduMonthlyExaminations\PosyanduMonthlyExaminationResource;
use Filament\Resources\Pages\EditRecord;

class EditPosyanduMonthlyExamination extends EditRecord
{
    protected static string $resource = PosyanduMonthlyExaminationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        if (! empty($data['examination_date'])) {
            $date = \Carbon\Carbon::parse($data['examination_date']);
            $data['month'] = $date->month;
            $data['year'] = $date->year;
        }

        return $data;
    }
}
