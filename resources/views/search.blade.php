@extends('layouts.app')
@section('title', 'Global Search')

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-bold text-slate-900">Search Results for "{{ $q }}"</h1>
</div>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h2 class="font-bold text-slate-900 mb-3">Patients ({{ $patients->count() }})</h2>
        @forelse($patients as $p)
            <a href="{{ route('patients.show', $p) }}" class="block p-3 rounded-lg border border-slate-100 hover:bg-teal-50/50 mb-2">
                <div class="font-semibold text-slate-900 text-sm">{{ $p->name }}</div>
                <div class="text-xs text-slate-500">{{ $p->uhid }} · {{ $p->phone }}</div>
            </a>
        @empty
            <p class="text-xs text-slate-400">No matching patients.</p>
        @endforelse
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h2 class="font-bold text-slate-900 mb-3">Samples ({{ $samples->count() }})</h2>
        @forelse($samples as $s)
            <div class="p-3 rounded-lg border border-slate-100 mb-2">
                <div class="font-mono text-xs font-bold text-teal-700">{{ $s->sample_id_code }} ({{ $s->barcode_value }})</div>
                <div class="text-xs text-slate-600">{{ $s->patient->name }} · {{ $s->status }}</div>
            </div>
        @empty
            <p class="text-xs text-slate-400">No matching samples.</p>
        @endforelse
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h2 class="font-bold text-slate-900 mb-3">Invoices ({{ $invoices->count() }})</h2>
        @forelse($invoices as $inv)
            <a href="{{ route('billing.show', $inv) }}" class="block p-3 rounded-lg border border-slate-100 hover:bg-teal-50/50 mb-2">
                <div class="font-semibold text-slate-900 text-sm">{{ $inv->invoice_no }}</div>
                <div class="text-xs text-slate-500">{{ $inv->patient->name }} · ₹{{ number_format($inv->total_amount, 2) }} ({{ $inv->payment_status }})</div>
            </a>
        @empty
            <p class="text-xs text-slate-400">No matching invoices.</p>
        @endforelse
    </div>
</div>
@endsection

