<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;

class GarasiDashboard extends Dashboard
{
    protected static \UnitEnum|string|null $navigationGroup = 'GARASI Anak';

    protected static ?string $navigationLabel = 'Dashboard GARASI';

    protected static string $routePath = 'garasi-dashboard';

    protected static ?string $title = 'Dashboard GARASI Anak';

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
