@extends('layouts.app')
@section('title', 'Compliance, NABL & Audit Logs')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Compliance, NABL Quality & Audit Trail</h1>
        <p class="text-sm text-slate-500">RBAC, tamper-evident audit logs, accreditation records, data retention & database backup.</p>
    </div>
    <form method="POST" action="{{ route('admin.compliance.backup') }}">
        @csrf
        <button class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg">Run Verified DB Backup</button>
    </form>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-3">NABL & Licence Records</h2>
        @foreach($docs as $doc)
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-100 mb-2 text-xs">
                <div class="font-bold text-slate-900">{{ $doc->title }}</div>
                <div class="text-slate-500 mt-0.5">{{ $doc->doc_type }} · Ref: {{ $doc->reference_no }}</div>
                <div class="text-emerald-700 font-semibold mt-1">Valid Until: {{ optional($doc->valid_until)->format('d M Y') }}</div>
            </div>
        @endforeach
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 font-bold text-slate-900">System Audit Logs</div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-2.5">Time</th>
                        <th class="px-4 py-2.5">User</th>
                        <th class="px-4 py-2.5">Action / Module</th>
                        <th class="px-4 py-2.5">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($auditLogs as $log)
                        <tr>
                            <td class="px-4 py-2.5 text-slate-500 whitespace-nowrap">{{ $log->created_at->format('d M, H:i') }}</td>
                            <td class="px-4 py-2.5 font-semibold text-slate-800">{{ $log->user_name }}</td>
                            <td class="px-4 py-2.5">
                                <span class="font-semibold text-teal-700">{{ $log->action }}</span>
                                <div class="text-slate-400">{{ $log->module }} ({{ $log->record_id }})</div>
                            </td>
                            <td class="px-4 py-2.5 text-slate-600">{{ $log->details }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $auditLogs->links() }}</div>
    </div>
</div>
@endsection

