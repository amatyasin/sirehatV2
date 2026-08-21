<?php

namespace App\Filament\Resources\Children\Pages;

use App\Filament\Resources\Children\ChildResource;
use App\Models\Child;
use App\Models\Posyandu;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

use App\Models\OrangTua;

class CreateChild extends CreateRecord
{
    protected static string $resource =
        ChildResource::class;

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {
        $user = auth()->user();

        $posyandu = Posyandu::find($data['posyandu_id'] ?? null);

        if (! $posyandu) {
            Notification::make()
                ->title('Posyandu tidak ditemukan.')
                ->danger()
                ->send();

            $this->halt();
        }

        if ($user->hasRole('admin_instansi')) {
            abort_unless(
                $posyandu->instansi_id === $user->instansi_id,
                403
            );
        }

        if ($user->hasRole('petugas_posyandu')) {
            abort_unless(
                $posyandu->id === $user->posyandu_id,
                403
            );
        }

        if (empty($data['orang_tua_id'])) {
            Notification::make()
                ->title('Orang Tua wajib dipilih.')
                ->danger()
                ->send();

            $this->halt();
        }

        $orangTua = OrangTua::find($data['orang_tua_id']);
        if (! $orangTua || $orangTua->posyandu_id !== $posyandu->id) {
            Notification::make()
                ->title('Orang Tua tidak sesuai dengan Posyandu yang dipilih.')
                ->danger()
                ->send();

            $this->halt();
        }

        if (! empty($data['nik'])) {
            $exists = Child::query()
                ->where('nik', $data['nik'])
                ->exists();

            if ($exists) {
                Notification::make()
                    ->title('NIK anak sudah digunakan.')
                    ->danger()
                    ->send();

                $this->halt();
            }
        }

        // Composite duplicate check (orang_tua_id + nama_lengkap + tanggal_lahir)
        if (! empty($data['nama_lengkap']) && ! empty($data['tanggal_lahir'])) {
            $duplicate = Child::query()
                ->where('orang_tua_id', $data['orang_tua_id'])
                ->whereRaw('LOWER(nama_lengkap) = ?', [strtolower(trim($data['nama_lengkap']))])
                ->where('tanggal_lahir', $data['tanggal_lahir'])
                ->exists();

            if ($duplicate) {
                Notification::make()
                    ->title('Data anak sudah terdaftar.')
                    ->body('Data anak dengan orang tua, nama, dan tanggal lahir tersebut sudah terdaftar.')
                    ->danger()
                    ->send();

                $this->halt();
            }
        }

        if (
            $data['tanggal_lahir']
            > now()->toDateString()
        ) {
            Notification::make()
                ->title('Tanggal lahir tidak valid.')
                ->body('Tanggal lahir tidak boleh melebihi hari ini.')
                ->danger()
                ->send();

            $this->halt();
        }

        $data['instansi_id'] = $posyandu->instansi_id;
        $data['aktif'] = $data['aktif'] ?? true;

        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {
            return static::getModel()::create($data);
        });
    }
}
