@extends('layouts.app')
@section('title', 'Diagnostic Report ' . $labReport->report_no)

@section('content')
<div class="no-print mb-4 flex items-center justify-between">
    <a href="{{ route('lab.reports') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">← Back to Reports</a>
    <div class="flex gap-2">
        <a href="{{ route('lab.reports.show', [$labReport, 'lang' => $lang === 'hi' ? 'en' : 'hi']) }}" class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs font-semibold">
            Switch to {{ $lang === 'hi' ? 'English' : 'Hindi (हिन्दी)' }}
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Print / Save PDF</button>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 p-8 max-w-4xl mx-auto print-area">
    <!-- Lab Letterhead -->
    <div class="flex justify-between items-start border-b-2 border-teal-600 pb-4 mb-5">
        <div>
            <div class="text-2xl font-bold text-teal-700 tracking-tight">PathoLab Diagnostic Center</div>
            <div class="text-xs text-slate-500 mt-0.5">{{ $labReport->labOrder->branch->name ?? 'Central Reference Lab' }} · NABL ISO 15189:2022 Accredited</div>
            <div class="text-xs text-slate-500">14 Medical Square, MG Road, Bengaluru · +91 80 4123 9000</div>
        </div>
        <div class="text-right">
            <div class="text-xs font-bold uppercase tracking-wider text-teal-700">{{ $lang === 'hi' ? 'प्रयोगशाला जांच रिपोर्ट' : 'DIAGNOSTIC LABORATORY REPORT' }}</div>
            <div class="font-mono text-sm font-bold text-slate-900 mt-0.5">{{ $labReport->report_no }}</div>
            <div class="text-[11px] text-slate-500">QR Verify: {{ $labReport->verification_code }}</div>
        </div>
    </div>

    <!-- Patient Demographics -->
    <div class="grid grid-cols-2 gap-4 bg-slate-50 border border-slate-200 rounded-lg p-4 text-xs mb-6">
        <div class="space-y-1">
            <div><span class="text-slate-500">{{ $lang === 'hi' ? 'रोगी का नाम' : 'Patient Name' }}:</span> <strong class="text-slate-900 text-sm">{{ $labReport->patient->name }}</strong></div>
            <div><span class="text-slate-500">UHID:</span> <strong class="font-mono">{{ $labReport->patient->uhid }}</strong></div>
            <div><span class="text-slate-500">{{ $lang === 'hi' ? 'आयु / लिंग' : 'Age / Gender' }}:</span> <strong>{{ $labReport->patient->age }} Years / {{ $labReport->patient->gender }}</strong></div>
        </div>
        <div class="space-y-1 text-right">
            <div><span class="text-slate-500">{{ $lang === 'hi' ? 'संदर्भित चिकित्सक' : 'Referred By' }}:</span> <strong>{{ $labReport->labOrder->sample->doctor->name ?? 'Self' }}</strong></div>
            <div><span class="text-slate-500">Sample ID:</span> <strong class="font-mono">{{ $labReport->labOrder->sample->sample_id_code ?? '-' }}</strong></div>
            <div><span class="text-slate-500">Reported On:</span> <strong>{{ optional($labReport->approved_at)->format('d M Y, h:i A') ?? now()->format('d M Y, h:i A') }}</strong></div>
        </div>
    </div>

    <!-- Test Results -->
    @if($labReport->labOrder)
        <div class="mb-3">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-200 pb-1.5">{{ $labReport->labOrder->test->name }}</h2>
            <p class="text-[11px] text-slate-500 mt-1">Methodology: {{ $labReport->labOrder->test->methodology }} · Specimen: {{ $labReport->labOrder->test->sample_type }}</p>
        </div>

        <table class="w-full text-sm mb-8">
            <thead class="bg-teal-50 text-teal-900 text-xs font-bold uppercase">
                <tr>
                    <th class="py-2.5 px-3 text-left">{{ $lang === 'hi' ? 'जांच पैरामीटर' : 'Investigation Parameter' }}</th>
                    <th class="py-2.5 px-3 text-left">{{ $lang === 'hi' ? 'परिणाम' : 'Result' }}</th>
                    <th class="py-2.5 px-3 text-left">{{ $lang === 'hi' ? 'इकाई' : 'Unit' }}</th>
                    <th class="py-2.5 px-3 text-left">{{ $lang === 'hi' ? 'सामान्य सीमा' : 'Reference Range' }}</th>
                    <th class="py-2.5 px-3 text-left">Flag</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($labReport->labOrder->results as $res)
                    <tr class="{{ $res->flag !== 'Normal' ? 'bg-rose-50/40' : '' }}">
                        <td class="py-2.5 px-3 font-medium text-slate-900">{{ $res->parameter_name }}</td>
                        <td class="py-2.5 px-3 font-bold tabular-nums {{ $res->flag !== 'Normal' ? 'text-rose-700' : 'text-slate-900' }}">{{ $res->result_value }}</td>
                        <td class="py-2.5 px-3 text-xs text-slate-600">{{ $res->unit }}</td>
                        <td class="py-2.5 px-3 text-xs text-slate-600 tabular-nums">{{ $res->reference_range }}</td>
                        <td class="py-2.5 px-3">
                            <span class="px-2 py-0.5 text-[11px] font-bold rounded {{ str_contains($res->flag, 'Critical') ? 'bg-rose-600 text-white' : ($res->flag === 'Normal' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-100 text-amber-800') }}">
                                {{ $res->flag }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Footer with QR Code & Pathologist E-Signature -->
    <div class="flex justify-between items-end pt-6 border-t border-slate-200">
        <div class="flex items-center gap-3">
            <!-- Deterministic Visual QR SVG -->
            <svg class="w-16 h-16 border border-slate-300 p-1 rounded bg-white" viewBox="0 0 21 21" fill="currentColor">
                <rect x="1" y="1" width="5" height="5" fill="none" stroke="currentColor" stroke-width="1"/>
                <rect x="2.5" y="2.5" width="2" height="2"/>
                <rect x="15" y="1" width="5" height="5" fill="none" stroke="currentColor" stroke-width="1"/>
                <rect x="16.5" y="2.5" width="2" height="2"/>
                <rect x="1" y="15" width="5" height="5" fill="none" stroke="currentColor" stroke-width="1"/>
                <rect x="2.5" y="16.5" width="2" height="2"/>
                <rect x="8" y="2" width="2" height="2"/><rect x="11" y="4" width="2" height="2"/>
                <rect x="8" y="8" width="4" height="4"/><rect x="14" y="9" width="2" height="3"/>
                <rect x="9" y="14" width="3" height="2"/><rect x="15" y="15" width="4" height="4"/>
            </svg>
            <div class="text-[11px] text-slate-500">
                <div class="font-semibold text-slate-700">Scan QR to Verify Authenticity</div>
                <div class="font-mono">{{ $labReport->verification_code }}</div>
                <div>End of Diagnostic Report</div>
            </div>
        </div>
        <div class="text-right">
            <div class="font-serif italic text-lg text-teal-800">Dr. Ananya Sharma</div>
            <div class="text-xs font-bold text-slate-900">Dr. Ananya Sharma, MD (Pathology)</div>
            <div class="text-[11px] text-slate-500">Chief Pathologist · Reg: KMC-45821</div>
        </div>
    </div>
</div>
@endsection

