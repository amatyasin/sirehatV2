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
