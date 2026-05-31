<?php

namespace App\Filament\Resources\PemeriksaanUmums\Pages;

use App\Filament\Resources\PemeriksaanUmums\PemeriksaanUmumResource;
use App\Models\PemeriksaanUmum;
use App\Models\StudentClassHistory;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPemeriksaanUmum extends EditRecord
{
    protected static string $resource =
        PemeriksaanUmumResource::class;

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

                )

                ->requiresConfirmation()

                ->modalHeading(
                    'Hapus Pemeriksaan Umum'
                )

                ->modalDescription(
                    'Data pemeriksaan umum akan dihapus permanen.'
                )

                ->successNotificationTitle(
                    'Pemeriksaan umum berhasil dihapus'
                ),

        ];
    }

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {

        $data['student_class_history_id'] = $data['student_class_history_id'] ?? $this->record->student_class_history_id;

        $history =
            StudentClassHistory::with([
                'school',
            ])->find(
                $data['student_class_history_id']
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
                    'Data hanya dapat diubah pada semester aktif.'
                )

                ->danger()

                ->send();

            $this->halt();
        }

        $user =
            auth()->user();

        if (

            $user->hasRole(
                'admin_instansi'
            )

        ) {

            if (

                $history
                    ->school
                    ?->instansi_id !==

                $user->instansi_id

            ) {

                Notification::make()

                    ->title(
                        'Anda tidak memiliki akses ke siswa ini.'
                    )

                    ->danger()

                    ->send();

                $this->halt();
            }
        }

        if (

            $user->hasRole(
                'admin_sekolah'
            )

        ) {

            if (

                $history->school_id !==
                $user->school_id

            ) {

                Notification::make()

                    ->title(
                        'Anda tidak memiliki akses ke siswa ini.'
                    )

                    ->danger()

                    ->send();

                $this->halt();
            }
        }

        if (

            $user->hasRole(
                'petugas_pemeriksaan'
            )

        ) {

            if (

                $history
                    ->school
                    ?->instansi_id !==

                $user->instansi_id

            ) {

                Notification::make()

                    ->title(
                        'Anda tidak memiliki akses ke siswa ini.'
                    )

                    ->danger()

                    ->send();

                $this->halt();
            }
        }

        $exists =
            PemeriksaanUmum::where(

                'student_class_history_id',

                $data['student_class_history_id']

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
                    'Pemeriksaan umum semester ini sudah ada.'
                )

                ->body(
                    'Silakan edit data pemeriksaan yang sudah tersedia.'
                )

                ->danger()

                ->send();

            $this->halt();
        }

        return $data;
    }
}
