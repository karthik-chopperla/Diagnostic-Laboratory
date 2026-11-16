@extends('layouts.app')
@section('title', 'Equipment & Analyzers')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Laboratory Equipment & Analyzer Management</h1>
    <p class="text-sm text-slate-500">Calibration schedules, preventive maintenance, breakdown logs, downtime & HL7/ASTM interfaces.</p>
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Register Analyzer</h2>
        <form method="POST" action="{{ route('ops.equipment.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <select name="branch_id" class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    @foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                </select>
                <select name="department_id" class="px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    @foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
                </select>
            </div>
            <input type="text" name="name" placeholder="Analyzer Name (e.g. Cobas e411)" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="manufacturer" placeholder="Manufacturer" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                <input type="text" name="model" placeholder="Model" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="location" placeholder="Bay / Room" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                <select name="interface_type" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    <option>HL7 / Bidirectional</option>
                    <option>ASTM E1394</option>
                    <option>Manual</option>
                </select>
            </div>
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Register Analyzer</button>
        </form>
    </div>

    <div class="xl:col-span-2 space-y-4">
        @foreach($analyzers as $an)
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-teal-700">{{ $an->equipment_code }}</span>
                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $an->status === 'Online' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-100 text-amber-800' }}">{{ $an->status }}</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mt-1">{{ $an->name }} · {{ $an->manufacturer }}</h3>
                        <p class="text-xs text-slate-500">{{ $an->department->name }} · {{ $an->location }} · {{ $an->interface_type }}</p>
                    </div>
                    <div class="text-right text-xs text-slate-600">
                        <div>Tests Run: <strong>{{ number_format($an->total_tests_run) }}</strong></div>
                        <div>Next Calib: <strong>{{ optional($an->next_calibration_at)->format('d M Y') }}</strong></div>
                        <div>Downtime: <strong>{{ $an->downtime_hours }} hrs</strong></div>
                    </div>
                </div>
                <form method="POST" action="{{ route('ops.equipment.log', $an) }}" class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap gap-2">
                    @csrf
                    <select name="log_type" class="px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                        <option>Calibration</option>
                        <option>Preventive Maintenance</option>
                        <option>Breakdown</option>
                        <option>Service</option>
                    </select>
                    <input type="text" name="description" placeholder="Log notes..." required class="flex-1 min-w-[200px] px-3 py-1.5 text-xs border border-slate-200 rounded-lg">
                    <button class="px-3 py-1.5 bg-slate-800 text-white text-xs font-semibold rounded-lg">Log Event</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection

