@extends('layouts.app')
@section('title', 'Quality Control (QC) & QA')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Quality Control (QC), Levey-Jennings & CAPA</h1>
    <p class="text-sm text-slate-500">Internal QC runs, Westgard rule alerts (1-2s, 1-3s), Levey-Jennings chart & non-conformance CAPA.</p>
</div>

<!-- Levey-Jennings Chart -->
<div class="bg-white rounded-xl border border-slate-200 p-5 mb-6">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h2 class="text-base font-bold text-slate-900">Levey-Jennings QC Chart (Hemoglobin · XN-CHECK Level 2)</h2>
            <p class="text-xs text-slate-500">Z-Score plot across ±1SD, ±2SD (Warning), ±3SD (Westgard 1-3s Rejection)</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <svg viewBox="0 0 700 180" class="w-full max-w-4xl h-44 bg-slate-50/70 rounded-lg border border-slate-100">
            <!-- +3SD, +2SD, Mean, -2SD, -3SD lines -->
            <line x1="40" y1="20" x2="680" y2="20" stroke="#e11d48" stroke-dasharray="4 3" stroke-width="1"/>
            <text x="8" y="24" font-size="10" fill="#e11d48">+3SD</text>
            <line x1="40" y1="45" x2="680" y2="45" stroke="#d97706" stroke-dasharray="4 3" stroke-width="1"/>
            <text x="8" y="49" font-size="10" fill="#d97706">+2SD</text>
            <line x1="40" y1="90" x2="680" y2="90" stroke="#0d9488" stroke-width="1.5"/>
            <text x="8" y="94" font-size="10" fill="#0d9488">Mean</text>
            <line x1="40" y1="135" x2="680" y2="135" stroke="#d97706" stroke-dasharray="4 3" stroke-width="1"/>
            <text x="8" y="139" font-size="10" fill="#d97706">-2SD</text>
            <line x1="40" y1="160" x2="680" y2="160" stroke="#e11d48" stroke-dasharray="4 3" stroke-width="1"/>
            <text x="8" y="164" font-size="10" fill="#e11d48">-3SD</text>

            @php
                $pts = $qcRecords->values();
                $count = max(1, $pts->count());
                $coords = [];
                foreach ($pts as $i => $rec) {
                    $x = 65 + ($i * (590 / max(1, $count - 1)));
                    $y = 90 - ($rec->z_score * 22.5);
                    $y = max(15, min(165, $y));
                    $coords[] = ['x' => round($x, 1), 'y' => round($y, 1), 'rec' => $rec];
                }
                $polyline = collect($coords)->map(fn($c) => "{$c['x']},{$c['y']}")->implode(' ');
            @endphp
            @if(count($coords) > 1)
                <polyline fill="none" stroke="#0f766e" stroke-width="2" points="{{ $polyline }}" />
            @endif
            @foreach($coords as $c)
                <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="4.5"
                        fill="{{ $c['rec']->status === 'Accepted' ? '#0d9488' : ($c['rec']->status === 'Warning' ? '#d97706' : '#e11d48') }}"
                        stroke="#ffffff" stroke-width="1.5">
                    <title>Z={{ $c['rec']->z_score }} ({{ $c['rec']->westgard_rule }})</title>
                </circle>
            @endforeach
        </svg>
    </div>
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Record QC Run</h2>
        <form method="POST" action="{{ route('ops.qc.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <select name="branch_id" class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    @foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                </select>
                <select name="department_id" class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    @foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
                </select>
            </div>
            <select name="analyzer_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                @foreach($analyzers as $an)<option value="{{ $an->id }}">{{ $an->name }}</option>@endforeach
            </select>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="material_name" value="XN-CHECK Level 2" required class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
                <input type="text" name="lot_number" value="QC-L2-2026A" required class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <select name="control_level" class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    <option>Level 1</option><option selected>Level 2</option><option>Level 3</option>
                </select>
                <input type="text" name="parameter_name" value="Hemoglobin (Hb)" required class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div><label class="text-[11px] text-slate-500">Mean</label><input type="number" step="0.01" name="target_mean" value="14.0" class="w-full px-2 py-1.5 text-xs border border-slate-200 rounded-lg"></div>
                <div><label class="text-[11px] text-slate-500">1SD</label><input type="number" step="0.01" name="sd_value" value="0.3" class="w-full px-2 py-1.5 text-xs border border-slate-200 rounded-lg"></div>
                <div><label class="text-[11px] text-slate-500">Measured</label><input type="number" step="0.01" name="measured_value" value="14.1" class="w-full px-2 py-1.5 text-xs border border-slate-200 rounded-lg"></div>
            </div>
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Evaluate Westgard & Save</button>
        </form>
    </div>

    <div class="xl:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">Date / Analyzer</th>
                        <th class="px-4 py-3">Parameter & Lot</th>
                        <th class="px-4 py-3">Measured / Z</th>
                        <th class="px-4 py-3">Westgard</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($qcRecords->reverse()->take(8) as $qc)
                        <tr>
                            <td class="px-4 py-2.5 text-xs">
                                <div class="font-semibold text-slate-900">{{ $qc->analyzer->name ?? 'Manual' }}</div>
                                <div class="text-slate-500">{{ $qc->recorded_at->format('d M, h:i A') }}</div>
                            </td>
                            <td class="px-4 py-2.5 text-xs">{{ $qc->parameter_name }} ({{ $qc->control_level }})</td>
                            <td class="px-4 py-2.5 text-xs font-bold tabular-nums">{{ $qc->measured_value }} (Z: {{ $qc->z_score }})</td>
                            <td class="px-4 py-2.5">
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full {{ $qc->status === 'Accepted' ? 'bg-emerald-50 text-emerald-700' : ($qc->status === 'Warning' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-700') }}">
                                    {{ $qc->westgard_rule }} · {{ $qc->status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="text-base font-bold text-slate-900 mb-3">CAPA & Non-Conformance Incidents</h2>
            @foreach($capas as $cp)
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 text-xs space-y-1">
                    <div class="flex justify-between">
                        <strong class="font-mono text-teal-700">{{ $cp->incident_code }} · {{ $cp->title }}</strong>
                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-semibold">{{ $cp->status }}</span>
                    </div>
                    <div class="text-slate-600"><strong>Root Cause:</strong> {{ $cp->root_cause }}</div>
                    <div class="text-slate-600"><strong>Corrective Action:</strong> {{ $cp->corrective_action }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

