<?php

namespace App\Filament\Resources\GarasiActivities\Widgets;

use App\Models\GarasiActivity;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class GarasiActivityStatsWidget extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        if (! $this->record) {
            return [];
        }

        $activity = $this->record;
        
        $totalSasaran = \App\Models\Child::where('posyandu_id', $activity->posyandu_id)
            ->where('aktif', true)
            ->count();
            
        $pesertaCount = $activity->participants()->count();
        
        $hadirCount = $activity->participants()->where('attendance', true)->count();
        $tidakHadirCount = $activity->participants()->where('attendance', false)->count();
        
        $skriningCount = $activity->participants()->whereHas('screening')->count();
        $rujukanCount = $activity->participants()->whereHas('screening', function ($q) {
            $q->where('risk_level', 'rujukan');
        })->count();

        return [
            Stat::make('Total Sasaran', $totalSasaran)
                ->description('Anak aktif di Posyandu')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Peserta Digenerate', $pesertaCount)
                ->color($pesertaCount < $totalSasaran ? 'warning' : 'success'),
            Stat::make('Kehadiran', $hadirCount)
                ->description($tidakHadirCount . ' Tidak Hadir')
                ->color('success'),
            Stat::make('Diskrining', $skriningCount)
                ->color('info'),
            Stat::make('Perlu Rujukan', $rujukanCount)
                ->color($rujukanCount > 0 ? 'danger' : 'gray'),
        ];
    }
}
