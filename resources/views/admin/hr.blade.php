@extends('layouts.app')
@section('title', 'HR & Employee Management')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">HR, Shifts, Attendance & Credentials</h1>
    <p class="text-sm text-slate-500">Pathologists, radiologists, technicians, phlebotomists, shift scheduling & qualification records.</p>
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Add Staff Member</h2>
        <form method="POST" action="{{ route('admin.hr.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <select name="branch_id" class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    @foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                </select>
                <select name="department_id" class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    <option value="">General</option>
                    @foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
                </select>
            </div>
            <input type="text" name="name" placeholder="Full Name *" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            <div class="grid grid-cols-2 gap-3">
                <select name="role" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    <option>Technician</option><option>Pathologist</option><option>Radiologist</option>
                    <option>Phlebotomist</option><option>Receptionist</option><option>Admin</option>
                </select>
                <input type="text" name="phone" placeholder="Phone *" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="qualification" placeholder="Qualification (MD / MLT)" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                <input type="text" name="license_no" placeholder="Reg / License No." class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <input type="text" name="shift" value="Morning (08:00 - 16:00)" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Add Employee</button>
        </form>
    </div>

    <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                <tr>
                    <th class="px-4 py-3">Staff</th>
                    <th class="px-4 py-3">Role & Dept</th>
                    <th class="px-4 py-3">Credentials</th>
                    <th class="px-4 py-3">Shift & Attendance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($employees as $emp)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900">{{ $emp->name }}</div>
                            <div class="text-xs font-mono text-teal-700">{{ $emp->employee_code }} · {{ $emp->phone }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <span class="px-2 py-0.5 rounded bg-sky-50 text-sky-700 font-semibold">{{ $emp->role }}</span>
                            <div class="text-slate-500 mt-0.5">{{ $emp->department->name ?? 'Administration' }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600">{{ $emp->qualification }}<br><span class="font-mono">{{ $emp->license_no }}</span></td>
                        <td class="px-4 py-3 text-xs">
                            <div>{{ $emp->shift }}</div>
                            <span class="text-emerald-700 font-semibold">{{ $emp->attendances->first()->status ?? 'Present' }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

