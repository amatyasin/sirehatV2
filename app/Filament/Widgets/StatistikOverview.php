<?php

namespace App\Filament\Widgets;

use App\Models\Child;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanGigi;
use App\Models\PemeriksaanGizi;
use App\Models\PemeriksaanMata;
use App\Models\PemeriksaanUmum;
use App\Models\Posyandu;
use App\Models\PosyanduMonthlyExamination;
use App\Models\PosyanduMonthlyParticipant;
use App\Models\Referral;
use App\Models\School;
use App\Models\StudentClassHistory;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatistikOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $user = auth()->user();

        $studentQuery = StudentClassHistory::query()->where('aktif', true);
        $childQuery = Child::query()->where('aktif', true);
        $schoolQuery = School::query();
        $posyanduQuery = Posyandu::query();
        $gigiQuery = PemeriksaanGigi::query();
        $giziQuery = PemeriksaanGizi::query();
        $mataQuery = PemeriksaanMata::query();
        $umumQuery = PemeriksaanUmum::query();
        $balitaQuery = PemeriksaanBalita::query();
        $referralQuery = Referral::query();

        $monthlyExamQuery = PosyanduMonthlyExamination::query();
        $monthlyPartQuery = PosyanduMonthlyParticipant::query();

        if ($user->hasRole('admin_kecamatan')) {
            $studentQuery->whereHas('school', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
            $childQuery->whereHas('posyandu', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
            $schoolQuery->where('kecamatan_id', $user->kecamatan_id);
            $posyanduQuery->where('kecamatan_id', $user->kecamatan_id);
            foreach ([$gigiQuery, $giziQuery, $mataQuery, $umumQuery] as $q) {
                $q->whereHas('studentClassHistory.school', fn ($sq) => $sq->where('kecamatan_id', $user->kecamatan_id));
            }
            $balitaQuery->whereHas('child.posyandu', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
            $referralQuery->whereHas('studentClassHistory.school', fn ($sq) => $sq->where('kecamatan_id', $user->kecamatan_id));
            $monthlyExamQuery->whereHas('posyandu.kelurahan', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
            $monthlyPartQuery->whereHas('examination.posyandu.kelurahan', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
        } elseif ($user->hasRole('admin_instansi') || $user->hasRole('petugas_pemeriksaan')) {
            $studentQuery->whereHas('school', fn ($q) => $q->where('instansi_id', $user->instansi_id));
            $childQuery->whereHas('posyandu', fn ($q) => $q->where('instansi_id', $user->instansi_id));
            $schoolQuery->where('instansi_id', $user->instansi_id);
            $posyanduQuery->where('instansi_id', $user->instansi_id);
            foreach ([$gigiQuery, $giziQuery, $mataQuery, $umumQuery] as $q) {
                $q->whereHas('studentClassHistory.school', fn ($sq) => $sq->where('instansi_id', $user->instansi_id));
            }
            $balitaQuery->whereHas('child.posyandu', fn ($q) => $q->where('instansi_id', $user->instansi_id));
            $referralQuery->whereHas('studentClassHistory.school', fn ($sq) => $sq->where('instansi_id', $user->instansi_id));
            $monthlyExamQuery->whereHas('posyandu', fn ($q) => $q->where('instansi_id', $user->instansi_id));
            $monthlyPartQuery->whereHas('examination.posyandu', fn ($q) => $q->where('instansi_id', $user->instansi_id));
        } elseif ($user->hasRole('admin_sekolah')) {
            $studentQuery->where('school_id', $user->school_id);
            $schoolQuery->where('id', $user->school_id);
            foreach ([$gigiQuery, $giziQuery, $mataQuery, $umumQuery] as $q) {
                $q->whereHas('studentClassHistory', fn ($sq) => $sq->where('school_id', $user->school_id));
            }
            $referralQuery->whereHas('studentClassHistory', fn ($sq) => $sq->where('school_id', $user->school_id));
        } elseif ($user->hasRole('petugas_posyandu')) {
            if ($user->posyandu_id) {
                $childQuery->where('posyandu_id', $user->posyandu_id);
                $posyanduQuery->where('id', $user->posyandu_id);
                $balitaQuery->whereHas('child', fn ($q) => $q->where('posyandu_id', $user->posyandu_id));
                $monthlyExamQuery->where('posyandu_id', $user->posyandu_id);
                $monthlyPartQuery->whereHas('examination', fn ($q) => $q->where('posyandu_id', $user->posyandu_id));
            } elseif ($user->instansi_id) {
                $childQuery->whereHas('posyandu', fn ($q) => $q->where('instansi_id', $user->instansi_id));
                $posyanduQuery->where('instansi_id', $user->instansi_id);
                $balitaQuery->whereHas('child.posyandu', fn ($q) => $q->where('instansi_id', $user->instansi_id));
                $monthlyExamQuery->whereHas('posyandu', fn ($q) => $q->where('instansi_id', $user->instansi_id));
                $monthlyPartQuery->whereHas('examination.posyandu', fn ($q) => $q->where('instansi_id', $user->instansi_id));
            }
        }

        $totalSiswa = $studentQuery->count();
        $totalAnak = $childQuery->count();
        $totalSekolah = $schoolQuery->count();
        $totalPosyandu = $posyanduQuery->count();
        $totalGigi = $gigiQuery->count();
        $totalGizi = $giziQuery->count();
        $totalMata = $mataQuery->count();
        $totalUmum = $umumQuery->count();
        $totalBalita = $balitaQuery->count();
        $totalReferral = $referralQuery->count();
        $belumDirujuk = (clone $referralQuery)->where('status_rujukan', 'Belum Dirujuk')->count();
        $prosesRujukan = (clone $referralQuery)->whereIn('status_rujukan', ['Sudah Dirujuk', 'Dalam Tindak Lanjut'])->count();
        $selesaiRujukan = (clone $referralQuery)->where('status_rujukan', 'Selesai')->count();

        // Monthly Examination Stats
        $monthlyExamThisMonth = (clone $monthlyExamQuery)->where('month', now()->month)->where('year', now()->year)->count();
        $monthlyExamDone = (clone $monthlyPartQuery)->whereHas('examination', fn ($q) => $q->where('month', now()->month)->where('year', now()->year))->where('examination_status', 'Sudah Diperiksa')->count();
        $tbIndicated = (clone $monthlyPartQuery)->whereHas('examination', fn ($q) => $q->where('month', now()->month)->where('year', now()->year))->where('tb_screening_result', 'Terindikasi')->count();

        // Health indicator rates
        $anemiaCount = (clone $giziQuery)->where('status_anemia', 'Anemia')->count();
        $kariesCount = (clone $gigiQuery)->where('gigi_berlubang', 'Y')->count();
        $mataRefCount = (clone $mataQuery)->where('dirujuk_ke_fasyankes', 'Y')->count();
        $stuntingSiswa = (clone $giziQuery)->whereIn('status_gizi', ['Sangat Kurus', 'Kurus'])->count();
        $stuntingBalita = (clone $balitaQuery)->whereIn('status_stunting', ['Pendek', 'Sangat Pendek'])->count();

        $anemiaRate = $totalGizi > 0 ? round(($anemiaCount / $totalGizi) * 100, 1) : 0;
        $kariesRate = $totalGigi > 0 ? round(($kariesCount / $totalGigi) * 100, 1) : 0;
        $mataRate = $totalMata > 0 ? round(($mataRefCount / $totalMata) * 100, 1) : 0;
        $stuntingSRate = $totalGizi > 0 ? round(($stuntingSiswa / $totalGizi) * 100, 1) : 0;
        $stuntingBRate = $totalBalita > 0 ? round(($stuntingBalita / $totalBalita) * 100, 1) : 0;

        // Role-specific Stat cards presentation
        if ($user->hasRole('admin_sekolah')) {
            return [
                Stat::make('Total Siswa', number_format($totalSiswa))
                    ->description('Siswa aktif terdaftar')
                    ->icon('heroicon-o-user-group')
                    ->color('primary'),

                Stat::make('Total Rujukan', number_format($totalReferral))
                    ->description($belumDirujuk.' belum diproses / '.$selesaiRujukan.' selesai')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('danger'),

                Stat::make('Sudah Diperiksa', number_format($totalUmum))
                    ->description('Siswa telah menjalani pemeriksaan')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('info'),

                Stat::make('Tingkat Stunting Siswa', $stuntingSRate.'%')
                    ->description($stuntingSiswa.' siswa kurus/sangat kurus')
                    ->icon('heroicon-o-chart-bar-square')
                    ->color($stuntingSRate > 10 ? 'danger' : 'warning'),

                Stat::make('Anemia Siswi', $anemiaRate.'%')
                    ->description($anemiaCount.' siswi terdeteksi anemia')
                    ->icon('heroicon-o-beaker')
                    ->color($anemiaRate > 15 ? 'danger' : 'warning'),

                Stat::make('Karies Gigi', $kariesRate.'%')
                    ->description($kariesCount.' siswa dengan gigi berlubang')
                    ->icon('heroicon-o-face-smile')
                    ->color($kariesRate > 25 ? 'danger' : 'warning'),

                Stat::make('Gangguan Mata', $mataRate.'%')
                    ->description($mataRefCount.' siswa dirujuk')
                    ->icon('heroicon-o-eye')
                    ->color($mataRate > 20 ? 'danger' : 'info'),
            ];
        }

        if ($user->hasRole('petugas_posyandu')) {
            return [
                Stat::make('Total Balita', number_format($totalAnak))
                    ->description('Anak Posyandu aktif')
                    ->icon('heroicon-o-heart')
                    ->color('rose'),

                Stat::make('Pemeriksaan Bulanan (Bulan Ini)', number_format($monthlyExamDone))
                    ->description($monthlyExamThisMonth.' Sesi Posyandu | '.$tbIndicated.' Terindikasi TBC')
                    ->icon('heroicon-o-calendar-days')
                    ->color('info'),

                Stat::make('Posyandu', number_format($totalPosyandu))
                    ->description('Posyandu terdaftar')
                    ->icon('heroicon-o-building-library')
                    ->color('success'),

                Stat::make('Stunting Balita', $stuntingBRate.'%')
                    ->description($stuntingBalita.' balita pendek/sangat pendek')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color($stuntingBRate > 10 ? 'danger' : 'warning'),
            ];
        }

        return [
            Stat::make('Total Siswa', number_format($totalSiswa))
                ->description('Siswa aktif terdaftar')
                ->icon('heroicon-o-user-group')
                ->color('primary')
                ->chart([60, 75, 80, 70, 90, 85, $totalSiswa / 100]),

            Stat::make('Total Balita', number_format($totalAnak))
                ->description('Anak Posyandu aktif')
                ->icon('heroicon-o-heart')
                ->color('rose')
                ->chart([30, 45, 40, 55, 50, 60, $totalAnak / 60]),

            Stat::make('Sekolah', number_format($totalSekolah))
                ->description($totalPosyandu.' Posyandu')
                ->icon('heroicon-o-building-library')
                ->color('success'),

            Stat::make('Pemeriksaan Bulanan Posyandu', number_format($monthlyExamDone))
                ->description($monthlyExamThisMonth.' Sesi Posyandu bulan ini')
                ->icon('heroicon-o-calendar-days')
                ->color('info'),

            Stat::make('Total Rujukan', number_format($totalReferral))
                ->description($belumDirujuk.' belum diproses / '.$selesaiRujukan.' selesai')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('danger')
                ->chart([$belumDirujuk, $prosesRujukan, $selesaiRujukan, $totalReferral]),

            Stat::make('Sudah Diperiksa', number_format($totalUmum))
                ->description('Siswa telah menjalani pemeriksaan umum')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info'),

            Stat::make('Tingkat Stunting Siswa', $stuntingSRate.'%')
                ->description($stuntingSiswa.' siswa kurus/sangat kurus')
                ->icon('heroicon-o-chart-bar-square')
                ->color($stuntingSRate > 10 ? 'danger' : 'warning'),

            Stat::make('Stunting Balita', $stuntingBRate.'%')
                ->description($stuntingBalita.' balita pendek/sangat pendek')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($stuntingBRate > 10 ? 'danger' : 'warning'),

            Stat::make('Anemia Siswi', $anemiaRate.'%')
                ->description($anemiaCount.' siswi terdeteksi anemia')
                ->icon('heroicon-o-beaker')
                ->color($anemiaRate > 15 ? 'danger' : 'warning'),

            Stat::make('Karies Gigi', $kariesRate.'%')
                ->description($kariesCount.' siswa dengan gigi berlubang')
                ->icon('heroicon-o-face-smile')
                ->color($kariesRate > 25 ? 'danger' : 'warning'),

            Stat::make('Gangguan Mata', $mataRate.'%')
                ->description($mataRefCount.' siswa dirujuk')
                ->icon('heroicon-o-eye')
                ->color($mataRate > 20 ? 'danger' : 'info'),
        ];
    }
}