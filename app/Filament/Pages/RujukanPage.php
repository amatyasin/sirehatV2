<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class RujukanPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-top-right-on-square';

    protected static ?string $navigationLabel = 'Rujukan';

    protected static ?string $title = 'Rujukan Kesehatan Siswa';

    protected static ?string $slug = 'rujukan-redirect';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Rujukan';

    protected static ?int $navigationSort = 1;

    public function getView(): string
    {
        return 'filament.pages.rujukan-page';
    }

    public function getStats(): array
    {
        $user = auth()->user();
        $referralQuery = \App\Models\Referral::query();

        if ($user) {
            if ($user->hasRole('admin_instansi') || $user->hasRole('petugas_pemeriksaan')) {
                $referralQuery->whereHas('studentClassHistory.school', fn ($sq) => $sq->where('instansi_id', $user->instansi_id));
            }

            if ($user->hasRole('admin_sekolah')) {
                $referralQuery->whereHas('studentClassHistory', fn ($sq) => $sq->where('school_id', $user->school_id));
            }
        }

        $totalReferral  = $referralQuery->count();
        $belumDirujuk   = (clone $referralQuery)->where('status_rujukan', 'Belum Dirujuk')->count();
        $prosesRujukan  = (clone $referralQuery)->whereIn('status_rujukan', ['Sudah Dirujuk', 'Dalam Tindak Lanjut'])->count();
        $selesaiRujukan = (clone $referralQuery)->where('status_rujukan', 'Selesai')->count();

        return [
            'total' => $totalReferral,
            'belum' => $belumDirujuk,
            'proses' => $prosesRujukan,
            'selesai' => $selesaiRujukan,
        ];
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole([
            'super_admin',
            'admin_dinkes',
            'admin_instansi',
            'admin_sekolah',
            'petugas_pemeriksaan',
        ]);
    }
}
