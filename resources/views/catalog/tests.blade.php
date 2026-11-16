@extends('layouts.app')
@section('title', 'Test & Investigation Master')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Test & Investigation Master (16 Categories)</h1>
    <p class="text-sm text-slate-500">Test codes, specimen containers, TAT, methodology, fasting rules, parameters & critical thresholds.</p>
</div>

<!-- Category Filter Pills -->
<div class="flex flex-wrap gap-1.5 mb-6">
    <a href="{{ route('catalog.tests') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold border {{ !$catId ? 'bg-teal-600 text-white border-teal-600' : 'bg-white text-slate-600 border-slate-200' }}">All Categories</a>
    @foreach($categories as $cat)
        <a href="{{ route('catalog.tests', ['category_id' => $cat->id]) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-semibold border {{ $catId == $cat->id ? 'bg-teal-600 text-white border-teal-600' : 'bg-white text-slate-600 border-slate-200' }}">
            {{ $cat->name }} ({{ $cat->tests_count }})
        </a>
    @endforeach
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Add New Investigation</h2>
        <form method="POST" action="{{ route('catalog.tests.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Category *</label>
                    <select name="category_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Department *</label>
                    <select name="department_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        @foreach($departments as $dep)
                            <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Code *</label>
                    <input type="text" name="code" placeholder="BIOC-099" required class="w-full px-2.5 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Test Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Sample</label>
                    <input type="text" name="sample_type" value="Serum" class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Container</label>
                    <input type="text" name="container_type" value="SST (Yellow)" class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Qty</label>
                    <input type="text" name="sample_quantity" value="3 mL" class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">TAT (hrs)</label>
                    <input type="number" name="tat_hours" value="4" class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Price (₹)</label>
                    <input type="number" name="price" value="500" class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Method</label>
                    <input type="text" name="methodology" value="Photometry" class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                </div>
            </div>
            <label class="flex items-center gap-2 text-xs text-slate-600">
                <input type="checkbox" name="fasting_required" value="1" class="rounded text-teal-600"> Fasting Required
            </label>
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Save Test to Master</button>
        </form>
    </div>

    <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Investigation</th>
                        <th class="px-4 py-3">Specimen & Container</th>
                        <th class="px-4 py-3">TAT</th>
                        <th class="px-4 py-3 text-right">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tests as $t)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-teal-700">{{ $t->code }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $t->name }}</div>
                                <div class="text-xs text-slate-500">{{ $t->category->name }} · {{ $t->methodology }} · {{ $t->parameters->count() }} params</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $t->sample_type }} ({{ $t->container_type }}, {{ $t->sample_quantity }})</td>
                            <td class="px-4 py-3 text-xs text-slate-700">{{ $t->tat_hours }}h</td>
                            <td class="px-4 py-3 text-right font-bold text-slate-900 tabular-nums">₹{{ number_format($t->price, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $tests->links() }}</div>
    </div>
</div>
@endsection

