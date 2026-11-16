@extends('layouts.app')
@section('title', 'Reagents & Inventory')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Reagent, Consumables & Purchase Inventory</h1>
        <p class="text-sm text-slate-500">Batch numbers, expiry alerts, min-stock alerts, stock-in/out & inventory valuation.</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-right">
        <div class="text-[11px] text-slate-500 uppercase font-semibold">Total Inventory Valuation</div>
        <div class="text-lg font-bold text-teal-700 tabular-nums">₹{{ number_format($totalValuation, 2) }}</div>
    </div>
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Add Reagent / Consumable</h2>
        <form method="POST" action="{{ route('ops.inventory.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <select name="branch_id" class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    @foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                </select>
                <select name="department_id" class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    @foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
                </select>
            </div>
            <input type="text" name="name" placeholder="Reagent / Consumable Name" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            <div class="grid grid-cols-2 gap-3">
                <select name="category" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    <option>Reagent</option><option>Consumable</option><option>Control</option>
                </select>
                <input type="text" name="supplier_name" placeholder="Supplier Name" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="batch_no" placeholder="Batch No." required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                <input type="date" name="expiry_date" value="{{ now()->addMonths(6)->toDateString() }}" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-4 gap-2">
                <input type="number" name="stock_qty" placeholder="Qty" value="10" class="px-2 py-1.5 text-xs border border-slate-200 rounded-lg">
                <input type="number" name="min_stock_qty" placeholder="Min" value="5" class="px-2 py-1.5 text-xs border border-slate-200 rounded-lg">
                <input type="text" name="unit" placeholder="Unit" value="Kits" class="px-2 py-1.5 text-xs border border-slate-200 rounded-lg">
                <input type="number" name="unit_cost" placeholder="Cost ₹" value="1500" class="px-2 py-1.5 text-xs border border-slate-200 rounded-lg">
            </div>
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Add Stock Item</button>
        </form>
    </div>

    <div class="xl:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">Item & Batch</th>
                        <th class="px-4 py-3">Expiry</th>
                        <th class="px-4 py-3">Stock</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Stock-In / Out</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($reagents as $r)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $r->name }}</div>
                                <div class="text-xs text-slate-500">{{ $r->code }} · Batch: {{ $r->batch_no }} · {{ $r->supplier_name }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs {{ $r->expiry_date->diffInDays(now(), false) > -30 ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                {{ $r->expiry_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 font-bold tabular-nums">{{ $r->stock_qty }} {{ $r->unit }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $r->status === 'In Stock' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $r->status }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    <form method="POST" action="{{ route('ops.inventory.adjust', $r) }}">@csrf<input type="hidden" name="delta" value="5"><button class="px-2 py-1 text-xs bg-teal-50 text-teal-700 font-bold rounded">+5</button></form>
                                    <form method="POST" action="{{ route('ops.inventory.adjust', $r) }}">@csrf<input type="hidden" name="delta" value="-1"><button class="px-2 py-1 text-xs bg-slate-100 text-slate-700 font-bold rounded">-1</button></form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="text-base font-bold text-slate-900 mb-3">Purchase Orders</h2>
            @foreach($purchaseOrders as $po)
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 flex justify-between items-center text-xs mb-2">
                    <div>
                        <strong class="font-mono text-teal-700">{{ $po->po_number }}</strong> · {{ $po->supplier_name }}
                        <div class="text-slate-500">{{ $po->items_summary }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-slate-900">₹{{ number_format($po->total_amount, 2) }}</div>
                        <span class="px-2 py-0.5 rounded bg-sky-50 text-sky-700 font-semibold">{{ $po->status }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

