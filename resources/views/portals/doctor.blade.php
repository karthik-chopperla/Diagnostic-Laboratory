@extends('layouts.app')
@section('title', 'Doctor Portal')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Referring Doctor Portal</h1>
        <p class="text-sm text-slate-500">View referred patient reports, critical notifications & referral statistics.</p>
    </div>
    <form method="GET">
        <select name="doctor_id" onchange="this.form.submit()" class="px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg font-semibold">
            @foreach($doctors as $d)
                <option value="{{ $d->id }}" @selected($selectedDoctor && $selectedDoctor->id === $d->id)>{{ $d->name }} ({{ $d->specialization }})</option>
            @endforeach
        </select>
    </form>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex justify-between items-center">
        <h2 class="font-bold text-slate-900">Patient Reports Shared with {{ $selectedDoctor->name ?? 'Doctor' }}</h2>
        <span class="text-xs text-teal-700 font-semibold">{{ $reports->count() }} report(s)</span>
    </div>
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
            <tr>
                <th class="px-4 py-3">Report No</th>
                <th class="px-4 py-3">Patient</th>
                <th class="px-4 py-3">Investigation</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Download</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($reports as $rpt)
                <tr>
                    <td class="px-4 py-3 font-mono text-xs font-bold text-teal-700">{{ $rpt->report_no }}</td>
                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $rpt->patient->name }} ({{ $rpt->patient->uhid }})</td>
                    <td class="px-4 py-3">{{ $rpt->labOrder->test->name ?? '-' }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 text-xs rounded-full bg-emerald-50 text-emerald-700 font-semibold">{{ $rpt->status }}</span></td>
                    <td class="px-4 py-3"><a href="{{ route('lab.reports.show', $rpt) }}" class="px-3 py-1 bg-teal-600 text-white text-xs font-semibold rounded-lg">Open PDF Report</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-xs text-slate-400">No reports shared for this doctor yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

