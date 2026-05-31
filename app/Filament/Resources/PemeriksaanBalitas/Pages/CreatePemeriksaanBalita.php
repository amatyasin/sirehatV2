<?php

namespace App\Filament\Resources\PemeriksaanBalitas\Pages;

use App\Filament\Resources\PemeriksaanBalitas\PemeriksaanBalitaResource;
use App\Models\PemeriksaanBalita;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePemeriksaanBalita extends CreateRecord
{
    protected static string $resource =
        PemeriksaanBalitaResource::class;

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {

        $tanggal = Carbon::parse(
            $data['tanggal_pemeriksaan']
        );

        $exists = PemeriksaanBalita::where(
            'child_id',
            $data['child_id']
        )
            ->whereMonth(
                'tanggal_pemeriksaan',
                $tanggal->month
            )
            ->whereYear(
                'tanggal_pemeriksaan',
                $tanggal->year
            )
            ->exists();

        if ($exists) {

            Notification::make()

                ->title(
                    'Pemeriksaan bulan ini sudah ada'
                )

                ->body(
                    'Anak hanya boleh diperiksa 1x dalam 1 bulan.'
                )

                ->danger()

                ->send();

            $this->halt();
        }

        return $data;
    }
}
