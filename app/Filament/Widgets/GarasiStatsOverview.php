<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GarasiStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        
        $childQuery = \App\Models\Child::query();
        $participantQuery = \App\Models\GarasiParticipant::query();
        $screeningQuery = \App\Models\GarasiScreening::query();
        $referralQuery = \App\Models\GarasiReferral::query();

        if (!$user->hasRole('super_admin') && !$user->hasRole('admin_dinkes')) {
            if ($user->hasRole('admin_kecamatan')) {
                $childQuery->whereHas('posyandu.kelurahan', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
                $participantQuery->whereHas('activity.posyandu.kelurahan', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
                $screeningQuery->whereHas('participant.activity.posyandu.kelurahan', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
                $referralQuery->whereHas('participant.activity.posyandu.kelurahan', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
            } elseif ($user->hasRole('admin_instansi')) {
                $childQuery->whereHas('posyandu', fn ($q) => $q->where('instansi_id', $user->instansi_id));
                $participantQuery->whereHas('activity', fn ($q) => $q->where('instansi_id', $user->instansi_id));
                $screeningQuery->whereHas('participant.activity', fn ($q) => $q->where('instansi_id', $user->instansi_id));
                $referralQuery->whereHas('participant.activity', fn ($q) => $q->where('instansi_id', $user->instansi_id));
            } elseif ($user->hasRole('petugas_posyandu')) {
                $childQuery->where('posyandu_id', $user->posyandu_id);
                $participantQuery->whereHas('activity', fn ($q) => $q->where('posyandu_id', $user->posyandu_id));
                $screeningQuery->whereHas('participant.activity', fn ($q) => $q->where('posyandu_id', $user->posyandu_id));
                $referralQuery->whereHas('participant.activity', fn ($q) => $q->where('posyandu_id', $user->posyandu_id));
            } else {
                $childQuery->whereRaw('1 = 0');
                $participantQuery->whereRaw('1 = 0');
                $screeningQuery->whereRaw('1 = 0');
                $referralQuery->whereRaw('1 = 0');
            }
        }

        $sasaranCount = $childQuery->count();
        $pesertaCount = (clone $participantQuery)->where('attendance', true)->count();
        $cakupan = $sasaranCount > 0 ? round(($pesertaCount / $sasaranCount) * 100, 1) : 0;
        
        $skriningCount = $screeningQuery->count();
        
        $risikoCount = (clone $screeningQuery)->where('risk_level', 'rujukan')->count();
        $rujukanSelesaiCount = (clone $referralQuery)->where('status', 'completed')->count();

        return [
            Stat::make('Total Sasaran', number_format($sasaranCount))
                ->description('Jumlah anak terdaftar di Posyandu')
                ->icon('heroicon-o-users'),
            Stat::make('Peserta Hadir', number_format($pesertaCount))
                ->description('Cakupan: ' . $cakupan . '%')
                ->icon('heroicon-o-user-group'),
            Stat::make('Telah Diskrining', number_format($skriningCount))
                ->description('Pemeriksaan gigi & mulut')
                ->icon('heroicon-o-clipboard-document-check'),
            Stat::make('Perlu Rujukan', number_format($risikoCount))
                ->description($rujukanSelesaiCount . ' Rujukan Selesai')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
