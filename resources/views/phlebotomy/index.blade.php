@extends('layouts.app')
@section('title', 'Phlebotomy & Home Collection')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Phlebotomy & Home Sample Collection</h1>
    <p class="text-sm text-slate-500">Route planning, phlebotomist assignment, collection checklist, mobile status & cash tracking.</p>
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Schedule Home Collection</h2>
        <form method="POST" action="{{ route('phlebotomy.store') }}" class="space-y-3">
            @csrf
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
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Branch</label>
                    <select name="branch_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Assign Phlebotomist</label>
                    <select name="phlebotomist_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option value="">Unassigned</option>
                        @foreach($phlebotomists as $ph)
                            <option value="{{ $ph->id }}">{{ $ph->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Patient Address *</label>
                <input type="text" name="address" required placeholder="House / Flat, Street, Landmark" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Route Zone</label>
                    <select name="route_zone" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option>Zone A - Central</option>
                        <option>Zone B - Indiranagar</option>
                        <option>Zone C - Hebbal North</option>
                        <option>Zone D - Koramangala</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Date</label>
                    <input type="date" name="scheduled_date" value="{{ today()->toDateString() }}" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Time Slot</label>
                    <input type="text" name="time_slot" value="07:00 - 08:00 AM" class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Visit Fee (₹)</label>
                    <input type="number" name="collection_charge" value="150" class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Cash Due (₹)</label>
                    <input type="number" name="cash_to_collect" value="0" class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                </div>
            </div>
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Dispatch Home Collection</button>
        </form>
    </div>

    <div class="xl:col-span-2 space-y-4">
        @foreach($collections as $hc)
            <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="font-mono text-xs font-bold text-teal-700">{{ $hc->booking_code }}</span>
                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-sky-50 text-sky-700">{{ $hc->route_zone }}</span>
                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-teal-50 text-teal-700">{{ $hc->status }}</span>
                    </div>
                    <div class="font-bold text-slate-900 mt-1">{{ $hc->patient->name }} <span class="text-xs font-normal text-slate-500">({{ $hc->patient->phone }})</span></div>
                    <div class="text-xs text-slate-600 mt-0.5">{{ $hc->address }} · Slot: {{ $hc->time_slot }}</div>
                    <div class="text-xs text-slate-500 mt-1">
                        Phlebotomist: <strong>{{ $hc->phlebotomist->name ?? 'Unassigned' }}</strong> ·
                        Cash Due: ₹{{ number_format($hc->cash_to_collect, 0) }} · Collected: ₹{{ number_format($hc->cash_collected, 0) }}
                    </div>
                </div>
                <form method="POST" action="{{ route('phlebotomy.status', $hc) }}" class="flex items-center gap-2 shrink-0">
                    @csrf @method('PATCH')
                    <input type="number" name="cash_collected" value="{{ $hc->cash_collected }}" placeholder="Cash ₹" class="w-24 px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                    <select name="status" class="px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                        @foreach(['Pending','Assigned','En-Route','Collected','Delivered to Lab','Cancelled'] as $st)
                            <option @selected($hc->status === $st)>{{ $st }}</option>
                        @endforeach
                    </select>
                    <button class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg">Update</button>
                </form>
            </div>
        @endforeach
        {{ $collections->links() }}
    </div>
</div>
@endsection

