@extends('layouts.app')
@section('title', 'Reports & Delivery')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Report Generation, QR Verification & Delivery</h1>
    <p class="text-sm text-slate-500">Print/PDF lab reports, bilingual English/Hindi support, WhatsApp/Email delivery & revision tracking.</p>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                <tr>
                    <th class="px-4 py-3">Report No / QR</th>
                    <th class="px-4 py-3">Patient</th>
                    <th class="px-4 py-3">Investigation</th>
                    <th class="px-4 py-3">Approved By</th>
                    <th class="px-4 py-3">Delivery</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($reports as $rpt)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-3">
                            <div class="font-mono text-xs font-bold text-teal-700">{{ $rpt->report_no }}</div>
                            <div class="text-[11px] font-mono text-slate-400">{{ $rpt->verification_code }} · Rev {{ $rpt->revision_no }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">{{ $rpt->patient->name }}</div>
                            <div class="text-xs text-slate-500">{{ $rpt->patient->uhid }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ $rpt->labOrder->test->name ?? 'Diagnostic Panel' }}</td>
                        <td class="px-4 py-3 text-xs text-slate-600">{{ $rpt->approved_by ?? 'Dr. Ananya Sharma' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $rpt->status === 'Delivered' ? 'bg-emerald-50 text-emerald-700' : 'bg-sky-50 text-sky-700' }}">
                                {{ $rpt->status }}
                            </span>
                            @if($rpt->delivered_via)
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ implode(', ', $rpt->delivered_via) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1.5">
                                <a href="{{ route('lab.reports.show', [$rpt, 'lang' => 'en']) }}" class="px-2.5 py-1 rounded bg-teal-600 text-white text-xs font-semibold">View / Print</a>
                                <a href="{{ route('lab.reports.show', [$rpt, 'lang' => 'hi']) }}" class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 text-xs font-semibold">Hindi (हिन्दी)</a>
                                <form method="POST" action="{{ route('lab.reports.deliver', $rpt) }}" class="inline">
                                    @csrf
                                    <button class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">Send WhatsApp/Email</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100">{{ $reports->links() }}</div>
</div>
@endsection

