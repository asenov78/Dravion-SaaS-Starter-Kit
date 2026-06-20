<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Check GitHub for new releases every 4 hours so the sidebar badge stays current.
Schedule::command('updates:check-releases')->everyFourHours();
