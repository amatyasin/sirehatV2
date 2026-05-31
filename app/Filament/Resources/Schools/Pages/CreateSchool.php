<?php

namespace App\Filament\Resources\Schools\Pages;

use App\Filament\Resources\Schools\SchoolResource;
use App\Models\School;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSchool extends CreateRecord
{
    protected static string $resource =
        SchoolResource::class;

    protected function beforeCreate(): void
    {

        $exists =
            School::where(

                'nama_sekolah',

                $this->data['nama_sekolah']

            )
                ->where(
                    'instansi_id',
                    $this->data['instansi_id']
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
    }
}
