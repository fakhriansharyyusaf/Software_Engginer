<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Optional: uncomment to run overdue handling automatically every hour
// in a real deployment (requires `php artisan schedule:work` or a
// system cron calling `php artisan schedule:run` every minute).
// For the demo, use the Admin dashboard button or run the command
// manually: php artisan seapedia:check-overdue
Schedule::command('seapedia:check-overdue')->hourly();
