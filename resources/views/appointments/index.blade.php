@extends('layouts.app')
@section('title', 'Appointment Management')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Appointment & Slot Management</h1>
        <p class="text-sm text-slate-500">Online booking, walk-in registration, home collection slots & SMS/WhatsApp confirmations.</p>
    </div>
    <div class="flex gap-2">
        @foreach(['', 'Online', 'Walk-in', 'Home Collection'] as $t)
            <a href="{{ route('appointments.index', ['type' => $t]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold border {{ $type === $t ? 'bg-teal-600 text-white border-teal-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                {{ $t ?: 'All Types' }}
            </a>
        @endforeach
    </div>
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Book Appointment</h2>
        <form method="POST" action="{{ route('appointments.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Patient *</label>
                <select name="patient_id" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->uhid }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Branch</label>
                    <select name="branch_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Booking Type</label>
                    <select name="appointment_type" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option>Online</option>
                        <option>Walk-in</option>
                        <option>Home Collection</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Referring Doctor</label>
                    <select name="doctor_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option value="">Self / Walk-in</option>
                        @foreach($doctors as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Department</label>
                    <select name="department_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option value="">General</option>
                        @foreach($departments as $dep)
                            <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Primary Test</label>
                <select name="test_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    <option value="">Consultation / Multiple</option>
                    @foreach($tests as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} (₹{{ $t->price }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Date & Time *</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Slot</label>
                    <input type="text" name="time_slot" value="09:00 AM - 09:30 AM" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
            </div>
            <div class="flex items-center gap-4 text-xs text-slate-600 pt-1">
                <label class="flex items-center gap-1.5"><input type="checkbox" name="send_sms" value="1" checked class="rounded text-teal-600"> Send SMS</label>
                <label class="flex items-center gap-1.5"><input type="checkbox" name="send_whatsapp" value="1" checked class="rounded text-teal-600"> Send WhatsApp</label>
            </div>
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Confirm Appointment</button>
        </form>
    </div>

    <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Appointment Schedule</h2>
            <span class="text-xs text-slate-500">{{ $appointments->total() }} appointments</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">Apt No / Slot</th>
                        <th class="px-4 py-3">Patient</th>
                        <th class="px-4 py-3">Type / Dept</th>
                        <th class="px-4 py-3">Alerts</th>
                        <th class="px-4 py-3">Status & Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($appointments as $apt)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <div class="font-mono text-xs font-bold text-teal-700">{{ $apt->appointment_no }}</div>
                                <div class="text-xs text-slate-500">{{ $apt->scheduled_at->format('d M, h:i A') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $apt->patient->name }}</div>
                                <div class="text-xs text-slate-500">{{ $apt->doctor->name ?? 'Walk-in' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-sky-50 text-sky-700">{{ $apt->appointment_type }}</span>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $apt->test->name ?? ($apt->department->name ?? 'General') }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($apt->sms_sent)<span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 mr-1">SMS</span>@endif
                                @if($apt->whatsapp_sent)<span class="px-1.5 py-0.5 rounded bg-teal-50 text-teal-700">WhatsApp</span>@endif
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('appointments.status', $apt) }}" class="flex items-center gap-1.5">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="px-2 py-1 text-xs border border-slate-200 rounded-lg bg-white font-medium">
                                        @foreach(['Scheduled','Confirmed','Checked-in','Completed','Rescheduled','Cancelled'] as $st)
                                            <option @selected($apt->status === $st)>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $appointments->links() }}</div>
    </div>
</div>
@endsection

