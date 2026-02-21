<?php

// Add to app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Auto-cancel orders yang tidak dibayar dalam 12 jam
        // Cek setiap 30 menit
        $schedule->command('orders:cancel-expired')
            ->everyThirtyMinutes()
            ->withoutOverlapping()
            ->runInBackground();

        // Alternative: Cek setiap 15 menit untuk response lebih cepat
        // $schedule->command('orders:cancel-expired')->everyFifteenMinutes();

        // Alternative: Cek setiap jam
        // $schedule->command('orders:cancel-expired')->hourly();

        // Optional: Cleanup old notifications
        $schedule->call(function () {
            \App\Services\NotificationService::cleanupOldNotifications();
        })->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}