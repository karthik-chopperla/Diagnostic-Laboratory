@extends('layouts.app')
@section('title', 'Health Checkup Packages')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Test Packages & Preventive Health Checkups</h1>
    <p class="text-sm text-slate-500">Custom bundles, corporate health packages, pricing & test mapping.</p>
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Create Custom Package</h2>
        <form method="POST" action="{{ route('catalog.packages.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Package Name *</label>
                <input type="text" name="name" required placeholder="e.g. Senior Citizen Care" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Category</label>
                    <select name="category" class="w-full px-2 py-2 text-xs border border-slate-200 rounded-lg">
                        <option>Preventive</option>
                        <option>Disease Profile</option>
                        <option>Wellness</option>
                        <option>Corporate</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">MRP (₹)</label>
                    <input type="number" name="price" value="2500" class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Disc (%)</label>
                    <input type="number" name="discount_percent" value="20" class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Included Tests *</label>
                <select name="test_ids[]" multiple size="6" required class="w-full px-2 py-1.5 text-xs border border-slate-200 rounded-lg">
                    @foreach($tests as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} (₹{{ $t->price }})</option>
                    @endforeach
                </select>
            </div>
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Save Package</button>
        </form>
    </div>

    <div class="xl:col-span-2 grid sm:grid-cols-2 gap-4 h-fit">
        @foreach($packages as $pk)
            <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-bold text-teal-700">{{ $pk->code }}</span>
                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $pk->is_corporate ? 'bg-indigo-50 text-indigo-700' : 'bg-teal-50 text-teal-700' }}">{{ $pk->category }}</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mt-1">{{ $pk->name }}</h3>
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($pk->tests as $t)
                            <span class="px-2 py-0.5 text-[11px] rounded bg-slate-100 text-slate-700">{{ $t->code }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-baseline justify-between">
                    <div>
                        <span class="text-lg font-bold text-teal-700 tabular-nums">₹{{ number_format($pk->final_price, 0) }}</span>
                        <span class="text-xs text-slate-400 line-through ml-1">₹{{ number_format($pk->price, 0) }}</span>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700">{{ $pk->discount_percent }}% OFF</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

