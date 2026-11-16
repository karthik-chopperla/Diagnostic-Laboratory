@extends('layouts.app')
@section('title', 'Critical Value & Emergency Management')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Critical Value & Emergency Alerts</h1>
    <p class="text-sm text-slate-500">Automatic critical threshold detection, pathologist/doctor notification & acknowledgement audit.</p>
</div>

<div class="space-y-4">
    @foreach($alerts as $alert)
        <div class="bg-white rounded-xl border {{ $alert->status === 'Open' ? 'border-rose-300 bg-rose-50/20' : 'border-slate-200' }} p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $alert->status === 'Open' ? 'bg-rose-600 text-white' : 'bg-emerald-100 text-emerald-700' }}">{{ $alert->status }}</span>
                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-amber-100 text-amber-800">{{ $alert->alert_level }}</span>
                    <span class="text-xs text-slate-500">{{ $alert->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="text-base font-bold text-slate-900 mt-1.5">
                    {{ $alert->patient->name }} <span class="text-xs font-normal text-slate-500">({{ $alert->patient->uhid }})</span>
                </div>
                <div class="text-sm font-semibold text-rose-700 mt-0.5">
                    {{ $alert->parameter_name }}: {{ $alert->result_value }} · {{ $alert->threshold_info }}
                </div>
                <div class="text-xs text-slate-600 mt-1">
                    Referring Doctor: <strong>{{ $alert->doctor->name ?? 'Walk-in / Self' }}</strong> ·
                    {{ $alert->communication_notes }}
                </div>
                @if($alert->acknowledged_at)
                    <div class="text-xs text-emerald-700 font-semibold mt-1">
                        Acknowledged by {{ $alert->acknowledged_by }} at {{ $alert->acknowledged_at->format('d M Y, h:i A') }}
                    </div>
                @endif
            </div>

            @if($alert->status === 'Open')
                <form method="POST" action="{{ route('lab.critical.ack', $alert) }}" class="flex flex-col sm:flex-row gap-2 shrink-0">
                    @csrf
                    <input type="text" name="communication_notes" placeholder="Doctor informed via call..." class="px-3 py-2 text-xs border border-slate-200 rounded-lg w-64">
                    <button name="status" value="Acknowledged" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg">Acknowledge & Log</button>
                </form>
            @endif
        </div>
    @endforeach
    {{ $alerts->links() }}
</div>
@endsection

