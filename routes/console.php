<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule incomplete booking reminders
// Runs every hour to check for bookings that ended but weren't marked as done
Schedule::command('bookings:send-incomplete-reminders --hours=1')
    ->hourly()
    ->description('Send reminders to users who have not marked their bookings as done');
