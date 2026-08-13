<?php

namespace App\Jobs;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job ini dijalankan via ->chain() setelah Excel::queue() selesai.
 * Mengirimkan notifikasi Filament ke user dengan link download file.
 */
class NotifyUserExportReady implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $userId,
        protected string $filename,
        protected string $disk = 'public'
    ) {}

    public function handle(): void
    {
        $user = User::find($this->userId);

        if (! $user) {
            return;
        }

        $url = asset('storage/' . $this->filename);

        Notification::make()
            ->title('Export Rekap Pemeriksaan Selesai')
            ->body('File Excel siap diunduh.')
            ->success()
            ->actions([
                \Filament\Notifications\Actions\Action::make('download')
                    ->label('Download Excel')
                    ->url($url)
                    ->openUrlInNewTab()
                    ->button(),
            ])
            ->sendToDatabase($user);
    }
}
