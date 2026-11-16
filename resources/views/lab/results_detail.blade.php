@extends('layouts.app')
@section('title', 'Order #ORD-' . $labOrder->id . ' — ' . $labOrder->test->name)

@section('content')
<!-- Top Navigation Bar -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('lab.results') }}" class="text-xs font-semibold text-slate-500 hover:text-teal-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Results List
            </a>
            <span class="text-slate-300">/</span>
            <span class="text-xs font-mono font-bold text-teal-700">#ORD-{{ $labOrder->id }}</span>
        </div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-slate-900">{{ $labOrder->test->name }}</h1>
            <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $labOrder->priority === 'STAT' ? 'bg-rose-100 text-rose-700' : ($labOrder->priority === 'Urgent' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                {{ $labOrder->priority }}
            </span>
            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ in_array($labOrder->status, ['Approved', 'Delivered']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-teal-50 text-teal-700 border border-teal-200' }}">
                {{ $labOrder->status }}
            </span>
        </div>
    </div>
    <div class="flex items-center gap-2">
        @if($labOrder->report)
            <a href="{{ route('lab.reports.show', $labOrder->report) }}"
               class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>View / Print Diagnostic Report</span>
            </a>
        @endif
    </div>
</div>

<!-- Critical Alert Banner if triggered -->
@if($labOrder->criticalAlerts->isNotEmpty())
    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900">
        <div class="flex items-center gap-2 font-bold text-sm text-rose-800 mb-1">
            <span class="w-2.5 h-2.5 rounded-full bg-rose-600 animate-pulse"></span>
            Critical Value Triggered for this Investigation
        </div>
        @foreach($labOrder->criticalAlerts as $alert)
            <div class="text-xs text-rose-700">
                <strong>{{ $alert->parameter_name }}:</strong> {{ $alert->result_value }} ({{ $alert->threshold_info }}) · Status: <span class="font-bold uppercase">{{ $alert->status }}</span>
                @if($alert->communication_notes) · Note: {{ $alert->communication_notes }} @endif
            </div>
        @endforeach
    </div>
@endif

<!-- Demographics & Clinical Information Summary Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
    <!-- Patient Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Patient Demographics</h2>
            <a href="{{ route('patients.show', $labOrder->patient) }}" class="text-xs font-semibold text-teal-600 hover:underline">Full Profile →</a>
        </div>
        <div class="space-y-1.5 text-xs text-slate-600">
            <div class="text-base font-bold text-slate-900">{{ $labOrder->patient->name }}</div>
            <div><span class="text-slate-400">UHID:</span> <span class="font-mono font-bold text-teal-700">{{ $labOrder->patient->uhid }}</span></div>
            <div><span class="text-slate-400">Age / Gender:</span> <strong>{{ $labOrder->patient->age }} Years / {{ $labOrder->patient->gender }}</strong></div>
            <div><span class="text-slate-400">Blood Group:</span> <strong>{{ $labOrder->patient->blood_group ?? 'N/A' }}</strong></div>
            <div><span class="text-slate-400">Phone:</span> {{ $labOrder->patient->phone }}</div>
            @if($labOrder->patient->allergies)
                <div class="pt-2 text-rose-700">
                    <span class="font-bold">Allergies:</span> {{ $labOrder->patient->allergies }}
                </div>
            @endif
        </div>
    </div>

    <!-- Specimen Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Specimen & Accession</h2>
            <span class="font-mono text-xs font-bold text-slate-700">{{ $labOrder->sample->barcode_value }}</span>
        </div>
        <div class="space-y-1.5 text-xs text-slate-600">
            <div><span class="text-slate-400">Sample ID:</span> <span class="font-mono font-bold text-teal-700">{{ $labOrder->sample->sample_id_code }}</span></div>
            <div><span class="text-slate-400">Accession No:</span> <span class="font-mono">{{ $labOrder->sample->accession_no }}</span></div>
            <div><span class="text-slate-400">Sample Type:</span> <strong>{{ $labOrder->sample->sample_type }}</strong></div>
            <div><span class="text-slate-400">Container:</span> {{ $labOrder->sample->container_type }} ({{ $labOrder->sample->quantity_ml }} mL)</div>
            <div><span class="text-slate-400">Storage Rack:</span> <span class="px-1.5 py-0.5 rounded bg-slate-100 font-medium">{{ $labOrder->sample->storage_location }}</span></div>
            <div><span class="text-slate-400">Collected At:</span> {{ optional($labOrder->sample->collected_at)->format('d M Y, h:i A') ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- Referral & Clinical Context -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Clinical Context & TAT</h2>
            <span class="text-xs font-medium text-slate-500">{{ $labOrder->department->name }}</span>
        </div>
        <div class="space-y-1.5 text-xs text-slate-600">
            <div><span class="text-slate-400">Referring Doctor:</span> <strong>{{ $labOrder->sample->doctor->name ?? 'Walk-in / Self' }}</strong></div>
            <div><span class="text-slate-400">Clinic / Hospital:</span> {{ $labOrder->sample->doctor->hospital_clinic ?? '-' }}</div>
            <div><span class="text-slate-400">Assigned Tech:</span> <strong>{{ $labOrder->technician->name ?? 'Unassigned' }}</strong></div>
            <div><span class="text-slate-400">Methodology:</span> {{ $labOrder->test->methodology }}</div>
            <div><span class="text-slate-400">TAT Due:</span> <strong class="text-amber-700">{{ optional($labOrder->tat_due_at)->format('d M Y, h:i A') ?? 'Standard' }}</strong></div>
            @if($labOrder->completed_at)
                <div><span class="text-slate-400">Completed At:</span> <strong class="text-emerald-700">{{ $labOrder->completed_at->format('d M Y, h:i A') }}</strong></div>
            @endif
        </div>
    </div>
</div>

<!-- Parameter Values Entry & Verification Card -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs mb-6">
    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Investigation Parameters & Observed Results</h2>
            <p class="text-xs text-slate-500 mt-0.5">Enter numerical or qualitative findings. Reference ranges adjust automatically based on patient gender ({{ $labOrder->patient->gender }}).</p>
        </div>
        <span class="text-xs font-bold text-teal-700">{{ $labOrder->test->parameters->count() }} Parameters Configured</span>
    </div>

    <form method="POST" action="{{ route('lab.results.save', $labOrder) }}" class="p-6">
        @csrf
        @if($labOrder->test->parameters->isNotEmpty())
            <div class="overflow-x-auto mb-6">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase text-slate-500 border-b border-slate-200 pb-3">
                        <tr>
                            <th class="py-2.5 pr-4">Parameter Name</th>
                            <th class="py-2.5 px-4">Observed Result</th>
                            <th class="py-2.5 px-4">Unit</th>
                            <th class="py-2.5 px-4">Reference Range</th>
                            <th class="py-2.5 px-4">Critical Thresholds</th>
                            <th class="py-2.5 px-4">Previous Value</th>
                            <th class="py-2.5 pl-4">Calculated Flag</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($labOrder->test->parameters as $param)
                            @php
                                $existing = $labOrder->results->firstWhere('test_parameter_id', $param->id) ?? $labOrder->results->firstWhere('parameter_name', $param->name);
                                $isFemale = $labOrder->patient->gender === 'Female';
                                $min = $isFemale ? ($param->ref_min_female ?? $param->ref_min_male) : $param->ref_min_male;
                                $max = $isFemale ? ($param->ref_max_female ?? $param->ref_max_male) : $param->ref_max_male;
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 pr-4 font-semibold text-slate-900">
                                    {{ $param->name }}
                                    <div class="text-[11px] font-mono text-slate-400">{{ $param->code }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-1.5">
                                        <input type="text" name="results[{{ $param->id }}]" value="{{ $existing->result_value ?? '' }}"
                                               placeholder="Enter value..."
                                               class="w-36 px-3.5 py-2 text-sm font-bold border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500">
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-xs font-medium text-slate-500">{{ $param->unit }}</td>
                                <td class="py-3 px-4 text-xs font-mono font-semibold text-slate-700">{{ $min }} - {{ $max }}</td>
                                <td class="py-3 px-4 text-xs text-slate-400">
                                    @if($param->critical_low || $param->critical_high)
                                        <span class="text-rose-600 font-semibold">&lt; {{ $param->critical_low ?? '—' }} | &gt; {{ $param->critical_high ?? '—' }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-xs text-slate-500 font-mono">{{ $existing->previous_value ?? '—' }}</td>
                                <td class="py-3 pl-4">
                                    @if($existing)
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ str_contains($existing->flag, 'Critical') ? 'bg-rose-100 text-rose-700 border border-rose-200' : ($existing->flag === 'Normal' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200') }}">
                                            {{ $existing->flag }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Not entered</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 rounded-xl bg-slate-50 text-xs text-slate-600 mb-6">
                This investigation does not have individual numerical parameters configured. Use qualitative remarks for reporting.
            </div>
        @endif

        <!-- Audit & Verification Footer -->
        <div class="pt-5 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="text-xs space-y-1 text-slate-500">
                @if($labOrder->verified_by)
                    <div>Verified by: <strong class="text-slate-800">{{ $labOrder->verified_by }}</strong> at {{ optional($labOrder->verified_at)->format('d M Y, h:i A') }}</div>
                @endif
                @if($labOrder->approved_by)
                    <div>Approved by: <strong class="text-teal-700">{{ $labOrder->approved_by }}</strong> at {{ optional($labOrder->approved_at)->format('d M Y, h:i A') }}</div>
                    <div>Digital Signature: <span class="font-mono text-slate-600">{{ $labOrder->pathologist_signature }}</span></div>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <button name="action_type" value="save"
                        class="px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 shadow-2xs transition cursor-pointer">
                    Save Draft
                </button>
                <button name="action_type" value="verify"
                        class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold shadow-2xs transition cursor-pointer">
                    Technician Verify
                </button>
                <button name="action_type" value="approve"
                        class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-xs transition cursor-pointer">
                    Pathologist Approve & Sign
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
