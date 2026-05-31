<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\StudentClassHistory;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStudent extends CreateRecord
{
    protected static string $resource =
        StudentResource::class;

    protected function handleRecordCreation(
        array $data
    ): Model {

        $schoolClassId =
            $data['school_class_id'];

        $academicYearId =
            $data['academic_year_id'];

        $semester =
            $data['semester'];

        unset(

            $data['school_class_id'],

            $data['academic_year_id'],

            $data['semester'],

        );

        $record =
            static::getModel()::create(
                $data
            );

        StudentClassHistory::create([

            'student_id' => $record->id,

            'school_id' => $record->school_id,

            'school_class_id' => $schoolClassId,

            'academic_year_id' => $academicYearId,

            'semester' => $semester,

            'aktif' => true,

        ]);

        return $record;
    }
}
