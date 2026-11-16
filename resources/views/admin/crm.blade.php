@extends('layouts.app')
@section('title', 'Marketing & CRM Suite')

@section('content')
<!-- Header & Quick Actions -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-teal-100 text-teal-800 uppercase tracking-wider">Module 24 · Diagnostic CRM</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Marketing, Patient CRM & Doctor Relations</h1>
        <p class="text-sm text-slate-500">Lead pipeline, health checkup campaigns, automated preventive recalls, PRM & customer feedback.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <button type="button" onclick="openModal('modal-add-lead')"
                class="px-3.5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span>+ New Lead</span>
        </button>
        <button type="button" onclick="openModal('modal-add-campaign')"
                class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            <span>+ Launch Campaign</span>
        </button>
        <button type="button" onclick="openModal('modal-add-reminder')"
                class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>+ Schedule Recall</span>
        </button>
        <button type="button" onclick="openModal('modal-add-ticket')"
                class="px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
            <span>+ Log Ticket / Review</span>
        </button>
    </div>
</div>

<!-- 6 CRM Telemetry KPI Cards -->
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs">
        <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Pipeline</div>
        <div class="text-xl font-bold text-slate-900 mt-1 tabular-nums">{{ $kpis['total_leads'] }} Leads</div>
        <div class="text-[11px] font-semibold text-teal-700 mt-1">₹{{ number_format($kpis['pipeline_value'], 0) }} expected</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs">
        <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Conversion Rate</div>
        <div class="text-xl font-bold text-teal-700 mt-1 tabular-nums">{{ $kpis['conversion_rate'] }}%</div>
        <div class="text-[11px] text-slate-500 mt-1">Booked & Converted</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs">
        <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Active Campaigns</div>
        <div class="text-xl font-bold text-slate-900 mt-1 tabular-nums">{{ $kpis['active_campaigns'] }}</div>
        <div class="text-[11px] text-emerald-700 mt-1 font-semibold">Promotions running</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs">
        <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Due Recalls</div>
        <div class="text-xl font-bold text-amber-600 mt-1 tabular-nums">{{ $kpis['due_reminders'] }}</div>
        <div class="text-[11px] text-slate-500 mt-1">HbA1c / Annual checks</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs">
        <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Customer CSAT</div>
        <div class="text-xl font-bold text-amber-500 mt-1 tabular-nums">★ {{ $kpis['csat_rating'] }} / 5</div>
        <div class="text-[11px] text-slate-500 mt-1">Based on reviews</div>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-2xs">
        <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Open Tickets</div>
        <div class="text-xl font-bold text-rose-600 mt-1 tabular-nums">{{ $kpis['open_tickets'] }}</div>
        <div class="text-[11px] text-slate-500 mt-1">Support & complaints</div>
    </div>
</div>

<!-- CRM Navigation Tabs -->
<div class="border-b border-slate-200 mb-6">
    <nav class="flex space-x-6 text-sm font-semibold">
        <button type="button" onclick="switchCrmTab('tab-pipeline')" id="btn-tab-pipeline"
                class="crm-nav-btn pb-3 border-b-2 border-teal-600 text-teal-700 font-bold transition flex items-center gap-1.5 cursor-pointer">
            <span>Lead Pipeline & Stages</span>
            <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-teal-100 text-teal-800">{{ $leads->count() }}</span>
        </button>
        <button type="button" onclick="switchCrmTab('tab-recalls')" id="btn-tab-recalls"
                class="crm-nav-btn pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex items-center gap-1.5 cursor-pointer">
            <span>Preventive Health Recalls</span>
            <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-slate-100 text-slate-700">{{ $reminders->count() }}</span>
        </button>
        <button type="button" onclick="switchCrmTab('tab-campaigns')" id="btn-tab-campaigns"
                class="crm-nav-btn pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex items-center gap-1.5 cursor-pointer">
            <span>Marketing Campaigns & Promotions</span>
            <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-slate-100 text-slate-700">{{ $campaigns->count() }}</span>
        </button>
        <button type="button" onclick="switchCrmTab('tab-doctors')" id="btn-tab-doctors"
                class="crm-nav-btn pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex items-center gap-1.5 cursor-pointer">
            <span>Doctor & Clinic Relations (PRM)</span>
            <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-slate-100 text-slate-700">{{ $topDoctors->count() }}</span>
        </button>
        <button type="button" onclick="switchCrmTab('tab-tickets')" id="btn-tab-tickets"
                class="crm-nav-btn pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex items-center gap-1.5 cursor-pointer">
            <span>Support Tickets & Feedback Reviews</span>
            <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-slate-100 text-slate-700">{{ $tickets->count() }}</span>
        </button>
    </nav>
</div>

<!-- ========================================== -->
<!-- TAB 1: Lead Pipeline & Stages (Kanban Board) -->
<!-- ========================================== -->
<div id="tab-pipeline" class="crm-tab-pane space-y-6">
    <!-- Filter bar -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.crm') }}" class="flex flex-wrap items-center gap-2.5">
            <select name="stage" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg">
                <option value="">All Pipeline Stages</option>
                @foreach($stages as $st)
                    <option value="{{ $st }}" @selected($stageFilter === $st)>{{ $st }}</option>
                @endforeach
            </select>
            <select name="source" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg">
                <option value="">All Acquisition Sources</option>
                @foreach(['WhatsApp', 'Website', 'Health Camp', 'Doctor Referral', 'Google', 'Walk-in'] as $src)
                    <option value="{{ $src }}" @selected($sourceFilter === $src)>{{ $src }}</option>
                @endforeach
            </select>
            @if($stageFilter || $sourceFilter)
                <a href="{{ route('admin.crm') }}" class="text-xs text-rose-600 font-semibold hover:underline">Clear Filters</a>
            @endif
        </form>
        <div class="text-xs text-slate-500 font-medium">
            Showing <strong class="text-slate-900">{{ $leads->count() }}</strong> active pipeline leads
        </div>
    </div>

    <!-- 5-Stage Kanban Columns -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-start">
        @php
            $stageColors = [
                'New' => ['header' => 'bg-sky-50 text-sky-800 border-sky-200', 'badge' => 'bg-sky-100 text-sky-700'],
                'Contacted' => ['header' => 'bg-amber-50 text-amber-800 border-amber-200', 'badge' => 'bg-amber-100 text-amber-800'],
                'Booked' => ['header' => 'bg-indigo-50 text-indigo-800 border-indigo-200', 'badge' => 'bg-indigo-100 text-indigo-700'],
                'Converted' => ['header' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'badge' => 'bg-emerald-100 text-emerald-800'],
                'Lost' => ['header' => 'bg-slate-100 text-slate-700 border-slate-200', 'badge' => 'bg-slate-200 text-slate-700'],
            ];
        @endphp

        @foreach($stages as $st)
            <div class="bg-slate-100/70 rounded-xl p-3 border border-slate-200 space-y-3 min-h-[380px]">
                <div class="flex items-center justify-between px-2 py-1.5 rounded-lg border {{ $stageColors[$st]['header'] }}">
                    <span class="text-xs font-bold">{{ $st }}</span>
                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full {{ $stageColors[$st]['badge'] }}">
                        {{ $leadsByStage[$st]->count() }}
                    </span>
                </div>

                <div class="space-y-2.5">
                    @forelse($leadsByStage[$st] as $lead)
                        <div class="bg-white rounded-xl border border-slate-200 p-3.5 shadow-2xs hover:border-teal-300 transition space-y-2">
                            <div class="flex items-start justify-between gap-1">
                                <strong class="text-sm text-slate-900 leading-snug">{{ $lead->name }}</strong>
                                <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-slate-100 text-slate-600 shrink-0">{{ $lead->source }}</span>
                            </div>

                            <div class="text-xs text-slate-500 font-mono">{{ $lead->phone }}</div>

                            @if($lead->interest)
                                <div class="text-xs text-teal-800 font-semibold bg-teal-50 px-2 py-1 rounded">
                                    {{ $lead->interest }}
                                </div>
                            @endif

                            <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100">
                                <span class="text-slate-400">Value:</span>
                                <strong class="text-slate-800 font-mono">₹{{ number_format($lead->expected_value, 0) }}</strong>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-slate-500">
                                <span>Rep: <strong>{{ $lead->assigned_to }}</strong></span>
                                @if($lead->follow_up_date)
                                    <span class="text-amber-700 font-semibold">Call: {{ $lead->follow_up_date->format('d M') }}</span>
                                @endif
                            </div>

                            @if($lead->notes)
                                <p class="text-[11px] text-slate-500 bg-slate-50 p-1.5 rounded italic">
                                    "{{ Str::limit($lead->notes, 60) }}"
                                </p>
                            @endif

                            <!-- Quick Move Stage Form -->
                            <form method="POST" action="{{ route('admin.crm.leads.update', $lead) }}" class="pt-2 border-t border-slate-100">
                                @csrf @method('PATCH')
                                <div class="flex items-center gap-1">
                                    <select name="status" onchange="this.form.submit()" class="w-full px-2 py-1 text-[11px] bg-slate-50 border border-slate-200 rounded font-semibold text-slate-700">
                                        <option value="" disabled selected>Move stage...</option>
                                        @foreach($stages as $otherStage)
                                            @if($otherStage !== $st)
                                                <option value="{{ $otherStage }}">Move to {{ $otherStage }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-slate-400">
                            No leads in {{ $st }}
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- ========================================== -->
<!-- TAB 2: Preventive Health Recalls & Follow-ups -->
<!-- ========================================== -->
<div id="tab-recalls" class="crm-tab-pane hidden space-y-4">
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Preventive Recall & Follow-up Reminders</h2>
                <p class="text-xs text-slate-500">Automated patient recalls for quarterly diabetic reviews, thyroid follow-ups, and annual health checkups.</p>
            </div>
            <button type="button" onclick="openModal('modal-add-reminder')"
                    class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg shrink-0 cursor-pointer">
                + Schedule Patient Recall
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Patient</th>
                        <th class="px-4 py-3">Recall Program</th>
                        <th class="px-4 py-3">Recommended Investigation</th>
                        <th class="px-4 py-3">Due Date</th>
                        <th class="px-4 py-3">Channel</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Dispatch Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reminders as $rem)
                        @php
                            $isOverdue = $rem->due_date->isPast() && $rem->status === 'Due';
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $rem->patient->name }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $rem->patient->uhid }} · {{ $rem->patient->phone }}</div>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-800">
                                {{ $rem->reminder_title }}
                                <div class="text-[11px] text-slate-400 font-normal">Every {{ $rem->frequency_months }} Months</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-medium text-teal-800">
                                <span class="bg-teal-50 px-2 py-1 rounded border border-teal-100">{{ $rem->test_name }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs font-mono {{ $isOverdue ? 'text-rose-600 font-bold' : 'text-slate-700' }}">
                                {{ $rem->due_date->format('d M Y') }}
                                @if($isOverdue)
                                    <span class="block text-[10px] text-rose-600 uppercase font-bold">Overdue</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <span class="px-2 py-0.5 rounded-full {{ $rem->channel === 'WhatsApp' ? 'bg-emerald-50 text-emerald-700' : ($rem->channel === 'SMS' ? 'bg-sky-50 text-sky-700' : 'bg-amber-50 text-amber-700') }} font-semibold">
                                    {{ $rem->channel }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $rem->status === 'Sent' ? 'bg-sky-50 text-sky-700' : ($rem->status === 'Booked' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ $rem->status }}
                                </span>
                                @if($rem->last_sent_at)
                                    <div class="text-[10px] text-slate-400 mt-0.5">Sent: {{ $rem->last_sent_at->format('d M, h:i A') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('admin.crm.reminders.send', $rem) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-xs transition flex items-center gap-1 ml-auto cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        <span>Send {{ $rem->channel }}</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-xs text-slate-400">
                                No preventive recall reminders scheduled yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- TAB 3: Marketing Campaigns & Health Promotions -->
<!-- ========================================== -->
<div id="tab-campaigns" class="crm-tab-pane hidden space-y-5">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($campaigns as $camp)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <span class="font-mono text-xs font-bold text-teal-700">{{ $camp->code }}</span>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ $camp->status }}
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-slate-900 mt-3">{{ $camp->name }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Audience: <strong>{{ $camp->target_audience }}</strong></p>

                    @if($camp->promo_code)
                        <div class="mt-3 flex items-center justify-between bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <div>
                                <div class="text-[10px] text-slate-400 uppercase font-semibold">Coupon Code</div>
                                <div class="font-mono text-xs font-bold text-slate-900">{{ $camp->promo_code }}</div>
                            </div>
                            <span class="px-2 py-1 text-xs font-bold rounded-lg bg-emerald-100 text-emerald-800">
                                {{ $camp->discount_percent }}% OFF
                            </span>
                        </div>
                    @endif

                    <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                        <div class="bg-slate-50/70 p-2 rounded-lg">
                            <span class="text-slate-400 text-[11px]">Target Leads:</span>
                            <div class="font-bold text-slate-800">{{ $camp->target_leads }} leads</div>
                        </div>
                        <div class="bg-slate-50/70 p-2 rounded-lg">
                            <span class="text-slate-400 text-[11px]">Budget:</span>
                            <div class="font-bold text-slate-800 font-mono">₹{{ number_format($camp->budget, 0) }}</div>
                        </div>
                    </div>

                    <div class="mt-3 text-xs text-slate-500">
                        Valid: <strong>{{ $camp->start_date->format('d M') }}</strong> to <strong>{{ $camp->end_date->format('d M Y') }}</strong>
                    </div>

                    @if($camp->notes)
                        <p class="mt-2 text-[11px] text-slate-600 bg-slate-50 p-2 rounded italic">
                            {{ $camp->notes }}
                        </p>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Leads Generated: <strong class="text-teal-700 font-bold">{{ $camp->leads_count }}</strong></span>
                    <a href="{{ route('admin.crm', ['source' => 'Website']) }}" class="text-teal-600 font-semibold hover:underline">View Leads →</a>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- ========================================== -->
<!-- TAB 4: Doctor & Clinic Relations (PRM) -->
<!-- ========================================== -->
<div id="tab-doctors" class="crm-tab-pane hidden space-y-5">
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Physician & Clinic Relationship Management (PRM)</h2>
                <p class="text-xs text-slate-500">Doctor referral volume, hospital network performance, and digital report engagement.</p>
            </div>
            <a href="{{ route('network.doctors') }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg">
                View Full Doctor Directory →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @foreach($topDoctors as $doc)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                    <div class="flex items-start justify-between">
                        <span class="font-mono text-xs font-bold text-teal-700">{{ $doc->code }}</span>
                        <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-teal-100 text-teal-800">Top Tier</span>
                    </div>
                    <div class="font-bold text-slate-900 text-sm">{{ $doc->name }}</div>
                    <div class="text-xs text-slate-500">{{ $doc->specialization }}</div>
                    <div class="text-xs text-slate-600">{{ $doc->hospital_clinic }}</div>
                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                        <span class="text-slate-400">Total Referrals:</span>
                        <strong class="text-teal-700 font-bold">{{ $doc->samples_count }} investigations</strong>
                    </div>
                    <a href="{{ route('portal.doctor', ['doctor_id' => $doc->id]) }}" class="block text-center text-xs font-semibold text-teal-600 hover:underline pt-1">
                        Open Doctor Portal →
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- TAB 5: Support Tickets & Patient Reviews -->
<!-- ========================================== -->
<div id="tab-tickets" class="crm-tab-pane hidden space-y-5">
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Customer Support Tickets, Inquiries & Patient Reviews</h2>
                <p class="text-xs text-slate-500">Track resolution of billing queries, home collection delays, and patient satisfaction ratings.</p>
            </div>
            <button type="button" onclick="openModal('modal-add-ticket')"
                    class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg shrink-0 cursor-pointer">
                + New Support Ticket
            </button>
        </div>

        <div class="space-y-3">
            @forelse($tickets as $tkt)
                <div class="p-4 rounded-xl border border-slate-200 bg-white hover:border-teal-200 transition space-y-2.5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-teal-700">{{ $tkt->ticket_no }}</span>
                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded bg-slate-100 text-slate-700">{{ $tkt->category }}</span>
                            <span class="px-2 py-0.5 text-[11px] font-bold rounded {{ $tkt->priority === 'High' ? 'bg-rose-100 text-rose-700' : ($tkt->priority === 'Medium' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600') }}">
                                {{ $tkt->priority }} Priority
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($tkt->rating)
                                <span class="px-2 py-0.5 rounded-lg bg-amber-50 text-amber-600 text-xs font-bold">
                                    {{ str_repeat('★', $tkt->rating) }} {{ $tkt->rating }}/5
                                </span>
                            @endif
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $tkt->status === 'Resolved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                {{ $tkt->status }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-bold text-slate-900">{{ $tkt->subject }}</h4>
                        <p class="text-xs text-slate-600 mt-0.5">{{ $tkt->description }}</p>
                    </div>

                    @if($tkt->resolution_notes)
                        <div class="bg-teal-50/70 p-2.5 rounded-lg border border-teal-100 text-xs text-teal-900">
                            <strong>Resolution Note:</strong> {{ $tkt->resolution_notes }}
                            @if($tkt->resolved_by)
                                <span class="block text-[11px] text-teal-700 mt-0.5">— Resolved by {{ $tkt->resolved_by }} on {{ optional($tkt->resolved_at)->format('d M Y, h:i A') }}</span>
                            @endif
                        </div>
                    @endif

                    @if($tkt->status !== 'Resolved')
                        <form method="POST" action="{{ route('admin.crm.tickets.update', $tkt) }}" class="pt-2 border-t border-slate-100 flex flex-wrap items-center gap-2">
                            @csrf @method('PATCH')
                            <input type="text" name="resolution_notes" placeholder="Enter resolution notes..." class="flex-1 min-w-[200px] px-3 py-1.5 text-xs border border-slate-200 rounded-lg">
                            <select name="status" class="px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg">
                                <option value="In Progress">In Progress</option>
                                <option value="Resolved">Mark as Resolved</option>
                            </select>
                            <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-semibold rounded-lg cursor-pointer">Update</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="text-center py-8 text-xs text-slate-400">
                    No support tickets or feedback recorded yet.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 1: Add New Lead -->
<!-- ========================================== -->
<div id="modal-add-lead" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full p-6 text-left">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-900">Add Patient Lead to Pipeline</h3>
            <button type="button" onclick="closeModal('modal-add-lead')" class="text-slate-400 hover:text-slate-700 text-base font-bold cursor-pointer">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.crm.leads') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Branch</label>
                <select name="branch_id" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number *</label>
                    <input type="text" name="phone" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Lead Source</label>
                    <select name="source" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                        <option>WhatsApp</option>
                        <option>Website</option>
                        <option>Health Camp</option>
                        <option>Doctor Referral</option>
                        <option>Google</option>
                        <option>Walk-in</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Associated Campaign</label>
                    <select name="campaign_name" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                        <option value="">General Enquiries</option>
                        @foreach($campaigns as $camp)
                            <option value="{{ $camp->name }}">{{ $camp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Expected Revenue (₹)</label>
                    <input type="number" name="expected_value" value="2500" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Package / Test Interest</label>
                    <input type="text" name="interest" placeholder="e.g. Full Body Checkup" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Next Follow-up Date</label>
                    <input type="date" name="follow_up_date" value="{{ today()->addDay()->toDateString() }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Assigned CRM Rep</label>
                <input type="text" name="assigned_to" value="Sneha (CRM)" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Notes / Call Summary</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg"></textarea>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-add-lead')" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg text-slate-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-xs font-semibold rounded-lg">Save Lead</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: Launch Campaign -->
<!-- ========================================== -->
<div id="modal-add-campaign" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full p-6 text-left">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-900">Launch New Health Checkup Promotion</h3>
            <button type="button" onclick="closeModal('modal-add-campaign')" class="text-slate-400 hover:text-slate-700 text-base font-bold cursor-pointer">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.crm.campaigns') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Campaign Name *</label>
                <input type="text" name="name" required placeholder="e.g. Monsoon Immunity Drive" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Target Audience</label>
                <input type="text" name="target_audience" required placeholder="e.g. Working Professionals & Diabetics" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Promo Coupon Code</label>
                    <input type="text" name="promo_code" placeholder="HEALTH2026" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg uppercase">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Discount %</label>
                    <input type="number" name="discount_percent" value="20" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Target Leads</label>
                    <input type="number" name="target_leads" value="100" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Marketing Budget (₹)</label>
                    <input type="number" name="budget" value="25000" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ today()->toDateString() }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ today()->addMonths(2)->toDateString() }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Campaign Strategy & Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg"></textarea>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-add-campaign')" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg text-slate-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-xs font-semibold rounded-lg">Launch Campaign</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 3: Schedule Preventive Recall -->
<!-- ========================================== -->
<div id="modal-add-reminder" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full p-6 text-left">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-900">Schedule Preventive Health Recall</h3>
            <button type="button" onclick="closeModal('modal-add-reminder')" class="text-slate-400 hover:text-slate-700 text-base font-bold cursor-pointer">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.crm.reminders') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Select Patient *</label>
                <select name="patient_id" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->uhid }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Recall Program Title *</label>
                <input type="text" name="reminder_title" required placeholder="e.g. Quarterly Diabetic HbA1c Recall" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Recommended Investigation *</label>
                <input type="text" name="test_name" required placeholder="e.g. HbA1c & Fasting Glucose" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Frequency (Months)</label>
                    <input type="number" name="frequency_months" value="3" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Due Date</label>
                    <input type="date" name="due_date" value="{{ today()->addMonths(3)->toDateString() }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Notification Channel</label>
                <select name="channel" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    <option>WhatsApp</option>
                    <option>SMS</option>
                    <option>Call</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Clinical Remarks</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg"></textarea>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-add-reminder')" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg text-slate-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-xs font-semibold rounded-lg">Schedule Recall</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 4: Log Support Ticket / Review -->
<!-- ========================================== -->
<div id="modal-add-ticket" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full p-6 text-left">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-base font-bold text-slate-900">Log Support Ticket / Patient Review</h3>
            <button type="button" onclick="closeModal('modal-add-ticket')" class="text-slate-400 hover:text-slate-700 text-base font-bold cursor-pointer">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.crm.tickets') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Patient (Optional)</label>
                <select name="patient_id" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
                    <option value="">General Inquirer / Walk-in</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->uhid }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Subject / Issue Summary *</label>
                <input type="text" name="subject" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Category</label>
                    <select name="category" class="w-full px-2 py-2 text-xs border border-slate-200 rounded-lg">
                        <option>Report</option>
                        <option>Billing</option>
                        <option>Home Collection</option>
                        <option>Feedback</option>
                        <option>Complaint</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Priority</label>
                    <select name="priority" class="w-full px-2 py-2 text-xs border border-slate-200 rounded-lg">
                        <option>Medium</option>
                        <option>High</option>
                        <option>Low</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rating (Stars)</label>
                    <select name="rating" class="w-full px-2 py-2 text-xs border border-slate-200 rounded-lg">
                        <option value="5">★★★★★ 5</option>
                        <option value="4">★★★★☆ 4</option>
                        <option value="3">★★★☆☆ 3</option>
                        <option value="2">★★☆☆☆ 2</option>
                        <option value="1">★☆☆☆☆ 1</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Detailed Description *</label>
                <textarea name="description" rows="3" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg"></textarea>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modal-add-ticket')" class="px-4 py-2 border border-slate-200 text-xs font-semibold rounded-lg text-slate-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-teal-600 text-white text-xs font-semibold rounded-lg">Save Ticket</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchCrmTab(tabId) {
    document.querySelectorAll('.crm-tab-pane').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.crm-nav-btn').forEach(btn => {
        btn.classList.remove('border-teal-600', 'text-teal-700', 'font-bold');
        btn.classList.add('border-transparent', 'text-slate-500');
    });

    const activePane = document.getElementById(tabId);
    if (activePane) activePane.classList.remove('hidden');

    const activeBtn = document.getElementById('btn-' + tabId);
    if (activeBtn) {
        activeBtn.classList.remove('border-transparent', 'text-slate-500');
        activeBtn.classList.add('border-teal-600', 'text-teal-700', 'font-bold');
    }
}

function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('hidden');
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
}
</script>
@endsection
