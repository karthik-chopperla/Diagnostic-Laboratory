@extends('layouts.app')
@section('title', 'Integrations & Automation')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Advanced Integrations & Automation Gateways</h1>
    <p class="text-sm text-slate-500">HL7 v2.5, ASTM E1394 analyzers, DICOM/PACS/RIS, FHIR R4, WhatsApp Business API, SMS, Payment & Tally.</p>
</div>

<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($integrations as $int)
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 text-[11px] font-semibold rounded bg-slate-100 text-slate-700">{{ $int->category }}</span>
                    <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full {{ $int->status === 'Connected' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">{{ $int->status }}</span>
                </div>
                <h3 class="text-base font-bold text-slate-900 mt-2.5">{{ $int->name }}</h3>
                <div class="text-xs font-mono text-teal-700 mt-1">{{ $int->protocol }} · {{ $int->endpoint_or_port }}</div>
                <p class="text-xs text-slate-500 mt-2">{{ $int->config_summary }}</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400">Last sync: {{ optional($int->last_sync_at)->diffForHumans() ?? 'Never' }}</span>
                <form method="POST" action="{{ route('admin.integrations.test', $int) }}">
                    @csrf
                    <button class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg">Ping & Sync</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection

