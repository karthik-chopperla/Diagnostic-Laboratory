@extends('layouts.app')
@section('title', 'Corporates & Hospitals')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Corporate, Hospital & B2B Client Management</h1>
    <p class="text-sm text-slate-500">Credit limits, company-wise discounts, monthly invoicing, contracts & employee health checkups.</p>
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Register Corporate / Hospital</h2>
        <form method="POST" action="{{ route('network.corporates.store') }}" class="space-y-3">
            @csrf
            <input type="text" name="name" placeholder="Organization Name *" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            <select name="type" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                <option>Corporate</option><option>Hospital</option><option>Nursing Home</option>
            </select>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="contact_person" placeholder="Contact Person *" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                <input type="text" name="phone" placeholder="Phone *" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="number" name="credit_limit" value="200000" placeholder="Credit Limit ₹" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                <input type="number" name="discount_percent" value="15" placeholder="Discount %" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Create Corporate Account</button>
        </form>
    </div>

    <div class="xl:col-span-2 grid sm:grid-cols-2 gap-4 h-fit">
        @foreach($corporates as $c)
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex justify-between items-start">
                    <span class="font-mono text-xs font-bold text-teal-700">{{ $c->code }}</span>
                    <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-indigo-50 text-indigo-700">{{ $c->type }}</span>
                </div>
                <h3 class="text-base font-bold text-slate-900 mt-1">{{ $c->name }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Contact: {{ $c->contact_person }} · {{ $c->phone }}</p>
                <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-3 gap-2 text-xs">
                    <div><div class="text-slate-400">Credit Limit</div><div class="font-bold">₹{{ number_format($c->credit_limit, 0) }}</div></div>
                    <div><div class="text-slate-400">Outstanding</div><div class="font-bold text-rose-600">₹{{ number_format($c->current_balance, 0) }}</div></div>
                    <div><div class="text-slate-400">Discount</div><div class="font-bold text-teal-700">{{ $c->discount_percent }}%</div></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

