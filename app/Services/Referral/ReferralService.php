<?php

namespace App\Services\Referral;

use App\Models\Referral;
use App\Repositories\Referral\ReferralRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ReferralService
{
    protected ReferralRepositoryInterface $referralRepository;

    public function __construct(ReferralRepositoryInterface $referralRepository)
    {
        $this->referralRepository = $referralRepository;
    }

    /**
     * Get paginated list of referrals.
     */
    public function getPaginatedReferrals(array $filters, int $perPage = 15)
    {
        return $this->referralRepository->getPaginated($filters, $perPage);
    }

    /**
     * Get unpaginated list of referrals.
     */
    public function getReferralsList(array $filters)
    {
        return $this->referralRepository->getList($filters);
    }

    /**
     * Get detailed referral with history logs.
     */
    public function getReferralDetail(int $id)
    {
        return $this->referralRepository->findById($id);
    }

    /**
     * Get school-level referral summaries.
     */
    public function getSchoolRecap(array $filters)
    {
        return $this->referralRepository->getRecapBySchool($filters);
    }

    /**
     * Get class-level referral summaries.
     */
    public function getClassRecap(array $filters)
    {
        return $this->referralRepository->getRecapByClass($filters);
    }

    /**
     * Get dashboard-level analytics statistics.
     */
    public function getStats(array $filters)
    {
        return $this->referralRepository->getDashboardStats($filters);
    }

    /**
     * Determine if a referral is needed and provide the reason.
     */
    public function determineReferralNeeded($pemeriksaan): array
    {
        $needed = false;
        $reasons = [];

        if ($pemeriksaan instanceof \App\Models\PemeriksaanGizi) {
            if ($pemeriksaan->dirujuk_ke_fasyankes === 'Y') {
                $needed = true;
                $reasons[] = 'Dirujuk oleh pemeriksa: ' . ($pemeriksaan->keterangan_rujukan ?: 'Pemeriksaan Gizi');
            }
            if (in_array($pemeriksaan->status_gizi, ['Sangat Kurus', 'Kurus', 'Obesitas', 'Gizi Buruk', 'Gizi Kurang', 'Gizi Lebih'])) {
                $needed = true;
                $reasons[] = 'Status Gizi ' . $pemeriksaan->status_gizi;
            }
            if ($pemeriksaan->status_anemia === 'Anemia' || $pemeriksaan->tanda_klinis_anemia === 'Y') {
                $needed = true;
                $reasons[] = 'Tanda klinis atau status Anemia';
            }
        } elseif ($pemeriksaan instanceof \App\Models\PemeriksaanGigi) {
            if ($pemeriksaan->dirujuk_ke_fasyankes === 'Y') {
                $needed = true;
                $reasons[] = 'Dirujuk oleh pemeriksa: ' . ($pemeriksaan->keterangan_rujukan ?: 'Pemeriksaan Gigi');
            }
            if ($pemeriksaan->gigi_berlubang === 'Y' || ($pemeriksaan->jumlah_gigi_berlubang !== null && $pemeriksaan->jumlah_gigi_berlubang > 0)) {
                $needed = true;
                $reasons[] = 'Gigi berlubang';
            }
            if ($pemeriksaan->gusi_berdarah === 'Y' || $pemeriksaan->gusi_bengkak === 'Y') {
                $needed = true;
                $reasons[] = 'Kelainan gusi';
            }
        } elseif ($pemeriksaan instanceof \App\Models\PemeriksaanMata) {
            if ($pemeriksaan->dirujuk_ke_fasyankes === 'Y') {
                $needed = true;
                $reasons[] = 'Dirujuk oleh pemeriksa: ' . ($pemeriksaan->keterangan_rujukan ?: 'Pemeriksaan Mata');
            }
            if ($pemeriksaan->mata_merah === 'Y' || $pemeriksaan->buta_warna === 'Y' || $pemeriksaan->mata_berair === 'Y' || $pemeriksaan->nyeri_mata === 'Y' || $pemeriksaan->mata_bengkak === 'Y' || $pemeriksaan->mata_belekan === 'Y') {
                $needed = true;
                $reasons[] = 'Temuan kelainan mata';
            }
            if (($pemeriksaan->visus_kanan && $pemeriksaan->visus_kanan !== '6/6' && $pemeriksaan->visus_kanan !== '-') ||
                ($pemeriksaan->visus_kiri && $pemeriksaan->visus_kiri !== '6/6' && $pemeriksaan->visus_kiri !== '-')) {
                $needed = true;
                $reasons[] = 'Gangguan visus penglihatan';
            }
        } elseif ($pemeriksaan instanceof \App\Models\PemeriksaanTelinga) {
            if ($pemeriksaan->dirujuk_ke_fasyankes === 'Y') {
                $needed = true;
                $reasons[] = 'Dirujuk oleh pemeriksa: ' . ($pemeriksaan->keterangan_rujukan ?: 'Pemeriksaan Telinga');
            }
            if ($pemeriksaan->gangguan_pendengaran_kanan === 'Y' || $pemeriksaan->gangguan_pendengaran_kiri === 'Y') {
                $needed = true;
                $reasons[] = 'Gangguan pendengaran';
            }
        } elseif ($pemeriksaan instanceof \App\Models\PemeriksaanUmum) {
            if ($pemeriksaan->dirujuk_ke_fasyankes === 'Y') {
                $needed = true;
                $reasons[] = 'Dirujuk oleh pemeriksa: ' . ($pemeriksaan->keterangan_rujukan ?: 'Pemeriksaan Umum');
            }
            
            // Check blood pressure
            if ($pemeriksaan->tekanan_darah && preg_match('/^(\d+)\s*\/\s*(\d+)$/', $pemeriksaan->tekanan_darah, $matches)) {
                $systolic = (int) $matches[1];
                $diastolic = (int) $matches[2];
                if ($systolic < 90 || $systolic > 120 || $diastolic < 60 || $diastolic > 80) {
                    $needed = true;
                    $reasons[] = "Tekanan darah tidak normal ({$pemeriksaan->tekanan_darah})";
                }
            }

            // Check clinical findings
            $findings = [];
            if ($pemeriksaan->bising_jantung === 'Y' || $pemeriksaan->bising_jantung === 'abnormal') $findings[] = 'bising jantung';
            if ($pemeriksaan->bising_paru === 'Y' || $pemeriksaan->bising_paru === 'abnormal') $findings[] = 'bising paru';
            if ($pemeriksaan->bercak_putih_mati_rasa === 'Y') $findings[] = 'bercak putih mati rasa';
            if ($pemeriksaan->kulit_ada_luka_sayatan === 'Y') $findings[] = 'luka sayatan kulit';
            if ($pemeriksaan->bekas_suntikan === 'Y') $findings[] = 'bekas suntikan abnormal';
            if ($pemeriksaan->luka_koreng_sukar_sembuh === 'Y') $findings[] = 'luka koreng sukar sembuh';

            if (!empty($findings)) {
                $needed = true;
                $reasons[] = 'Temuan klinis: ' . implode(', ', $findings);
            }
        }

        return [
            'needed' => $needed,
            'reason' => implode('; ', $reasons)
        ];
    }

    /**
     * Sync a checkup model to the referrals table.
     */
    public function syncReferral($pemeriksaan): ?Referral
    {
        $result = $this->determineReferralNeeded($pemeriksaan);
        
        $pemeriksaanClass = get_class($pemeriksaan);
        $referral = Referral::where('pemeriksaan_type', $pemeriksaanClass)
            ->where('pemeriksaan_id', $pemeriksaan->id)
            ->first();

        if ($result['needed']) {
            $jenisPemeriksaan = $this->getJenisPemeriksaan($pemeriksaanClass);
            
            if (!$referral) {
                $referral = new Referral();
                $referral->status_rujukan = 'Belum Dirujuk';
            }

            $referral->student_class_history_id = $pemeriksaan->student_class_history_id;
            $referral->pemeriksaan_type = $pemeriksaanClass;
            $referral->pemeriksaan_id = $pemeriksaan->id;
            $referral->jenis_pemeriksaan = $jenisPemeriksaan;
            $referral->alasan_rujukan = $result['reason'];
            $referral->tanggal_pemeriksaan = $pemeriksaan->tanggal_pemeriksaan;
            $referral->petugas_pemeriksa = $pemeriksaan->petugas_pemeriksa ?? 'Petugas Kesehatan';

            $this->referralRepository->save($referral);
            return $referral;
        } else {
            if ($referral && $referral->status_rujukan === 'Belum Dirujuk') {
                $referral->delete();
            }
            return null;
        }
    }

    /**
     * Update referral status and log to history.
     */
    public function updateStatus(int $referralId, string $newStatus, ?string $catatan, int $userId): Referral
    {
        return DB::transaction(function () use ($referralId, $newStatus, $catatan, $userId) {
            $referral = $this->referralRepository->findById($referralId);
            if (!$referral) {
                throw new \Exception("Referral not found");
            }

            $oldStatus = $referral->status_rujukan;
            $referral->status_rujukan = $newStatus;
            
            if ($newStatus === 'Sudah Dirujuk' && !$referral->tanggal_rujukan) {
                $referral->tanggal_rujukan = now()->toDateString();
            }
            
            if ($catatan) {
                $referral->catatan_tindak_lanjut = $catatan;
            }

            $this->referralRepository->save($referral);

            $history = new \App\Models\ReferralStatusHistory([
                'referral_id' => $referral->id,
                'status_lama' => $oldStatus,
                'status_baru' => $newStatus,
                'user_id' => $userId,
                'catatan' => $catatan,
            ]);
            $history->save();

            return $referral;
        });
    }

    /**
     * Sync all existing health checkups to referrals table.
     */
    public function syncAllExisting(): int
    {
        $count = 0;

        \App\Models\PemeriksaanGizi::chunk(100, function ($records) use (&$count) {
            foreach ($records as $record) {
                if ($this->syncReferral($record)) {
                    $count++;
                }
            }
        });

        \App\Models\PemeriksaanGigi::chunk(100, function ($records) use (&$count) {
            foreach ($records as $record) {
                if ($this->syncReferral($record)) {
                    $count++;
                }
            }
        });

        \App\Models\PemeriksaanMata::chunk(100, function ($records) use (&$count) {
            foreach ($records as $record) {
                if ($this->syncReferral($record)) {
                    $count++;
                }
            }
        });

        \App\Models\PemeriksaanTelinga::chunk(100, function ($records) use (&$count) {
            foreach ($records as $record) {
                if ($this->syncReferral($record)) {
                    $count++;
                }
            }
        });

        \App\Models\PemeriksaanUmum::chunk(100, function ($records) use (&$count) {
            foreach ($records as $record) {
                if ($this->syncReferral($record)) {
                    $count++;
                }
            }
        });

        return $count;
    }

    private function getJenisPemeriksaan(string $class): string
    {
        switch ($class) {
            case \App\Models\PemeriksaanGizi::class:
                return 'Gizi';
            case \App\Models\PemeriksaanGigi::class:
                return 'Gigi';
            case \App\Models\PemeriksaanMata::class:
                return 'Mata';
            case \App\Models\PemeriksaanTelinga::class:
                return 'Telinga';
            case \App\Models\PemeriksaanUmum::class:
                return 'Umum';
            default:
                return 'Umum';
        }
    }
}
