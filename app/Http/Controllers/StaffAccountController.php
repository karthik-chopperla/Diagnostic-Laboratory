<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LabAudit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffAccountController extends Controller
{
    private const APPROVABLE_ROLES = [
        'Lab Technician',
        'Front Desk & Billing',
        'Chief Pathologist',
    ];

    public function index()
    {
        $pendingUsers = User::query()
            ->where('role', 'Pending Approval')
            ->where('is_active', false)
            ->orderBy('created_at')
            ->get();

        return view('admin.staff-access', compact('pendingUsers'));
    }

    public function approve(Request $request, User $user)
    {
        $this->ensurePending($user);

        $data = $request->validate([
            'role' => ['required', Rule::in(self::APPROVABLE_ROLES)],
        ]);

        $user->update([
            'role' => $data['role'],
            'is_active' => true,
        ]);

        LabAudit::log(
            'Staff Account Approved',
            'Access Management',
            $user->email,
            "Approved as {$data['role']}",
            $request->user()->name
        );

        return back()->with('success', "Access approved for {$user->name} as {$data['role']}.");
    }

    public function reject(Request $request, User $user)
    {
        $this->ensurePending($user);

        LabAudit::log(
            'Staff Account Rejected',
            'Access Management',
            $user->email,
            'Pending staff access request rejected.',
            $request->user()->name
        );

        $user->delete();

        return back()->with('success', 'The staff access request was rejected.');
    }

    private function ensurePending(User $user): void
    {
        abort_unless($user->role === 'Pending Approval' && ! $user->is_active, 404);
    }
}
