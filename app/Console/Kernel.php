<?php

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
    \Log::info('✅ Schedule() method loaded.');
    $schedule->command('app:check-unused-resources')->weeklyOn(4, '11:00');
    $schedule->command('app:sync-rds-database')->everyFiveMinutes();
    $schedule->call(function () {
      \Log::info('🕐 Schedule test ran successfully.');
    })->everyMinute();

  }

  /**
   * Register the commands for the application.
   */
  protected function commands(): void
  {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}