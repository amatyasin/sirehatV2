<?php

namespace App\Filament\Resources\PemeriksaanGigis\Pages;

use App\Filament\Resources\PemeriksaanGigis\PemeriksaanGigiResource;
use App\Models\PemeriksaanGigi;
use App\Models\StudentClassHistory;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPemeriksaanGigi extends EditRecord
{
    protected static string $resource =
        PemeriksaanGigiResource::class;

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
                    'Hapus Pemeriksaan Gigi'
                )

                ->modalDescription(
                    'Data pemeriksaan gigi akan dihapus permanen.'
                )

                ->successNotificationTitle(
                    'Pemeriksaan gigi berhasil dihapus'
                ),

        ];
    }

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {

        $data['student_class_history_id'] = $data['student_class_history_id'] ?? $this->record->student_class_history_id;

        $user =
            auth()->user();

        $history =
            StudentClassHistory::with([

                'student',

                'school',

                'schoolClass',

                'academicYear',

            ])->find(

                $data[
                    'student_class_history_id'
                ]

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

        if (

            $user->hasAnyRole([

                'admin_instansi',

                'petugas_pemeriksaan',

            ])

        ) {

            if (

                $history
                    ->school
                    ?->instansi_id

                !==

                $user->instansi_id

            ) {

                Notification::make()

                    ->title(
                        'Akses ditolak.'
                    )

                    ->body(
                        'Data siswa bukan dari instansi Anda.'
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

                $history->school_id

                !==

                $user->school_id

            ) {

                Notification::make()

                    ->title(
                        'Akses ditolak.'
                    )

                    ->body(
                        'Data siswa bukan dari sekolah Anda.'
                    )

                    ->danger()

                    ->send();

                $this->halt();
            }
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

        $exists =
            PemeriksaanGigi::query()

                ->where(

                    'student_class_history_id',

                    $data[
                        'student_class_history_id'
                    ]

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
                    'Pemeriksaan gigi semester ini sudah ada.'
                )

                ->body(
                    'Silakan edit data pemeriksaan yang sudah tersedia.'
                )

                ->danger()

                ->send();

            $this->halt();
        }

        if (

            isset(
                $data[
                    'tanggal_pemeriksaan'
                ]
            )

        ) {

            $tanggal =
                Carbon::parse(

                    $data[
                        'tanggal_pemeriksaan'
                    ]

                );

            if (

                $tanggal->isFuture()

            ) {

                Notification::make()

                    ->title(
                        'Tanggal pemeriksaan tidak valid.'
                    )

                    ->body(
                        'Tanggal pemeriksaan tidak boleh melebihi hari ini.'
                    )

                    ->danger()

                    ->send();

                $this->halt();
            }
        }

        if (

            ($data[
                'gigi_berlubang'
            ] ?? 'N')

            === 'N'

        ) {

            $data[
                'jumlah_gigi_berlubang'
            ] = null;
        }

        if (

            ($data[
                'dirujuk_ke_fasyankes'
            ] ?? 'N')

            === 'N'

        ) {

            $data[
                'keterangan_rujukan'
            ] = null;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
