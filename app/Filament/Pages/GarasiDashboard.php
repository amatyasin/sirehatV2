<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;

class GarasiDashboard extends Dashboard
{
    protected static \UnitEnum|string|null $navigationGroup = 'UKGM (Upaya Kesehatan Gigi Masyarakat)';

    protected static ?string $navigationLabel = 'Dashboard UKGM';

    protected static string $routePath = 'garasi-dashboard';

    protected static ?string $title = 'Dashboard UKGM (Upaya Kesehatan Gigi Masyarakat)';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar-square';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin_dinkes', 'admin_instansi', 'admin_kecamatan', 'petugas_posyandu'])
            && auth()->user()->can('garasi.view');
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\GarasiStatsOverview::class,
        ];
    }
}
