@extends('layouts.app')
@section('title', 'Dashboard & Analytics')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Laboratory Command Center</h1>
        <p class="text-sm text-slate-500 mt-0.5">Real-time clinical operations, turnaround times, revenue & quality telemetry.</p>
    </div>
    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
        <select name="branch_id" onchange="this.form.submit()" class="px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500/30">
            <option value="">All Branches (Consolidated)</option>
            @foreach($branches as $b)
                <option value="{{ $b->id }}" @selected($branchId == $b->id)>{{ $b->name }}</option>
            @endforeach
        </select>
    </form>
</div>

<!-- Core Role-Tailored KPI Cards -->
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
    @php
        $kpis = [
            ['Total Patients', number_format($stats['total_patients']), 'Registered UHIDs', 'text-teal-700 bg-teal-50 border-teal-100', 'patients.index'],
            ["Today's Appointments", $stats['today_appointments'], 'Scheduled & Walk-in', 'text-sky-700 bg-sky-50 border-sky-100', 'appointments.index'],
            ["Today's Samples", $stats['today_samples'], 'Accessioned today', 'text-cyan-700 bg-cyan-50 border-cyan-100', 'samples.index'],
            ['Pending Investigations', $stats['pending_investigations'], 'Active lab worklist', 'text-amber-700 bg-amber-50 border-amber-100', 'lab.worklist'],
            ['Awaiting Approval', $stats['awaiting_approval'], 'Pathologist sign-off', 'text-indigo-700 bg-indigo-50 border-indigo-100', 'lab.results'],
            ['Reports Delivered', $stats['reports_delivered'], 'WhatsApp / Email / Portal', 'text-emerald-700 bg-emerald-50 border-emerald-100', 'lab.reports'],
            ['Critical Alerts', $stats['critical_alerts'], 'Immediate escalation', 'text-rose-700 bg-rose-50 border-rose-200', 'lab.critical'],
            ['Daily Revenue', '₹' . number_format($stats['daily_revenue'], 0), 'Today collections', 'text-teal-700 bg-teal-50 border-teal-100', 'billing.index'],
            ['Pending Payments', '₹' . number_format($stats['pending_payments'], 0), 'Retail dues', 'text-orange-700 bg-orange-50 border-orange-100', 'billing.index'],
            ['Corporate Outstanding', '₹' . number_format($stats['corporate_outstanding'], 0), 'B2B credit balance', 'text-slate-700 bg-slate-100 border-slate-200', 'network.corporates'],
        ];

        $visibleKpis = collect($kpis)->filter(function($k) {
            return auth()->user()?->canAccessRoute($k[4]);
        });
    @endphp
    @foreach($visibleKpis as [$label, $val, $sub, $tone, $routeName])
        <a href="{{ route($routeName) }}" class="bg-white rounded-xl border border-slate-200 p-4 hover:border-teal-300 hover:shadow-sm transition block">
            <div class="text-xs font-medium text-slate-500">{{ $label }}</div>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="text-2xl font-bold text-slate-900 tabular-nums">{{ $val }}</div>
                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full border {{ $tone }}">{{ $sub }}</span>
            </div>
        </a>
    @endforeach
</div>

<!-- Critical Alerts Banner (if accessible by role) -->
@if(auth()->user()?->canAccessRoute('lab.critical') && $openCriticalAlerts->isNotEmpty())
<div class="mb-6 bg-rose-50 border border-rose-200 rounded-xl p-4">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2 text-rose-800 font-semibold text-sm">
            <span class="w-2.5 h-2.5 rounded-full bg-rose-600 animate-ping"></span>
            Active Critical Value Alerts Requiring Clinical Communication
        </div>
        <a href="{{ route('lab.critical') }}" class="text-xs font-semibold text-rose-700 hover:underline">Manage All Alerts →</a>
    </div>
    <div class="grid md:grid-cols-2 gap-3">
        @foreach($openCriticalAlerts as $alert)
            <div class="bg-white rounded-lg border border-rose-200 p-3 flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-bold text-slate-900">{{ $alert->patient->name }} <span class="text-xs font-normal text-slate-500">({{ $alert->patient->uhid }})</span></div>
                    <div class="text-xs text-rose-700 font-semibold mt-0.5">{{ $alert->parameter_name }}: {{ $alert->result_value }} · {{ $alert->threshold_info }}</div>
                </div>
                <form method="POST" action="{{ route('lab.critical.ack', $alert) }}">
                    @csrf
                    <button class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold">Acknowledge</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Charts & Sample Rejection Row -->
<div class="grid lg:grid-cols-3 gap-6 mb-6">
    <!-- Revenue & Volume Chart -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">7-Day Operations & Test Volume</h2>
                <p class="text-xs text-slate-500">
                    {{ auth()->user()?->isTechnician() ? 'Daily investigations workload processed' : 'Daily collections (₹) vs investigations processed' }}
                </p>
            </div>
            <div class="flex items-center gap-4 text-xs font-medium">
                @if(!auth()->user()?->isTechnician())
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-teal-500"></span>Revenue</span>
                @endif
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-sky-500"></span>Test Volume</span>
            </div>
        </div>
        @php
            $maxRev = max(1, $chartDays->max('revenue'));
            $maxVol = max(1, $chartDays->max('volume'));
        @endphp
        <div class="grid grid-cols-7 gap-3 items-end h-44 pt-4 border-b border-slate-100 pb-2">
            @foreach($chartDays as $day)
                <div class="flex flex-col items-center gap-1 h-full justify-end">
                    <div class="w-full flex items-end justify-center gap-1.5 h-32">
                        @if(!auth()->user()?->isTechnician())
                            <div class="w-4 bg-teal-500/90 rounded-t transition-all" style="height: {{ max(8, round(($day['revenue'] / $maxRev) * 100)) }}%" title="₹{{ number_format($day['revenue']) }}"></div>
                        @endif
                        <div class="w-4 bg-sky-400/90 rounded-t transition-all" style="height: {{ max(8, round(($day['volume'] / $maxVol) * 100)) }}%" title="{{ $day['volume'] }} tests"></div>
                    </div>
                    <span class="text-[11px] font-medium text-slate-500">{{ $day['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Sample Rejection & Processing Stats -->
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h2 class="text-base font-bold text-slate-900 mb-1">Sample Quality Telemetry</h2>
        <p class="text-xs text-slate-500 mb-4">Pre-analytical sample rejection & completion status</p>
        @php
            $totS = max(1, $sampleStats['total']);
            $rejRate = round(($sampleStats['rejected'] / $totS) * 100, 1);
        @endphp
        <div class="space-y-3">
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 flex justify-between items-center">
                <span class="text-xs font-medium text-slate-600">Total Accessioned Samples</span>
                <span class="text-sm font-bold text-slate-900 tabular-nums">{{ $sampleStats['total'] }}</span>
            </div>
            <div class="p-3 rounded-lg bg-emerald-50/70 border border-emerald-100 flex justify-between items-center">
                <span class="text-xs font-medium text-emerald-800">Completed & Reported</span>
                <span class="text-sm font-bold text-emerald-700 tabular-nums">{{ $sampleStats['completed'] }}</span>
            </div>
            <div class="p-3 rounded-lg bg-sky-50/70 border border-sky-100 flex justify-between items-center">
                <span class="text-xs font-medium text-sky-800">In Processing / Transit</span>
                <span class="text-sm font-bold text-sky-700 tabular-nums">{{ $sampleStats['processing'] }}</span>
            </div>
            <div class="p-3 rounded-lg bg-rose-50/70 border border-rose-100 flex justify-between items-center">
                <span class="text-xs font-medium text-rose-800">Rejected Samples ({{ $rejRate }}%)</span>
                <span class="text-sm font-bold text-rose-700 tabular-nums">{{ $sampleStats['rejected'] }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Department & Machine / Front Desk Workload -->
<div class="grid lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-slate-900">Department-Wise Workload</h2>
            @if(auth()->user()?->canAccessRoute('lab.departments'))
                <a href="{{ route('lab.departments') }}" class="text-xs font-semibold text-teal-600 hover:underline">Open Dept Queues →</a>
            @endif
        </div>
        <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
            @foreach($departmentWorkload as $dept)
                <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                    <div>
                        <div class="text-sm font-semibold text-slate-800">{{ $dept->name }}</div>
                        <div class="text-[11px] text-slate-500">Head: {{ $dept->head_name }} · TAT {{ $dept->turn_around_hours }}h</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">{{ $dept->pending_count }} Pending</span>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-200/70 text-slate-700">{{ $dept->total_count }} Total</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if(auth()->user()?->canAccessRoute('ops.equipment'))
    <!-- Machine / Analyzer Workload (for Tech & Admin) -->
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-slate-900">Machine / Analyzer Workload</h2>
            <a href="{{ route('ops.equipment') }}" class="text-xs font-semibold text-teal-600 hover:underline">Manage Analyzers →</a>
        </div>
        <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
            @foreach($analyzers as $an)
                <div class="p-3 rounded-lg border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">{{ $an->name }} <span class="text-xs font-normal text-slate-500">({{ $an->equipment_code }})</span></div>
                        <div class="text-xs text-slate-500">{{ $an->department->name }} · {{ $an->interface_type }}</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $an->status === 'Online' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $an->status }}
                        </span>
                        <div class="text-xs font-bold text-slate-700 mt-1 tabular-nums">{{ number_format($an->total_tests_run) }} tests run</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @else
    <!-- Front Desk Quick Action Hub -->
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-slate-900">Front Desk Quick Actions</h2>
            <span class="text-xs font-medium text-slate-400">Reception & Operations</span>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <a href="{{ route('patients.index') }}" class="p-3 rounded-xl border border-slate-200 hover:border-teal-400 hover:bg-teal-50/40 transition group">
                <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm mb-2 group-hover:scale-105 transition">👤</div>
                <div class="text-xs font-bold text-slate-800">New Patient UHID</div>
                <div class="text-[11px] text-slate-500">Register OPD & walk-in</div>
            </a>
            <a href="{{ route('billing.index') }}" class="p-3 rounded-xl border border-slate-200 hover:border-teal-400 hover:bg-teal-50/40 transition group">
                <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm mb-2 group-hover:scale-105 transition">🧾</div>
                <div class="text-xs font-bold text-slate-800">Create Bill & Order</div>
                <div class="text-[11px] text-slate-500">Receipts & payments</div>
            </a>
            <a href="{{ route('appointments.index') }}" class="p-3 rounded-xl border border-slate-200 hover:border-teal-400 hover:bg-teal-50/40 transition group">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm mb-2 group-hover:scale-105 transition">📅</div>
                <div class="text-xs font-bold text-slate-800">Appointments</div>
                <div class="text-[11px] text-slate-500">Slot booking & scheduling</div>
            </a>
            <a href="{{ route('phlebotomy.index') }}" class="p-3 rounded-xl border border-slate-200 hover:border-teal-400 hover:bg-teal-50/40 transition group">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm mb-2 group-hover:scale-105 transition">🛵</div>
                <div class="text-xs font-bold text-slate-800">Home Phlebotomy</div>
                <div class="text-[11px] text-slate-500">Dispatch home collections</div>
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Recent Lab Orders -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-900">Recent Laboratory Worklist</h2>
        @if(auth()->user()?->canAccessRoute('lab.worklist'))
            <a href="{{ route('lab.worklist') }}" class="text-xs font-semibold text-teal-600 hover:underline">View Full Worklist →</a>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                <tr>
                    <th class="px-4 py-3">Sample / UHID</th>
                    <th class="px-4 py-3">Patient</th>
                    <th class="px-4 py-3">Investigation</th>
                    <th class="px-4 py-3">Department</th>
                    <th class="px-4 py-3">Priority</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($recentOrders as $ord)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-3 font-mono text-xs text-slate-700">{{ $ord->sample->sample_id_code ?? '-' }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $ord->patient->name }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $ord->test->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $ord->department->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $ord->priority === 'STAT' ? 'bg-rose-100 text-rose-700' : ($ord->priority === 'Urgent' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">{{ $ord->priority }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-teal-50 text-teal-700 border border-teal-200">{{ $ord->status }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
