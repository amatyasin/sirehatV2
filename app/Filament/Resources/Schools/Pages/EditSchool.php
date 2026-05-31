<?php

namespace App\Filament\Resources\Schools\Pages;

use App\Filament\Resources\Schools\SchoolResource;
use App\Models\School;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSchool extends EditRecord
{
    protected static string $resource =
        SchoolResource::class;

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {

        $exists =
            School::where(

                'nama_sekolah',

                $data['nama_sekolah']

            )
                ->where(
                    'instansi_id',
                    $data['instansi_id']
                )
                ->where(
                    'id',
                    '!=',
                    $this->record->id
                )
                ->exists();

        if ($exists) {

            Notification::make()

                ->title(
                    'Sekolah sudah terdaftar pada puskesmas ini.'
                )

                ->danger()

                ->send();

            $this->halt();
        }

        return $data;
    }
}
