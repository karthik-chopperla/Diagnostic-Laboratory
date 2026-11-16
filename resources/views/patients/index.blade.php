@extends('layouts.app')
@section('title', 'Patient Management')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Patient Management & UHID Registry</h1>
        <p class="text-sm text-slate-500">Demographics, medical/clinical history, family linking, documents & duplicate merge.</p>
    </div>
    <form method="GET" class="flex gap-2">
        <input type="text" name="q" value="{{ $search }}" placeholder="Filter UHID, name, phone..." class="px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg">
        <button class="px-4 py-2 bg-slate-800 text-white text-sm font-semibold rounded-lg">Filter</button>
    </form>
</div>

@if($duplicates->isNotEmpty())
<div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm flex items-center justify-between">
    <div>
        <strong>Duplicate Patient Alert:</strong> {{ $duplicates->count() }} potential duplicate patient group(s) detected by Name + Phone. Open a patient profile to merge records.
    </div>
</div>
@endif

<div class="grid xl:grid-cols-3 gap-6">
    <!-- Registration Form -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Register New Patient</h2>
        <form method="POST" action="{{ route('patients.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Branch</label>
                    <select name="branch_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-slate-50">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Patient Type</label>
                    <select name="patient_type" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-slate-50">
                        <option>Walk-in</option>
                        <option>Registered</option>
                        <option>Corporate</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Full Name *</label>
                <input type="text" name="name" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Gender *</label>
                    <select name="gender" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Age *</label>
                    <input type="number" name="age" required min="0" max="120" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Blood Group</label>
                    <input type="text" name="blood_group" placeholder="O+" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Phone *</label>
                    <input type="text" name="phone" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">DOB</label>
                    <input type="date" name="dob" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Address & City</label>
                <input type="text" name="address" placeholder="Street, Area" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg mb-2">
                <input type="text" name="city" placeholder="City" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Emergency Contact</label>
                    <input type="text" name="emergency_contact_name" placeholder="Name" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Emergency Phone</label>
                    <input type="text" name="emergency_contact_phone" placeholder="Phone" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Allergies / Medical History</label>
                <textarea name="allergies" rows="2" placeholder="Known allergies, chronic conditions..." class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Corporate Client (Optional)</label>
                <select name="corporate_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    <option value="">None</option>
                    @foreach($corporates as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-xs">Register Patient & Generate UHID</button>
        </form>
    </div>

    <!-- Patient Directory -->
    <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Registered Patients</h2>
            <span class="text-xs text-slate-500">{{ $patients->total() }} total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">UHID</th>
                        <th class="px-4 py-3">Patient</th>
                        <th class="px-4 py-3">Contact</th>
                        <th class="px-4 py-3">Type / Branch</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($patients as $p)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-teal-700">{{ $p->uhid }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $p->name }}</div>
                                <div class="text-xs text-slate-500">{{ $p->age }} yrs · {{ $p->gender }} {{ $p->blood_group ? '· ' . $p->blood_group : '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $p->phone }}<br>{{ $p->city }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-slate-100 text-slate-700">{{ $p->patient_type }}</span>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $p->branch->name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('patients.show', $p) }}" class="px-3 py-1.5 rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-100 text-xs font-semibold">Profile & History</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $patients->links() }}</div>
    </div>
</div>
@endsection

