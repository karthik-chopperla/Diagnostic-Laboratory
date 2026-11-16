@extends('layouts.app')
@section('title', '11 Laboratory Departments')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Laboratory Department Dashboards</h1>
        <p class="text-sm text-slate-500">Department-specific work queues, test processing & direct patient value entry.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('lab.results') }}" class="px-3.5 py-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-xs transition">
            Open Full Result Verification View →
        </a>
    </div>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    @foreach($departments as $d)
        @php $isAct = $activeDept && $activeDept->id === $d->id; @endphp
        <a href="{{ route('lab.departments', ['dept' => $d->slug]) }}"
           class="p-3.5 rounded-xl border transition {{ $isAct ? 'bg-teal-600 text-white border-teal-600 shadow-sm' : 'bg-white text-slate-800 border-slate-200 hover:border-teal-300' }}">
            <div class="text-xs font-mono opacity-75">{{ $d->code }}</div>
            <div class="font-bold text-sm mt-0.5 truncate">{{ $d->name }}</div>
            <div class="mt-2 flex items-center justify-between text-[11px]">
                <span>{{ $d->pending_count }} Pending</span>
                <span>{{ $d->completed_count }} Done</span>
            </div>
        </a>
    @endforeach
</div>

@if($activeDept)
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <h2 class="text-base font-bold text-slate-900">{{ $activeDept->name }} Work Queue</h2>
            <p class="text-xs text-slate-500">{{ $activeDept->description }} · Head: {{ $activeDept->head_name }} · Standard TAT: {{ $activeDept->turn_around_hours }}h</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('lab.worklist', ['department_id' => $activeDept->id]) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Department Worklist →</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                <tr>
                    <th class="px-4 py-3">Sample / Order</th>
                    <th class="px-4 py-3">Patient</th>
                    <th class="px-4 py-3">Investigation</th>
                    <th class="px-4 py-3">Technician</th>
                    <th class="px-4 py-3">Priority</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Edit Patient Values</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $ord)
                    @php
                        $resultsCount = $ord->results->count();
                        $hasAbnormal = $ord->results->contains(fn($r) => $r->flag !== 'Normal');
                    @endphp
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-3 font-mono text-xs">
                            <span class="font-bold text-teal-700">#ORD-{{ $ord->id }}</span>
                            <div class="text-[11px] text-slate-500">{{ $ord->sample->sample_id_code }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">{{ $ord->patient->name }}</div>
                            <div class="text-xs text-slate-500">{{ $ord->patient->uhid }} · {{ $ord->patient->age }}Y / {{ $ord->patient->gender }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $ord->test->name }}</div>
                            <div class="text-xs text-slate-500">{{ $ord->test->parameters->count() }} parameters</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600">{{ $ord->technician->name ?? 'Unassigned' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $ord->priority === 'STAT' ? 'bg-rose-100 text-rose-700' : ($ord->priority === 'Urgent' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">{{ $ord->priority }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $ord->status === 'Approved' || $ord->status === 'Delivered' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-teal-50 text-teal-700 border border-teal-200' }}">{{ $ord->status }}</span>
                            @if($hasAbnormal)
                                <span class="ml-1 px-1.5 py-0.5 text-[10px] font-bold rounded bg-rose-100 text-rose-700">Abnormal</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button" onclick="document.getElementById('modal-ord-{{ $ord->id }}').classList.remove('hidden')"
                                        class="px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-xs transition cursor-pointer flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>{{ $resultsCount > 0 ? 'Edit Values' : 'Enter Values' }}</span>
                                </button>
                                @if($ord->report)
                                    <a href="{{ route('lab.reports.show', $ord->report) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Report</a>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- Modal for Entering/Editing Patient Values -->
                    <div id="modal-ord-{{ $ord->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden text-left">
                            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">{{ $ord->test->name }} — Patient Result Entry</h3>
                                    <div class="text-xs text-slate-500 mt-0.5">
                                        Patient: <strong class="text-slate-800">{{ $ord->patient->name }}</strong> ({{ $ord->patient->uhid }}) · Specimen: {{ $ord->sample->sample_type }}
                                    </div>
                                </div>
                                <button type="button" onclick="document.getElementById('modal-ord-{{ $ord->id }}').classList.add('hidden')"
                                        class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 text-base font-bold cursor-pointer">✕</button>
                            </div>

                            <form method="POST" action="{{ route('lab.results.save', $ord) }}" class="flex-1 overflow-y-auto p-6 space-y-4">
                                @csrf
                                @if($ord->test->parameters->isNotEmpty())
                                    <table class="w-full text-sm">
                                        <thead class="text-xs uppercase text-slate-500 border-b border-slate-200 pb-2">
                                            <tr>
                                                <th class="py-2 text-left">Parameter</th>
                                                <th class="py-2 text-left">Observed Value</th>
                                                <th class="py-2 text-left">Unit</th>
                                                <th class="py-2 text-left">Reference Range</th>
                                                <th class="py-2 text-left">Flag</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach($ord->test->parameters as $param)
                                                @php
                                                    $existing = $ord->results->firstWhere('test_parameter_id', $param->id) ?? $ord->results->firstWhere('parameter_name', $param->name);
                                                    $isFemale = $ord->patient->gender === 'Female';
                                                    $min = $isFemale ? ($param->ref_min_female ?? $param->ref_min_male) : $param->ref_min_male;
                                                    $max = $isFemale ? ($param->ref_max_female ?? $param->ref_max_male) : $param->ref_max_male;
                                                @endphp
                                                <tr>
                                                    <td class="py-2.5 font-semibold text-slate-900">{{ $param->name }}</td>
                                                    <td class="py-2.5">
                                                        <input type="text" name="results[{{ $param->id }}]" value="{{ $existing->result_value ?? '' }}"
                                                               placeholder="Value..."
                                                               class="w-28 px-3 py-1.5 text-sm font-bold border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500">
                                                    </td>
                                                    <td class="py-2.5 text-xs text-slate-500">{{ $param->unit }}</td>
                                                    <td class="py-2.5 text-xs text-slate-600 font-mono">{{ $min }} - {{ $max }}</td>
                                                    <td class="py-2.5">
                                                        @if($existing)
                                                            <span class="px-2 py-0.5 text-[11px] font-bold rounded {{ str_contains($existing->flag, 'Critical') ? 'bg-rose-100 text-rose-700' : ($existing->flag === 'Normal' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-100 text-amber-800') }}">
                                                                {{ $existing->flag }}
                                                            </span>
                                                        @else
                                                            <span class="text-xs text-slate-400">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="p-4 rounded-lg bg-slate-50 text-xs text-slate-600">
                                        This investigation does not have individual numerical parameters configured. Use the Result Verification tab for qualitative or narrative notes.
                                    </div>
                                @endif

                                <div class="pt-4 border-t border-slate-200 flex items-center justify-between gap-2">
                                    <button type="button" onclick="document.getElementById('modal-ord-{{ $ord->id }}').classList.add('hidden')"
                                            class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-600 cursor-pointer">
                                        Cancel
                                    </button>
                                    <div class="flex items-center gap-2">
                                        <button name="action_type" value="save" class="px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 cursor-pointer">
                                            Save Draft
                                        </button>
                                        <button name="action_type" value="verify" class="px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold cursor-pointer">
                                            Technician Verify
                                        </button>
                                        <button name="action_type" value="approve" class="px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-xs cursor-pointer">
                                            Pathologist Approve & Sign
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-xs text-slate-400">No active orders in {{ $activeDept->name }} queue.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
