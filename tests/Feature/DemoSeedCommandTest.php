<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DemoSeedCommandTest extends TestCase
{
    public function test_demo_seed_command_skips_after_completion_marker_is_set(): void
    {
        Cache::forever('demo-seed-completed-v1', true);

        $this->artisan('demo:seed-once')
            ->expectsOutput('Demo database seeding already completed; skipping.')
            ->assertSuccessful();
    }
}
