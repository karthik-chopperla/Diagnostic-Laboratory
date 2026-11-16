@extends('layouts.app')
@section('title', 'Result Entry & Verification')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Result Entry & Clinical Verification</h1>
        <p class="text-sm text-slate-500">Master investigation list — click any row to expand inline or open full patient details page.</p>
    </div>
</div>

<!-- Status Tabs -->
<div class="flex flex-wrap gap-2 mb-4">
    <a href="{{ route('lab.results') }}"
       class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition {{ !$status ? 'bg-teal-600 text-white border-teal-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
        All Orders ({{ $counts['all'] }})
    </a>
    <a href="{{ route('lab.results', ['status' => 'Pending']) }}"
       class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition {{ $status === 'Pending' ? 'bg-teal-600 text-white border-teal-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
        Pending ({{ $counts['pending'] }})
    </a>
    <a href="{{ route('lab.results', ['status' => 'Result Entered']) }}"
       class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition {{ $status === 'Result Entered' ? 'bg-teal-600 text-white border-teal-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
        Result Entered ({{ $counts['entered'] }})
    </a>
    <a href="{{ route('lab.results', ['status' => 'Verified']) }}"
       class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition {{ $status === 'Verified' ? 'bg-teal-600 text-white border-teal-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
        Verified ({{ $counts['verified'] }})
    </a>
    <a href="{{ route('lab.results', ['status' => 'Approved']) }}"
       class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition {{ $status === 'Approved' ? 'bg-teal-600 text-white border-teal-600 shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
        Approved / Delivered ({{ $counts['approved'] }})
    </a>
</div>

<!-- Search & Department Filters -->
<div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
    <form method="GET" action="{{ route('lab.results') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div class="sm:col-span-2">
            <input type="text" name="q" value="{{ $search }}" placeholder="Search by Patient name, UHID, Sample ID..."
                   class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500">
        </div>
        <div>
            <select name="department_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg">
                <option value="">All Departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" @selected($deptId == $d->id)>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <select name="priority" onchange="this.form.submit()" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg">
                <option value="">All Priorities</option>
                @foreach(['STAT', 'Urgent', 'Routine'] as $pr)
                    <option @selected($priority === $pr)>{{ $pr }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg shrink-0">Filter</button>
        </div>
    </form>
</div>

<!-- Orders List Table with Inline Expand -->
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 w-10"></th>
                    <th class="px-4 py-3">Order / Sample</th>
                    <th class="px-4 py-3">Patient Demographics</th>
                    <th class="px-4 py-3">Investigation</th>
                    <th class="px-4 py-3">Department</th>
                    <th class="px-4 py-3">Priority</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $ord)
                    @php
                        $totalParams = $ord->test->parameters->count();
                        $enteredParams = $ord->results->count();
                        $hasAbnormal = $ord->results->contains(fn($r) => $r->flag !== 'Normal');
                        $isApproved = in_array($ord->status, ['Approved', 'Delivered']);
                    @endphp
                    <!-- Master Row -->
                    <tr class="hover:bg-slate-50/70 transition cursor-pointer group" onclick="toggleExpand('expand-{{ $ord->id }}')">
                        <td class="px-4 py-3 text-center">
                            <button type="button" id="btn-expand-{{ $ord->id }}" class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-teal-50 group-hover:text-teal-700 text-slate-500 flex items-center justify-center transition">
                                <svg class="w-3.5 h-3.5 transition-transform" id="arrow-expand-{{ $ord->id }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs">
                            <span class="font-bold text-teal-700">#ORD-{{ $ord->id }}</span>
                            <div class="text-[11px] text-slate-500">{{ $ord->sample->sample_id_code }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900">{{ $ord->patient->name }}</div>
                            <div class="text-xs text-slate-500">{{ $ord->patient->uhid }} · {{ $ord->patient->age }}Y / {{ $ord->patient->gender }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-800">{{ $ord->test->name }}</div>
                            <div class="text-xs text-slate-500">{{ $totalParams > 0 ? "{$enteredParams}/{$totalParams} values recorded" : "Descriptive panel" }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600">
                            {{ $ord->department->name }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-[11px] font-bold rounded-full {{ $ord->priority === 'STAT' ? 'bg-rose-100 text-rose-700' : ($ord->priority === 'Urgent' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">
                                {{ $ord->priority }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $isApproved ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-teal-50 text-teal-700 border border-teal-200' }}">
                                    {{ $ord->status }}
                                </span>
                                @if($hasAbnormal)
                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-rose-100 text-rose-700">Abnormal</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('lab.results.detail', $ord) }}"
                                   class="px-3 py-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-semibold transition flex items-center gap-1">
                                    <span>Details Page</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                                @if($ord->report)
                                    <a href="{{ route('lab.reports.show', $ord->report) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Report</a>
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- Expandable Detail Row (Accordion) -->
                    <tr id="expand-{{ $ord->id }}" class="hidden bg-slate-50/80 border-t border-slate-100">
                        <td colspan="8" class="p-5">
                            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
                                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100 mb-4">
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">{{ $ord->test->name }} — Parameter Values Entry</h3>
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            Patient: <strong>{{ $ord->patient->name }}</strong> · Specimen: {{ $ord->sample->sample_type }} ({{ $ord->sample->container_type }})
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('lab.results.detail', $ord) }}" class="text-xs font-semibold text-teal-600 hover:underline">
                                            Open Full Details Page with Clinical History →
                                        </a>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('lab.results.save', $ord) }}">
                                    @csrf
                                    @if($ord->test->parameters->isNotEmpty())
                                        <div class="overflow-x-auto mb-4">
                                            <table class="w-full text-sm text-left">
                                                <thead class="text-xs text-slate-500 uppercase border-b border-slate-200 pb-2">
                                                    <tr>
                                                        <th class="py-2 pr-4">Parameter</th>
                                                        <th class="py-2 px-4">Observed Value</th>
                                                        <th class="py-2 px-4">Unit</th>
                                                        <th class="py-2 px-4">Reference Range</th>
                                                        <th class="py-2 px-4">Previous Value</th>
                                                        <th class="py-2 pl-4">Flag</th>
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
                                                            <td class="py-2.5 pr-4 font-semibold text-slate-900">{{ $param->name }}</td>
                                                            <td class="py-2.5 px-4">
                                                                <input type="text" name="results[{{ $param->id }}]" value="{{ $existing->result_value ?? '' }}"
                                                                       placeholder="Value..."
                                                                       class="w-32 px-3 py-1.5 text-sm font-bold border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500">
                                                            </td>
                                                            <td class="py-2.5 px-4 text-xs text-slate-500">{{ $param->unit }}</td>
                                                            <td class="py-2.5 px-4 text-xs text-slate-600 font-mono">{{ $min }} - {{ $max }}</td>
                                                            <td class="py-2.5 px-4 text-xs text-slate-400 tabular-nums">{{ $existing->previous_value ?? '-' }}</td>
                                                            <td class="py-2.5 pl-4">
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
                                        </div>
                                    @else
                                        <div class="p-4 rounded-lg bg-slate-50 text-xs text-slate-600 mb-4">
                                            No numerical parameters configured for this investigation. Use the Details Page for descriptive reporting.
                                        </div>
                                    @endif

                                    <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
                                        <div class="text-xs text-slate-500">
                                            @if($ord->pathologist_signature)
                                                Signed: <strong class="text-teal-700">{{ $ord->pathologist_signature }}</strong>
                                            @endif
                                        </div>
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-slate-400 text-xs">
                            No investigations found matching your filter criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100">{{ $orders->links() }}</div>
</div>

<script>
function toggleExpand(id) {
    const el = document.getElementById(id);
    const arrow = document.getElementById('arrow-' + id);
    if (el) {
        if (el.classList.contains('hidden')) {
            el.classList.remove('hidden');
            if (arrow) arrow.classList.add('rotate-180');
        } else {
            el.classList.add('hidden');
            if (arrow) arrow.classList.remove('rotate-180');
        }
    }
}
</script>
@endsection
