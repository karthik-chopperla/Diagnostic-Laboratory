<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    public function test_role_switch_endpoint_is_not_available(): void
    {
        $this->assertFalse(Route::has('switch.role'));
        $this->post('/switch-role', ['email' => 'admin@patholab.local'])->assertNotFound();
    }

    public function test_login_page_does_not_disclose_demo_credentials(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertDontSee('Demo Staff Accounts')
            ->assertDontSee('admin@patholab.local')
            ->assertDontSee('Password:');
    }

    public function test_demo_seeder_rejects_reused_passwords(): void
    {
        config([
            'app.demo_seed_passwords' => [
                'admin' => 'reused-demo-password',
                'technician' => 'reused-demo-password',
                'front_desk' => 'reused-demo-password',
            ],
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Set three unique demo passwords of at least 16 characters before seeding.');

        app(DatabaseSeeder::class)->run();
    }

    public function test_demo_seeder_rejects_short_passwords(): void
    {
        config([
            'app.demo_seed_passwords' => [
                'admin' => 'short',
                'technician' => 'another-short',
                'front_desk' => 'third-short',
            ],
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Set three unique demo passwords of at least 16 characters before seeding.');

        app(DatabaseSeeder::class)->run();
    }
}
