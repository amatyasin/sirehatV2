<?php

namespace App\Filament\Resources\Children\Pages;

use App\Filament\Resources\Children\ChildResource;
use App\Models\Child;
use App\Models\Posyandu;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateChild extends CreateRecord
{
    protected static string $resource =
        ChildResource::class;

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {

        $user =
            auth()->user();

        $posyandu =
            Posyandu::find(
                $data['posyandu_id']
            );

        if (! $posyandu) {

            Notification::make()

                ->title(
                    'Posyandu tidak ditemukan.'
                )

                ->danger()

                ->send();

            $this->halt();
        }

        if (

            $user->hasRole(
                'admin_instansi'
            )

        ) {

            abort_unless(

                $posyandu->instansi_id ===
                $user->instansi_id,

                403

            );
        }

        if (

            $user->hasRole(
                'petugas_posyandu'
            )

        ) {

            abort_unless(

                $posyandu->id ===
                $user->posyandu_id,

                403

            );
        }

        if (! empty($data['nik'])) {

            $exists =
                Child::query()

                    ->where(
                        'nik',
                        $data['nik']
                    )

                    ->exists();

            if ($exists) {

                Notification::make()

                    ->title(
                        'NIK anak sudah digunakan.'
                    )

                    ->danger()

                    ->send();

                $this->halt();
            }
        }

        if (

            $data['tanggal_lahir']
            >
            now()->toDateString()

        ) {

            Notification::make()

                ->title(
                    'Tanggal lahir tidak valid.'
                )

                ->body(
                    'Tanggal lahir tidak boleh melebihi hari ini.'
                )

                ->danger()

                ->send();

            $this->halt();
        }

        DB::transaction(function () use (&$data, $posyandu) {

            $data['instansi_id'] =
                $posyandu->instansi_id;

            $data['aktif'] =
                $data['aktif'] ?? true;

        });

        return $data;
    }
}
