@extends('layouts.app')
@section('title', 'Staff Access Requests')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Staff Access Requests</h1>
    <p class="text-sm text-slate-500">Review each request and assign an appropriate role before granting access.</p>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    @if($pendingUsers->isEmpty())
        <p class="px-5 py-8 text-sm text-slate-500 text-center">There are no pending staff access requests.</p>
    @else
        <div class="divide-y divide-slate-100">
            @foreach($pendingUsers as $pendingUser)
                <div class="p-5 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0">
                        <div class="font-semibold text-slate-900">{{ $pendingUser->name }}</div>
                        <div class="text-sm text-slate-500">{{ $pendingUser->email }}</div>
                        <div class="text-xs text-slate-400 mt-1">Requested {{ $pendingUser->created_at->diffForHumans() }}</div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <form method="POST" action="{{ route('admin.staff-access.approve', $pendingUser) }}" class="flex flex-col gap-2 sm:flex-row">
                            @csrf
                            @method('PATCH')
                            <label class="sr-only" for="role-{{ $pendingUser->id }}">Assign role to {{ $pendingUser->name }}</label>
                            <select id="role-{{ $pendingUser->id }}" name="role" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                                <option value="Lab Technician">Lab Technician</option>
                                <option value="Front Desk & Billing">Front Desk & Billing</option>
                                <option value="Chief Pathologist">Chief Pathologist</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">
                                Approve
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.staff-access.reject', $pendingUser) }}" onsubmit="return confirm('Reject this staff access request?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-white border border-rose-200 text-rose-700 hover:bg-rose-50 text-sm font-semibold rounded-lg">
                                Reject
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
