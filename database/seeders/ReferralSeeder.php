<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentClassHistory;
use App\Models\PemeriksaanTelinga;
use App\Services\Referral\ReferralService;

class ReferralSeeder extends Seeder
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Ear checkups for some students
        $histories = StudentClassHistory::all();

        if ($histories->isEmpty()) {
            $this->command->warn("No student class history found. Seed students first.");
            return;
        }

        foreach ($histories as $index => $history) {
            // Seed ear checkups for about 40% of students
            if ($index % 2 === 0) {
                $dirujuk = $index % 6 === 0 ? 'Y' : 'N';
                $gangguanKanan = $index % 8 === 0 ? 'Y' : 'N';
                $gangguanKiri = $index % 10 === 0 ? 'Y' : 'N';

                PemeriksaanTelinga::firstOrCreate(
                    ['student_class_history_id' => $history->id],
                    [
                        'tanggal_pemeriksaan' => now()->subDays(rand(1, 90))->toDateString(),
                        'telinga_luar_kanan' => $dirujuk === 'Y' ? 'Infeksi' : 'Bersih',
                        'telinga_luar_kiri' => $gangguanKiri === 'Y' ? 'Serumen' : 'Bersih',
                        'gangguan_pendengaran_kanan' => $gangguanKanan,
                        'gangguan_pendengaran_kiri' => $gangguanKiri,
                        'serumen_kanan' => $gangguanKanan === 'Y' ? 'Y' : 'N',
                        'serumen_kiri' => $gangguanKiri === 'Y' ? 'Y' : 'N',
                        'dirujuk_ke_fasyankes' => $dirujuk,
                        'keterangan_rujukan' => $dirujuk === 'Y' ? 'Infeksi telinga luar' : null,
                    ]
                );
            }
        }

        // 2. Call service layer to compile and sync referrals from ALL examinations (Gizi, Gigi, Mata, Telinga, Umum)
        $syncedCount = $this->referralService->syncAllExisting();
        
        $this->command->info("Successfully synced {$syncedCount} referrals from historical examinations.");
    }
}
