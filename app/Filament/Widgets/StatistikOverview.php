<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Posyandu;
use App\Models\School;
use App\Models\Child;
use App\Models\PemeriksaanGigi;
use App\Models\PemeriksaanGizi;
use App\Models\PemeriksaanMata;
use App\Models\PemeriksaanUmum;
use App\Models\StudentClassHistory;

class StatistikOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user =
            auth()->user();
        $studentQuery =
            StudentClassHistory::query()
                ->where('aktif', true);
        $childQuery =
            Child::query()
                ->where('aktif', true);
        $schoolQuery =
            School::query();
        $posyanduQuery =
            Posyandu::query();
        $gigiQuery =
            PemeriksaanGigi::query();
        $giziQuery =
            PemeriksaanGizi::query();
        $mataQuery =
            PemeriksaanMata::query();
        $umumQuery =
            PemeriksaanUmum::query();

        if (
            $user->hasRole('admin_instansi')
            || $user->hasRole('petugas_pemeriksaan')
        ) {
            $studentQuery->whereHas(
                'school',
                fn ($q) =>
                    $q->where(
                        'instansi_id',
                        $user->instansi_id));
            $childQuery->whereHas(
                'posyandu',
                fn ($q) =>
                    $q->where(
                        'instansi_id',
                        $user->instansi_id));
            $schoolQuery->where(
                'instansi_id',
                $user->instansi_id);
            $posyanduQuery->where(
                'instansi_id',
                $user->instansi_id);
            $gigiQuery->whereHas(
                'studentClassHistory.school',
                fn ($q) =>
                    $q->where(
                        'instansi_id',
                        $user->instansi_id));
            $giziQuery->whereHas(
                'studentClassHistory.school',
                fn ($q) =>
                    $q->where(
                        'instansi_id',
                        $user->instansi_id));
            $mataQuery->whereHas(
                'studentClassHistory.school',
                fn ($q) =>
                    $q->where(
                        'instansi_id',
                        $user->instansi_id));
            $umumQuery->whereHas(
                'studentClassHistory.school',
                fn ($q) =>
                    $q->where(
                        'instansi_id',
                        $user->instansi_id));
        }
        if (
            $user->hasRole('admin_sekolah')
        ) {
            $studentQuery->where(
                'school_id',
                $user->school_id);
            $schoolQuery->where(
                'id',
                $user->school_id);
            $gigiQuery->whereHas(
                'studentClassHistory',
                fn ($q) =>
                    $q->where(
                        'school_id',
                        $user->school_id));
            $giziQuery->whereHas(
                'studentClassHistory',
                fn ($q) =>
                    $q->where(
                        'school_id',
                        $user->school_id));
            $mataQuery->whereHas(
                'studentClassHistory',
                fn ($q) =>
                    $q->where(
                        'school_id',
                        $user->school_id));
            $umumQuery->whereHas(
                'studentClassHistory',
                fn ($q) =>
                    $q->where(
                        'school_id',
                        $user->school_id));
        }
        $totalSiswa =
            $studentQuery->count();
        $totalAnak =
            $childQuery->count();
        $totalSekolah =
            $schoolQuery->count();
        $totalPosyandu =
            $posyanduQuery->count();
        $totalGigi =
            $gigiQuery->count();
        $totalGizi =
            $giziQuery->count();
        $totalMata =
            $mataQuery->count();
        $totalUmum =
            $umumQuery->count();

        return [
            Stat::make(
                'Total Siswa',
                number_format($totalSiswa))
                ->description(
                    'Siswa aktif')
                ->icon(
                    'heroicon-o-user-group')
                ->color(
                    'primary'),
            Stat::make(
                'Total Anak',
                number_format($totalAnak))
                ->description(
                    'Anak aktif')
                ->icon(
                    'heroicon-o-user-group')
                ->color(
                    'primary'),
            Stat::make(
                'Total Sekolah',
                number_format($totalSekolah))
                ->description(
                    'Sekolah terdaftar')
                ->icon(
                    'heroicon-o-building-library')
                ->color(
                    'success'),
            Stat::make(
                'Total Posyandu',
                number_format($totalPosyandu))
                ->description(
                    'Posyandu terdaftar')
                ->icon(
                    'heroicon-o-home')
                ->color(
                    'warning'),
            Stat::make(
                'Pemeriksaan Umum',
                number_format($totalUmum))
                ->icon(
                    'heroicon-o-heart')
                ->color(
                    'danger'),
            Stat::make(
                'Pemeriksaan Gigi',
                number_format($totalGigi))
                ->icon(
                    'heroicon-o-face-smile')
                ->color(
                    'warning'),
            Stat::make(
                'Pemeriksaan Mata',
                number_format($totalMata))
                ->icon(
                    'heroicon-o-eye')
                ->color(
                    'info'),
            Stat::make(
                'Pemeriksaan Gizi',
                number_format($totalGizi))
                ->icon(
                    'heroicon-o-scale')
                ->color(
                    'success'),
        ];
    }
}