<?php

use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

// Schedule commands to run at specified intervals
Schedule::command('queue:work --stop-when-empty --sleep=3 --timeout=60')
    ->everyMinute()
    ->withoutOverlapping(1)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/queue-work.log'));

Schedule::command('owlet:sync-orders')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/sync-owlet-orders.log'));

Schedule::command('exchange-rate:update')
    ->everyTenMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/exchange-rate-update.log'));

Schedule::command('getatext:sync-services')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/getatext-sync-services.log'));

Schedule::command('orders:auto-cancel')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/auto-cancel-orders.log'));

Schedule::command('logs:clear')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/logs-clear.log'));
