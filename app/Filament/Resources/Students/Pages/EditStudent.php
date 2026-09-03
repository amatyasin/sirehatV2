<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\School;
use App\Models\StudentClassHistory;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStudent extends EditRecord
{
    protected static string $resource =
        StudentResource::class;

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {

        $user =
            auth()->user();

        $school =
            School::findOrFail(
                $data['school_id']
            );

        if (

            $user->hasRole(
                'admin_instansi'
            )

        ) {

            abort_unless(

                $school->instansi_id ===
                $user->instansi_id,

                403

            );
        }

        if (

            $user->hasRole(
                'admin_sekolah'
            )

        ) {

            abort_unless(

                $school->id ===
                $user->school_id,

                403

            );
        }

        $data['instansi_id'] =
            $school->instansi_id;

        StudentClassHistory::updateOrCreate(
            [
                'student_id' => $this->record->id,
                'aktif' => true,
            ],
            [
                'school_id' => $school->id,
                'school_class_id' => $data['school_class_id'],
                'academic_year_id' => $data['academic_year_id'],
                'semester' => $data['semester'],
            ]
        );

        unset(

            $data['school_class_id'],

            $data['academic_year_id'],

            $data['semester']

        );

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [

            DeleteAction::make()

                ->visible(

                    auth()
                        ->user()
                        ->hasAnyRole([

                            'super_admin',

                            'admin_dinkes',

                        ])

                ),

        ];
    }
}
