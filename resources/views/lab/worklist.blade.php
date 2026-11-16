@extends('layouts.app')
@section('title', 'Lab Worklist & Processing')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Laboratory Worklist & Processing</h1>
        <p class="text-sm text-slate-500">STAT prioritization, technician assignment, TAT monitoring & rerun/dilution tracking.</p>
    </div>
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="department_id" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
            <option value="">All Departments</option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}" @selected($deptId == $d->id)>{{ $d->name }}</option>
            @endforeach
        </select>
        <select name="priority" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-lg">
            <option value="">All Priorities</option>
            @foreach(['STAT', 'Urgent', 'Routine'] as $pr)
                <option @selected($priority === $pr)>{{ $pr }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                <tr>
                    <th class="px-4 py-3">Order / Sample</th>
                    <th class="px-4 py-3">Patient</th>
                    <th class="px-4 py-3">Test & Dept</th>
                    <th class="px-4 py-3">Priority & TAT</th>
                    <th class="px-4 py-3">Assign & Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($orders as $ord)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-4 py-3">
                            <div class="font-mono text-xs font-bold text-teal-700">#ORD-{{ $ord->id }}</div>
                            <div class="text-[11px] text-slate-500">{{ $ord->sample->sample_id_code }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">{{ $ord->patient->name }}</div>
                            <div class="text-xs text-slate-500">{{ $ord->patient->uhid }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800">{{ $ord->test->name }}</div>
                            <div class="text-xs text-slate-500">{{ $ord->department->name }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-[11px] font-bold rounded-full {{ $ord->priority === 'STAT' ? 'bg-rose-100 text-rose-700' : ($ord->priority === 'Urgent' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600') }}">{{ $ord->priority }}</span>
                            @if($ord->tat_due_at)
                                <div class="text-[11px] text-slate-500 mt-1">Due: {{ $ord->tat_due_at->format('h:i A') }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('lab.worklist.update', $ord) }}" class="flex flex-wrap items-center gap-1.5">
                                @csrf @method('PATCH')
                                <select name="assigned_technician_id" class="px-2 py-1 text-xs border border-slate-200 rounded-lg">
                                    <option value="">Technician...</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}" @selected($ord->assigned_technician_id == $tech->id)>{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                                <select name="status" class="px-2 py-1 text-xs border border-slate-200 rounded-lg font-semibold">
                                    @foreach(['Pending','Sample Received','In-Progress','Rerun','Result Entered','Verified','Approved','Delivered'] as $st)
                                        <option @selected($ord->status === $st)>{{ $st }}</option>
                                    @endforeach
                                </select>
                                <button class="px-2.5 py-1 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg">Save</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100">{{ $orders->links() }}</div>
</div>
@endsection

