<?php

namespace App\Filament\Resources\PemeriksaanGizis\Pages;

use App\Filament\Resources\PemeriksaanGizis\PemeriksaanGiziResource;
use App\Models\PemeriksaanGizi;
use App\Models\StudentClassHistory;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPemeriksaanGizi extends EditRecord
{
    protected static string $resource =
        PemeriksaanGiziResource::class;

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
                    'Hapus Pemeriksaan Gizi'
                )

                ->modalDescription(
                    'Data pemeriksaan gizi akan dihapus permanen.'
                )

                ->successNotificationTitle(
                    'Pemeriksaan gizi berhasil dihapus'
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
            PemeriksaanGizi::query()

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
                    'Pemeriksaan gizi semester ini sudah ada.'
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

            isset(
                $data[
                    'berat_badan'
                ]
            )

        ) {

            if (

                $data[
                    'berat_badan'
                ] <= 0

            ) {

                Notification::make()

                    ->title(
                        'Berat badan tidak valid.'
                    )

                    ->body(
                        'Berat badan harus lebih dari 0.'
                    )

                    ->danger()

                    ->send();

                $this->halt();
            }
        }

        if (

            isset(
                $data[
                    'tinggi_badan'
                ]
            )

        ) {

            if (

                $data[
                    'tinggi_badan'
                ] <= 0

            ) {

                Notification::make()

                    ->title(
                        'Tinggi badan tidak valid.'
                    )

                    ->body(
                        'Tinggi badan harus lebih dari 0.'
                    )

                    ->danger()

                    ->send();

                $this->halt();
            }
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
}
