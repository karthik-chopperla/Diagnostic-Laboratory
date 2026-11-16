@extends('layouts.app')
@section('title', 'Billing & Invoicing')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Billing, Invoicing & Cash Collection</h1>
        <p class="text-sm text-slate-500">Test & package billing, coupons, GST, partial/advance payments, refunds & counter reports.</p>
    </div>
</div>

<!-- Collection Summary -->
<div class="grid sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-xs text-slate-500 font-medium">Today's Cash Counter</div>
        <div class="text-xl font-bold text-slate-900 mt-1 tabular-nums">₹{{ number_format($summary['today_cash'], 2) }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-xs text-slate-500 font-medium">Today's UPI / Card / Bank</div>
        <div class="text-xl font-bold text-teal-700 mt-1 tabular-nums">₹{{ number_format($summary['today_upi_card'], 2) }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-xs text-slate-500 font-medium">Outstanding Balance</div>
        <div class="text-xl font-bold text-rose-600 mt-1 tabular-nums">₹{{ number_format($summary['outstanding'], 2) }}</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4">
        <div class="text-xs text-slate-500 font-medium">Active Coupons</div>
        <div class="text-xs font-mono font-bold text-slate-700 mt-1.5">{{ $coupons->pluck('code')->implode(' · ') }}</div>
    </div>
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <!-- New Invoice -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Create New Bill / Invoice</h2>
        <form method="POST" action="{{ route('billing.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Branch</label>
                    <select name="branch_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Counter</label>
                    <select name="counter_name" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option>Counter 1</option>
                        <option>Counter 2</option>
                        <option>Corporate Desk</option>
                        <option>Home Collection</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Patient *</label>
                <select name="patient_id" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->uhid }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Referring Doctor</label>
                    <select name="doctor_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option value="">Self</option>
                        @foreach($doctors as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Corporate Client</label>
                    <select name="corporate_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option value="">Retail Patient</option>
                        @foreach($corporates as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Select Tests (Hold Ctrl/Cmd for multiple)</label>
                <select name="test_ids[]" multiple size="4" class="w-full px-2 py-1.5 text-xs border border-slate-200 rounded-lg">
                    @foreach($tests as $t)
                        <option value="{{ $t->id }}">{{ $t->code }} - {{ $t->name }} (₹{{ $t->price }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Select Health Packages (Optional)</label>
                <select name="package_ids[]" multiple size="3" class="w-full px-2 py-1.5 text-xs border border-slate-200 rounded-lg">
                    @foreach($packages as $pk)
                        <option value="{{ $pk->id }}">{{ $pk->name }} (₹{{ $pk->final_price }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Coupon</label>
                    <input type="text" name="coupon_code" placeholder="HEALTH15" class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg uppercase">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Discount (₹)</label>
                    <input type="number" name="discount_amount" value="0" min="0" class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">GST (%)</label>
                    <input type="number" name="gst_percent" value="0" min="0" max="28" class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Amount Paid Now (₹)</label>
                    <input type="number" name="paid_amount" value="0" min="0" step="0.01" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option>UPI</option>
                        <option>Cash</option>
                        <option>Card</option>
                        <option>Bank Transfer</option>
                        <option>Advance</option>
                    </select>
                </div>
            </div>
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Generate Invoice & Receipt</button>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Invoices & Receipts</h2>
            <div class="flex gap-1.5">
                @foreach(['', 'Paid', 'Partial', 'Unpaid'] as $st)
                    <a href="{{ route('billing.index', ['status' => $st]) }}" class="px-2.5 py-1 rounded text-xs font-semibold {{ $status === $st ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $st ?: 'All' }}</a>
                @endforeach
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">Invoice</th>
                        <th class="px-4 py-3">Patient</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Paid / Balance</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoices as $inv)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <div class="font-mono text-xs font-bold text-teal-700">{{ $inv->invoice_no }}</div>
                                <div class="text-[11px] text-slate-500">{{ $inv->counter_name }} · {{ $inv->created_at->format('d M') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $inv->patient->name }}</div>
                                <div class="text-xs text-slate-500">{{ $inv->items->count() }} item(s)</div>
                            </td>
                            <td class="px-4 py-3 font-bold text-slate-900 tabular-nums">₹{{ number_format($inv->total_amount, 2) }}</td>
                            <td class="px-4 py-3 text-xs tabular-nums">
                                <div class="text-emerald-700 font-semibold">Paid: ₹{{ number_format($inv->paid_amount, 2) }}</div>
                                @if($inv->balance_amount > 0)
                                    <div class="text-rose-600 font-semibold">Due: ₹{{ number_format($inv->balance_amount, 2) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $inv->payment_status === 'Paid' ? 'bg-emerald-50 text-emerald-700' : ($inv->payment_status === 'Partial' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                    {{ $inv->payment_status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('billing.show', $inv) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold">Print / Pay</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $invoices->links() }}</div>
    </div>
</div>
@endsection

