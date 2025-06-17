<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Commands yang akan diregistrasi
     */
    protected $commands = [
        Commands\SendEmails::class,
        Commands\ProcessPayments::class,
        Commands\CleanupFiles::class,
    ];

    /**
     * Mendefinisikan command schedule
     */
    protected function schedule(Schedule $schedule): void
    {
        // Auto sync tagihan status daily at 02:00
        $schedule->command('tagihan:sync-terjadwal')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/tagihan-sync.log'));

        // Auto sync tagihan bulanan status daily at 02:30
        $schedule->command('tagihan:sync-bulanan')
            ->dailyAt('02:30')
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/tagihan-sync.log'));
    }

    /**
     * Register commands untuk Artisan
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
