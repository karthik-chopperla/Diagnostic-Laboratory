<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'pathology',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
        \Illuminate\Support\Facades\DB::purge('mysql');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/billing');
        $response->assertRedirect('/login');
    }

    public function test_technician_cannot_access_billing_or_finance(): void
    {
        $tech = User::where('email', 'tech@patholab.local')->first();
        $this->actingAs($tech);

        // Should be blocked from billing
        $responseBilling = $this->get('/billing');
        $responseBilling->assertRedirect('/');
        $responseBilling->assertSessionHas('error');

        // Should be blocked from finance
        $responseFinance = $this->get('/finance');
        $responseFinance->assertRedirect('/');
        $responseFinance->assertSessionHas('error');

        // Should be allowed on departments and worklist
        $responseDepts = $this->get('/departments');
        $responseDepts->assertOk();

        $responseWorklist = $this->get('/worklist');
        $responseWorklist->assertOk();
    }

    public function test_front_desk_cannot_access_departments_or_equipment(): void
    {
        $desk = User::where('email', 'desk@patholab.local')->first();
        $this->actingAs($desk);

        // Should be blocked from departments
        $responseDepts = $this->get('/departments');
        $responseDepts->assertRedirect('/');
        $responseDepts->assertSessionHas('error');

        // Should be blocked from equipment
        $responseEquip = $this->get('/equipment');
        $responseEquip->assertRedirect('/');
        $responseEquip->assertSessionHas('error');

        // Should be allowed on billing and patients
        $responseBilling = $this->get('/billing');
        $responseBilling->assertOk();

        $responsePatients = $this->get('/patients');
        $responsePatients->assertOk();
    }

    public function test_admin_can_access_all_modules(): void
    {
        $admin = User::where('email', 'admin@patholab.local')->first();
        $this->actingAs($admin);

        $this->get('/billing')->assertOk();
        $this->get('/departments')->assertOk();
        $this->get('/finance')->assertOk();
        $this->get('/equipment')->assertOk();
        $this->get('/crm')->assertOk();
    }
}
