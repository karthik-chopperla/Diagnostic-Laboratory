@extends('layouts.app')
@section('title', 'Finance & Accounting')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Finance, Cashbook & Accounting</h1>
        <p class="text-sm text-slate-500">Income/expense ledger, vendor & salary expenses, P&L summary & Tally/CSV export.</p>
    </div>
    <a href="{{ route('admin.finance.export') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg">Export Accounting CSV</a>
</div>

<div class="grid sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4"><div class="text-xs text-slate-500">Total Income</div><div class="text-xl font-bold text-emerald-700 mt-1 tabular-nums">₹{{ number_format($totalIncome, 2) }}</div></div>
    <div class="bg-white rounded-xl border border-slate-200 p-4"><div class="text-xs text-slate-500">Total Expenses</div><div class="text-xl font-bold text-rose-600 mt-1 tabular-nums">₹{{ number_format($totalExpense, 2) }}</div></div>
    <div class="bg-white rounded-xl border border-slate-200 p-4"><div class="text-xs text-slate-500">Net Profit / Loss</div><div class="text-xl font-bold text-teal-700 mt-1 tabular-nums">₹{{ number_format($totalIncome - $totalExpense, 2) }}</div></div>
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Record Ledger Entry</h2>
        <form method="POST" action="{{ route('admin.finance.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <select name="branch_id" class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    @foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                </select>
                <select name="type" class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    <option>Income</option><option>Expense</option>
                </select>
            </div>
            <input type="text" name="category" placeholder="Category (e.g. Salary, Vendor Payment)" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            <input type="text" name="description" placeholder="Description *" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            <div class="grid grid-cols-2 gap-3">
                <input type="number" step="0.01" name="amount" placeholder="Amount ₹" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                <select name="payment_method" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    <option>Bank Transfer</option><option>UPI</option><option>Cash</option>
                </select>
            </div>
            <input type="date" name="transaction_date" value="{{ today()->toDateString() }}" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Post Transaction</button>
        </form>
    </div>

    <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Category & Description</th>
                    <th class="px-4 py-3">Method</th>
                    <th class="px-4 py-3 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($transactions as $tx)
                    <tr>
                        <td class="px-4 py-3 text-xs text-slate-500">{{ $tx->transaction_date->format('d M Y') }}<br>{{ $tx->branch->code }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">{{ $tx->category }}</div>
                            <div class="text-xs text-slate-500">{{ $tx->description }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600">{{ $tx->payment_method }}</td>
                        <td class="px-4 py-3 text-right font-bold tabular-nums {{ $tx->type === 'Income' ? 'text-emerald-700' : 'text-rose-600' }}">
                            {{ $tx->type === 'Income' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

