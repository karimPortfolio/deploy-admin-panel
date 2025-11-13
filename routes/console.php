<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('app:check-unused-resources')->weeklyOn(4, '11:00');
Schedule::command('app:sync-rds-database')->everyFiveMinutes();
Schedule::command('app:sync-rds-db-snapshot')->everyFiveMinutes();
Schedule::command('app:sync-server')->everyFiveMinutes();
