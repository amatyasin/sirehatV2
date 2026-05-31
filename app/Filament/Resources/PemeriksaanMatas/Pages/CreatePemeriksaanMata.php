<?php

namespace App\Filament\Resources\PemeriksaanMatas\Pages;

use App\Filament\Resources\PemeriksaanMatas\PemeriksaanMataResource;
use App\Models\PemeriksaanMata;
use App\Models\StudentClassHistory;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePemeriksaanMata extends CreateRecord
{
    protected static string $resource =
        PemeriksaanMataResource::class;

    protected function beforeCreate(): void
    {

        $history =
            StudentClassHistory::find(
                $this->data['student_class_history_id']
            );

        if (! $history) {

            Notification::make()

                ->title(
                    'Riwayat akademik siswa tidak ditemukan.'
                )

                ->danger()

                ->send();

            $this->halt();
        }

        if (! $history->aktif) {

            Notification::make()

                ->title(
                    'Semester siswa sudah tidak aktif.'
                )

                ->body(
                    'Pemeriksaan hanya dapat dilakukan pada semester aktif.'
                )

                ->danger()

                ->send();

            $this->halt();
        }

        $user =
            auth()->user();

        if (

            $user->hasAnyRole([

                'admin_instansi',

                'petugas_pemeriksaan',

            ])

        ) {

            abort_unless(

                $history->school?->instansi_id ===
                $user->instansi_id,

                403

            );
        }

        $exists =
            PemeriksaanMata::where(
                'student_class_history_id',
                $this->data['student_class_history_id']
            )->exists();

        if ($exists) {

            Notification::make()

                ->title(
                    'Pemeriksaan mata semester ini sudah ada.'
                )

                ->body(
                    'Silakan edit data pemeriksaan yang sudah tersedia.'
                )

                ->danger()

                ->send();

            $this->halt();
        }
    }
}
