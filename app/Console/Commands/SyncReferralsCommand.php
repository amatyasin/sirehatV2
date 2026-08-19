<?php

namespace App\Console\Commands;

use App\Services\Referral\ReferralService;
use Illuminate\Console\Command;

class SyncReferralsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referral:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi seluruh data pemeriksaan kesehatan ke tabel rujukan';

    /**
     * Execute the console command.
     */
    public function handle(ReferralService $referralService): int
    {
        $this->info('Memulai sinkronisasi data rujukan...');

        $count = $referralService->syncAllExisting();

        $this->info("Sinkronisasi selesai! Total {$count} data rujukan berhasil disinkronkan.");

        return Command::SUCCESS;
    }
}
