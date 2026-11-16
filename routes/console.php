<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('demo:seed-once', function () {
    if (Cache::get('demo-seed-completed-v1', false)) {
        $this->info('Demo database seeding already completed; skipping.');

        return 0;
    }

    $exitCode = $this->call('db:seed', ['--force' => true]);

    if ($exitCode !== 0) {
        return $exitCode;
    }

    Cache::forever('demo-seed-completed-v1', true);
    $this->info('Demo database seeding completed.');

    return 0;
})->purpose('Seed demo data once when explicitly enabled at startup');
