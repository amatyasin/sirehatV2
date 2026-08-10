<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class KesehatanDistribusiWidget extends ChartWidget
{
    protected ?string $heading = 'Ringkasan Indikator Kesehatan Siswa';
    protected ?string $description = 'Persentase kasus berdasarkan kategori pemeriksaan';
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $user = auth()->user();

        $baseQuery = fn() => DB::table('student_class_histories')
            ->join('schools', 'student_class_histories.school_id', '=', 'schools.id')
            ->when(
                $user->hasRole('admin_instansi') || $user->hasRole('petugas_pemeriksaan'),
                fn($q) => $q->where('schools.instansi_id', $user->instansi_id)
            )
            ->when(
                $user->hasRole('admin_sekolah'),
                fn($q) => $q->where('student_class_histories.school_id', $user->school_id)
            );

        $totalSiswa = DB::table('pemeriksaan_gizis')
            ->join('student_class_histories', 'pemeriksaan_gizis.student_class_history_id', '=', 'student_class_histories.id')
            ->join('schools', 'student_class_histories.school_id', '=', 'schools.id')
            ->when(
                $user->hasRole('admin_instansi') || $user->hasRole('petugas_pemeriksaan'),
                fn($q) => $q->where('schools.instansi_id', $user->instansi_id)
            )
            ->when(
                $user->hasRole('admin_sekolah'),
                fn($q) => $q->where('student_class_histories.school_id', $user->school_id)
            )
            ->count() ?: 1;

        $anemia = DB::table('pemeriksaan_gizis')
            ->join('student_class_histories', 'pemeriksaan_gizis.student_class_history_id', '=', 'student_class_histories.id')
            ->join('schools', 'student_class_histories.school_id', '=', 'schools.id')
            ->when(
                $user->hasRole('admin_instansi') || $user->hasRole('petugas_pemeriksaan'),
                fn($q) => $q->where('schools.instansi_id', $user->instansi_id)
            )
            ->when(
                $user->hasRole('admin_sekolah'),
                fn($q) => $q->where('student_class_histories.school_id', $user->school_id)
            )
            ->where('status_anemia', 'Anemia')
            ->count();

        $karies = DB::table('pemeriksaan_gigis')
            ->join('student_class_histories', 'pemeriksaan_gigis.student_class_history_id', '=', 'student_class_histories.id')
            ->join('schools', 'student_class_histories.school_id', '=', 'schools.id')
            ->when(
                $user->hasRole('admin_instansi') || $user->hasRole('petugas_pemeriksaan'),
                fn($q) => $q->where('schools.instansi_id', $user->instansi_id)
            )
            ->when(
                $user->hasRole('admin_sekolah'),
                fn($q) => $q->where('student_class_histories.school_id', $user->school_id)
            )
            ->where('gigi_berlubang', 'Y')
            ->count();

        $gangguan_mata = DB::table('pemeriksaan_matas')
            ->join('student_class_histories', 'pemeriksaan_matas.student_class_history_id', '=', 'student_class_histories.id')
            ->join('schools', 'student_class_histories.school_id', '=', 'schools.id')
            ->when(
                $user->hasRole('admin_instansi') || $user->hasRole('petugas_pemeriksaan'),
                fn($q) => $q->where('schools.instansi_id', $user->instansi_id)
            )
            ->when(
                $user->hasRole('admin_sekolah'),
                fn($q) => $q->where('student_class_histories.school_id', $user->school_id)
            )
            ->where('dirujuk_ke_fasyankes', 'Y')
            ->count();

        $sehat = $totalSiswa - $anemia - $karies - $gangguan_mata;
        $sehat = max(0, $sehat);

        return [
            'datasets' => [
                [
                    'data' => [$sehat, $anemia, $karies, $gangguan_mata],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.85)',
                        'rgba(239, 68, 68, 0.85)',
                        'rgba(245, 158, 11, 0.85)',
                        'rgba(99, 102, 241, 0.85)',
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                        'rgb(99, 102, 241)',
                    ],
                    'borderWidth' => 2,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => [
                'Sehat (' . number_format($sehat) . ')',
                'Anemia (' . number_format($anemia) . ')',
                'Karies Gigi (' . number_format($karies) . ')',
                'Gangguan Mata (' . number_format($gangguan_mata) . ')',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => ['padding' => 16, 'font' => ['size' => 12]],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
