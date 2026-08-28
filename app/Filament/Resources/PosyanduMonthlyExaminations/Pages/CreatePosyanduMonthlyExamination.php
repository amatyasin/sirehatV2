<?php

namespace App\Filament\Resources\PosyanduMonthlyExaminations\Pages;

use App\Filament\Resources\PosyanduMonthlyExaminations\PosyanduMonthlyExaminationResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePosyanduMonthlyExamination extends CreateRecord
{
    protected static string $resource = PosyanduMonthlyExaminationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        if (! empty($data['examination_date'])) {
            $date = \Carbon\Carbon::parse($data['examination_date']);
            $data['month'] = $date->month;
            $data['year'] = $date->year;
        }

        return $data;
    }
}
