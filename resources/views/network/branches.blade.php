@extends('layouts.app')
@section('title', 'Multi-Branch & Franchise')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Multi-Branch & Franchise Network</h1>
    <p class="text-sm text-slate-500">Central processing lab, branch-wise revenue comparison, central patient DB & inter-branch transfers.</p>
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Add Branch / Franchise</h2>
        <form method="POST" action="{{ route('network.branches.store') }}" class="space-y-3">
            @csrf
            <input type="text" name="name" placeholder="Branch Name *" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            <input type="text" name="address" placeholder="Address *" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="city" placeholder="City *" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                <input type="text" name="phone" placeholder="Phone *" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <input type="text" name="license_number" placeholder="Lab Licence No." class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            <select name="accreditation_status" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                <option>NABL Accredited</option><option>State Licenced</option><option>Collection Center</option>
            </select>
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Add Branch</button>
        </form>
    </div>

    <div class="xl:col-span-2 space-y-4">
        @foreach($branches as $b)
            <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-xs font-bold text-teal-700">{{ $b->code }}</span>
                        @if($b->is_central)<span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-teal-600 text-white">Central Processing Lab</span>@endif
                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-emerald-50 text-emerald-700">{{ $b->accreditation_status }}</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mt-1">{{ $b->name }}</h3>
                    <p class="text-xs text-slate-500">{{ $b->address }}, {{ $b->city }} · {{ $b->phone }}</p>
                </div>
                <div class="grid grid-cols-3 gap-4 text-center text-xs shrink-0">
                    <div class="p-2.5 rounded-lg bg-slate-50"><div class="text-slate-400">Patients</div><div class="font-bold text-slate-900 text-sm">{{ $b->patients_count }}</div></div>
                    <div class="p-2.5 rounded-lg bg-slate-50"><div class="text-slate-400">Samples</div><div class="font-bold text-slate-900 text-sm">{{ $b->samples_count }}</div></div>
                    <div class="p-2.5 rounded-lg bg-teal-50"><div class="text-teal-700">Revenue</div><div class="font-bold text-teal-800 text-sm tabular-nums">₹{{ number_format($b->payments_sum_amount ?? 0, 0) }}</div></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

