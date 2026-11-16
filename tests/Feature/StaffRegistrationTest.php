<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_is_available_to_guests(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Submit Access Request');
    }

    public function test_staff_registration_creates_an_inactive_request(): void
    {
        $this->post('/register', [
            'name' => 'New Staff Member',
            'email' => ' NEWSTAFF@example.com ',
            'password' => 'a-strong-password-123',
            'password_confirmation' => 'a-strong-password-123',
            'role' => 'Chief Pathologist',
        ])->assertRedirect('/login')
            ->assertSessionHas('success');

        $user = User::where('email', 'newstaff@example.com')->firstOrFail();

        $this->assertSame('Pending Approval', $user->role);
        $this->assertFalse($user->is_active);

        $this->post('/login', [
            'email' => 'newstaff@example.com',
            'password' => 'a-strong-password-123',
        ])->assertSessionHasErrors('email');
    }

    public function test_administrator_can_approve_a_pending_account_with_a_role(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => 'admin-password',
            'role' => 'Chief Pathologist',
            'is_active' => true,
        ]);
        $pendingUser = User::create([
            'name' => 'Pending Staff',
            'email' => 'pending@example.com',
            'password' => 'staff-password',
            'role' => 'Pending Approval',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.staff-access'))
            ->assertOk()
            ->assertSee('pending@example.com');

        $this->patch(route('admin.staff-access.approve', $pendingUser), ['role' => 'Super Admin'])
            ->assertSessionHasErrors('role');

        $this->patch(route('admin.staff-access.approve', $pendingUser), ['role' => 'Lab Technician'])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $pendingUser->id,
            'role' => 'Lab Technician',
            'is_active' => true,
        ]);
    }

    public function test_non_administrator_cannot_approve_a_pending_account(): void
    {
        $technician = User::create([
            'name' => 'Technician',
            'email' => 'tech@example.com',
            'password' => 'technician-password',
            'role' => 'Lab Technician',
            'is_active' => true,
        ]);
        $pendingUser = User::create([
            'name' => 'Pending Staff',
            'email' => 'pending@example.com',
            'password' => 'staff-password',
            'role' => 'Pending Approval',
            'is_active' => false,
        ]);

        $this->actingAs($technician)
            ->patch(route('admin.staff-access.approve', $pendingUser), ['role' => 'Chief Pathologist'])
            ->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'id' => $pendingUser->id,
            'role' => 'Pending Approval',
            'is_active' => false,
        ]);
    }
}
