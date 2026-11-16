@extends('layouts.app')
@section('title', 'Invoice ' . $invoice->invoice_no)

@section('content')
<div class="no-print mb-4 flex items-center justify-between">
    <a href="{{ route('billing.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">← Back to Billing</a>
    <button onclick="window.print()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-xs">Print Invoice / Receipt</button>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-8 print-area">
        <div class="flex justify-between items-start border-b border-slate-200 pb-5 mb-5">
            <div>
                <h1 class="text-xl font-bold text-teal-700">PathoLab Diagnostic Center</h1>
                <p class="text-xs text-slate-500">{{ $invoice->branch->name }} · {{ $invoice->branch->address }}</p>
                <p class="text-xs text-slate-500">Phone: {{ $invoice->branch->phone }} · Lic: {{ $invoice->branch->license_number }}</p>
            </div>
            <div class="text-right">
                <div class="text-lg font-bold text-slate-900">TAX INVOICE</div>
                <div class="font-mono text-sm font-bold text-teal-700">{{ $invoice->invoice_no }}</div>
                <div class="text-xs text-slate-500">{{ $invoice->created_at->format('d M Y, h:i A') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-xs mb-6 bg-slate-50 p-4 rounded-lg border border-slate-100">
            <div>
                <div class="text-slate-400 uppercase font-semibold">Billed To</div>
                <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $invoice->patient->name }}</div>
                <div class="text-slate-600">UHID: {{ $invoice->patient->uhid }} · {{ $invoice->patient->age }}Y / {{ $invoice->patient->gender }}</div>
                <div class="text-slate-600">Phone: {{ $invoice->patient->phone }}</div>
            </div>
            <div class="text-right">
                <div class="text-slate-400 uppercase font-semibold">Referral & Counter</div>
                <div class="font-semibold text-slate-800 mt-0.5">{{ $invoice->doctor->name ?? 'Self / Walk-in' }}</div>
                <div class="text-slate-600">Counter: {{ $invoice->counter_name }}</div>
                <div class="text-slate-600">Status: <strong>{{ $invoice->payment_status }}</strong></div>
            </div>
        </div>

        <table class="w-full text-sm mb-6">
            <thead class="border-y border-slate-200 bg-slate-50 text-xs font-semibold text-slate-600">
                <tr>
                    <th class="py-2.5 px-3 text-left">Investigation / Package</th>
                    <th class="py-2.5 px-3 text-center">Type</th>
                    <th class="py-2.5 px-3 text-right">Amount (₹)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($invoice->items as $item)
                    <tr>
                        <td class="py-2.5 px-3 font-medium text-slate-800">{{ $item->item_name }}</td>
                        <td class="py-2.5 px-3 text-center text-xs text-slate-500">{{ $item->item_type }}</td>
                        <td class="py-2.5 px-3 text-right font-semibold tabular-nums">₹{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="flex justify-end">
            <div class="w-64 space-y-1.5 text-sm">
                <div class="flex justify-between text-slate-600"><span>Subtotal</span><span class="tabular-nums">₹{{ number_format($invoice->subtotal, 2) }}</span></div>
                <div class="flex justify-between text-emerald-700"><span>Discount</span><span class="tabular-nums">-₹{{ number_format($invoice->discount_amount, 2) }}</span></div>
                <div class="flex justify-between text-slate-600"><span>GST ({{ $invoice->gst_percent }}%)</span><span class="tabular-nums">₹{{ number_format($invoice->gst_amount, 2) }}</span></div>
                <div class="flex justify-between font-bold text-base text-slate-900 border-t border-slate-200 pt-2"><span>Total</span><span class="tabular-nums">₹{{ number_format($invoice->total_amount, 2) }}</span></div>
                <div class="flex justify-between text-teal-700 font-semibold"><span>Paid</span><span class="tabular-nums">₹{{ number_format($invoice->paid_amount, 2) }}</span></div>
                <div class="flex justify-between text-rose-600 font-bold"><span>Balance Due</span><span class="tabular-nums">₹{{ number_format($invoice->balance_amount, 2) }}</span></div>
            </div>
        </div>
    </div>

    <!-- Payment & Refund Panel -->
    <div class="no-print space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-bold text-slate-900 mb-3">Record Payment / Refund</h2>
            <form method="POST" action="{{ route('billing.payments', $invoice) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Transaction Type</label>
                    <select name="payment_type" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option>Payment</option>
                        <option>Refund</option>
                        <option>Credit Note</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Amount (₹)</label>
                    <input type="number" name="amount" value="{{ $invoice->balance_amount > 0 ? $invoice->balance_amount : 0 }}" step="0.01" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Method</label>
                    <select name="payment_method" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option>UPI</option>
                        <option>Cash</option>
                        <option>Card</option>
                        <option>Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Reference No.</label>
                    <input type="text" name="transaction_ref" placeholder="UPI / Card Auth ID" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <button class="w-full py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Submit Transaction</button>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-bold text-slate-900 mb-3">Receipts History</h2>
            @foreach($invoice->payments as $pay)
                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 mb-2 text-xs flex justify-between">
                    <div>
                        <div class="font-mono font-bold text-slate-800">{{ $pay->receipt_no }}</div>
                        <div class="text-slate-500">{{ $pay->payment_type }} · {{ $pay->payment_method }}</div>
                    </div>
                    <div class="font-bold text-teal-700 tabular-nums">₹{{ number_format($pay->amount, 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

