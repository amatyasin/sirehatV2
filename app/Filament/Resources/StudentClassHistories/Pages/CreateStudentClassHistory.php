<?php

namespace App\Filament\Resources\StudentClassHistories\Pages;

use App\Filament\Resources\StudentClassHistories\StudentClassHistoryResource;
use App\Models\Student;
use App\Models\StudentClassHistory;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateStudentClassHistory extends CreateRecord
{
    protected static string $resource =
        StudentClassHistoryResource::class;

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {

        $student =
            Student::find(
                $data['student_id']
            );

        if (

            ! $student

            ||

            $student->school_id
            !=
            $data['school_id']

        ) {

            Notification::make()

                ->title(
                    'Siswa tidak sesuai sekolah.'
                )

                ->body(
                    'Pastikan siswa berasal dari sekolah yang dipilih.'
                )

                ->danger()

                ->send();

            $this->halt();
        }

        $exists =
            StudentClassHistory::query()

                ->where(
                    'student_id',
                    $data['student_id']
                )

                ->where(
                    'school_class_id',
                    $data['school_class_id']
                )

                ->where(
                    'academic_year_id',
                    $data['academic_year_id']
                )

                ->where(
                    'semester',
                    $data['semester']
                )

                ->exists();

        if ($exists) {

            Notification::make()

                ->title(
                    'Riwayat kelas siswa sudah ada.'
                )

                ->body(
                    'Siswa sudah memiliki riwayat pada kelas dan semester tersebut.'
                )

                ->danger()

                ->send();

            $this->halt();
        }

        DB::transaction(function () use (&$data) {

            StudentClassHistory::query()

                ->where(
                    'student_id',
                    $data['student_id']
                )

                ->where(
                    'aktif',
                    true
                )

                ->update([

                    'aktif' => false,

                ]);

            $data['aktif'] = true;

        });

        return $data;
    }
}
