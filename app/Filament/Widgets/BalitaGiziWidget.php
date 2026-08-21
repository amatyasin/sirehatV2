<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BalitaGiziWidget extends ChartWidget
{
    protected ?string $heading = 'Tren Pertumbuhan Balita per Kelompok Usia';
    protected ?string $description = 'Rata-rata berat badan berdasarkan kelompok usia (bulan)';
    protected int | string | array $columnSpan = 2;
    protected static ?int $sort = 5;

    public static function canView(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return !$user->hasRole('admin_sekolah');
    }

    protected function getData(): array
    {
        $user = auth()->user();

        $ageGroups = [
            '0-6 bln'   => [0, 6],
            '7-12 bln'  => [7, 12],
            '13-18 bln' => [13, 18],
            '19-24 bln' => [19, 24],
            '25-36 bln' => [25, 36],
            '37-48 bln' => [37, 48],
            '49-60 bln' => [49, 60],
        ];

        $labels = array_keys($ageGroups);
        $bbLaki = [];
        $bbPerempuan = [];
        $stuntingRate = [];

        foreach ($ageGroups as $label => [$minAge, $maxAge]) {
            $baseQ = DB::table('pemeriksaan_balitas')
                ->join('children', 'pemeriksaan_balitas.child_id', '=', 'children.id')
                ->when(
                    $user->hasRole('admin_kecamatan'),
                    fn($q) => $q->whereExists(function ($sub) use ($user) {
                        $sub->select(DB::raw(1))
                            ->from('posyandus')
                            ->whereColumn('posyandus.id', 'children.posyandu_id')
                            ->where('posyandus.kecamatan_id', $user->kecamatan_id);
                    })
                )
                ->when(
                    $user->hasRole('admin_instansi') || $user->hasRole('petugas_pemeriksaan'),
                    fn($q) => $q->whereExists(function ($sub) use ($user) {
                        $sub->select(DB::raw(1))
                            ->from('posyandus')
                            ->whereColumn('posyandus.id', 'children.posyandu_id')
                            ->where('posyandus.instansi_id', $user->instansi_id);
                    })
                )
                ->when(
                    $user->hasRole('petugas_posyandu'),
                    function ($q) use ($user) {
                        if ($user->posyandu_id) {
                            $q->where('children.posyandu_id', $user->posyandu_id);
                        } elseif ($user->instansi_id) {
                            $q->whereExists(function ($sub) use ($user) {
                                $sub->select(DB::raw(1))
                                    ->from('posyandus')
                                    ->whereColumn('posyandus.id', 'children.posyandu_id')
                                    ->where('posyandus.instansi_id', $user->instansi_id);
                            });
                        }
                    }
                )
                ->whereBetween(
                    DB::raw('TIMESTAMPDIFF(MONTH, children.tanggal_lahir, pemeriksaan_balitas.tanggal_pemeriksaan)'),
                    [$minAge, $maxAge]
                );

            $laki = (clone $baseQ)->where('children.jenis_kelamin', 'L')->avg('pemeriksaan_balitas.berat_badan');
            $perempuan = (clone $baseQ)->where('children.jenis_kelamin', 'P')->avg('pemeriksaan_balitas.berat_badan');
            $total = (clone $baseQ)->count() ?: 1;
            $stunting = (clone $baseQ)->whereIn('pemeriksaan_balitas.status_stunting', ['Pendek', 'Sangat Pendek'])->count();

            $bbLaki[] = round($laki ?? 0, 2);
            $bbPerempuan[] = round($perempuan ?? 0, 2);
            $stuntingRate[] = round(($stunting / $total) * 100, 1);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Rata-rata BB Laki-laki (kg)',
                    'data' => $bbLaki,
                    'borderColor' => 'rgb(99, 102, 241)',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => true,
                    'pointBackgroundColor' => 'rgb(99, 102, 241)',
                    'pointRadius' => 5,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Rata-rata BB Perempuan (kg)',
                    'data' => $bbPerempuan,
                    'borderColor' => 'rgb(244, 114, 182)',
                    'backgroundColor' => 'rgba(244, 114, 182, 0.1)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => true,
                    'pointBackgroundColor' => 'rgb(244, 114, 182)',
                    'pointRadius' => 5,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => '% Stunting',
                    'data' => $stuntingRate,
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.05)',
                    'borderWidth' => 2,
                    'borderDash' => [5, 5],
                    'tension' => 0.3,
                    'pointBackgroundColor' => 'rgb(239, 68, 68)',
                    'pointRadius' => 4,
                    'yAxisID' => 'y1',
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'top'],
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => ['display' => true, 'text' => 'Berat Badan (kg)'],
                    'grid' => ['color' => 'rgba(0,0,0,0.05)'],
                    'beginAtZero' => true,
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => ['display' => true, 'text' => '% Stunting'],
                    'grid' => ['drawOnChartArea' => false],
                    'min' => 0,
                    'max' => 30,
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
        ];
    }
}
