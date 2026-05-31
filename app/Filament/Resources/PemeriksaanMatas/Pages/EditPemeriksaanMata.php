<?php

namespace App\Filament\Resources\PemeriksaanMatas\Pages;

use App\Filament\Resources\PemeriksaanMatas\PemeriksaanMataResource;
use App\Models\PemeriksaanMata;
use App\Models\StudentClassHistory;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPemeriksaanMata extends EditRecord
{
    protected static string $resource =
        PemeriksaanMataResource::class;

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
                    'Hapus Pemeriksaan Mata'
                )

                ->modalDescription(
                    'Data pemeriksaan mata akan dihapus permanen.'
                )

                ->successNotificationTitle(
                    'Pemeriksaan mata berhasil dihapus'
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
                    'Pemeriksaan mata semester ini sudah ada.'
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
