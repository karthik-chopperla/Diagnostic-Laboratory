@extends('layouts.app')
@section('title', 'Patient Portal')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Patient Self-Service Portal</h1>
        <p class="text-sm text-slate-500">Track sample status, view invoices, download reports & manage family profiles.</p>
    </div>
    <form method="GET">
        <select name="patient_id" onchange="this.form.submit()" class="px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg font-semibold">
            @foreach($patients as $p)
                <option value="{{ $p->id }}" @selected($selectedPatient && $selectedPatient->id === $p->id)>{{ $p->name }} ({{ $p->uhid }})</option>
            @endforeach
        </select>
    </form>
</div>

@if($selectedPatient)
<div class="grid lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h2 class="font-bold text-slate-900 mb-3">My Diagnostic Reports</h2>
        @forelse($selectedPatient->labReports as $rpt)
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 mb-2 flex justify-between items-center">
                <div>
                    <div class="font-mono text-xs font-bold text-teal-700">{{ $rpt->report_no }}</div>
                    <div class="text-xs text-slate-600">{{ $rpt->labOrder->test->name ?? 'Report' }}</div>
                </div>
                <a href="{{ route('lab.reports.show', $rpt) }}" class="px-2.5 py-1 bg-teal-600 text-white text-xs font-semibold rounded">Download</a>
            </div>
        @empty
            <p class="text-xs text-slate-400">No reports available yet.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h2 class="font-bold text-slate-900 mb-3">Sample Tracking</h2>
        @forelse($selectedPatient->samples as $s)
            <div class="p-3 rounded-lg border border-slate-100 mb-2 text-xs">
                <div class="flex justify-between">
                    <strong class="font-mono text-teal-700">{{ $s->sample_id_code }}</strong>
                    <span class="px-2 py-0.5 rounded-full bg-teal-50 text-teal-700 font-semibold">{{ $s->status }}</span>
                </div>
                <div class="text-slate-500 mt-1">{{ $s->sample_type }} · Collected {{ optional($s->collected_at)->format('d M, h:i A') }}</div>
            </div>
        @empty
            <p class="text-xs text-slate-400">No active samples.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h2 class="font-bold text-slate-900 mb-3">Invoices & Family Members</h2>
        @foreach($selectedPatient->invoices as $inv)
            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 mb-2 text-xs flex justify-between items-center">
                <div>
                    <div class="font-mono font-bold">{{ $inv->invoice_no }}</div>
                    <div class="text-slate-500">₹{{ number_format($inv->total_amount, 2) }} · {{ $inv->payment_status }}</div>
                </div>
                <a href="{{ route('billing.show', $inv) }}" class="text-teal-700 font-semibold hover:underline">Receipt</a>
            </div>
        @endforeach
        @if($selectedPatient->familyMembers->isNotEmpty())
            <div class="mt-4 pt-3 border-t border-slate-100">
                <div class="text-xs font-bold text-slate-700 mb-1.5">Linked Family Profiles</div>
                @foreach($selectedPatient->familyMembers as $fm)
                    <a href="{{ route('portal.patient', ['patient_id' => $fm->id]) }}" class="block text-xs text-teal-700 hover:underline">{{ $fm->name }} ({{ $fm->family_relation }})</a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endif
@endsection

