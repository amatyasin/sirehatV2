<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StuntingChartWidget extends ChartWidget
{
    protected ?string $heading = 'Distribusi Status Stunting Siswa per Kecamatan';
    protected ?string $description = 'Perbandingan siswa Normal vs Pendek/Sangat Pendek berdasarkan wilayah';
    protected int | string | array $columnSpan = 2;
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return !$user->hasRole('petugas_posyandu');
    }

    protected function getData(): array
    {
        $user = auth()->user();

        $data = DB::table('pemeriksaan_gizis')
            ->join('student_class_histories', 'pemeriksaan_gizis.student_class_history_id', '=', 'student_class_histories.id')
            ->join('schools', 'student_class_histories.school_id', '=', 'schools.id')
            ->join('instansis', 'schools.instansi_id', '=', 'instansis.id')
            ->when(
                $user->hasRole('admin_kecamatan'),
                fn($q) => $q->where('schools.kecamatan_id', $user->kecamatan_id)
            )
            ->when(
                $user->hasRole('admin_instansi') || $user->hasRole('petugas_pemeriksaan'),
                fn($q) => $q->where('schools.instansi_id', $user->instansi_id)
            )
            ->when(
                $user->hasRole('admin_sekolah'),
                fn($q) => $q->where('student_class_histories.school_id', $user->school_id)
            )
            ->select(
                'instansis.nama_instansi',
                DB::raw("SUM(CASE WHEN pemeriksaan_gizis.status_anemia IN ('Normal') AND pemeriksaan_gizis.status_gizi NOT IN ('Sangat Kurus','Kurus') THEN 1 ELSE 0 END) as normal"),
                DB::raw("SUM(CASE WHEN pemeriksaan_gizis.status_gizi IN ('Sangat Kurus','Kurus') THEN 1 ELSE 0 END) as stunting")
            )
            ->groupBy('instansis.id', 'instansis.nama_instansi')
            ->orderBy('instansis.nama_instansi')
            ->get();

        // Simplify Puskesmas names
        $labels = $data->map(fn($d) => str_replace('Puskesmas ', '', $d->nama_instansi))->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Normal',
                    'data' => $data->pluck('normal')->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
                [
                    'label' => 'Gizi Kurang/Buruk',
                    'data' => $data->pluck('stunting')->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => ['maxRotation' => 45],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => 'rgba(0,0,0,0.05)'],
                ],
            ],
        ];
    }
}
