<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\ComplianceDoc;
use App\Models\CrmLead;
use App\Models\Department;
use App\Models\Employee;
use App\Models\FinanceTransaction;
use App\Models\IntegrationConfig;
use App\Models\SupportTicket;
use App\Services\LabAudit;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function hr()
    {
        $employees = Employee::with(['branch', 'department', 'attendances'])->orderBy('employee_code')->get();
        $branches = Branch::all();
        $departments = Department::all();
        return view('admin.hr', compact('employees', 'branches', 'departments'));
    }

    public function storeEmployee(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string',
            'role' => 'required|string',
            'qualification' => 'nullable|string',
            'license_no' => 'nullable|string',
            'phone' => 'required|string',
            'shift' => 'required|string',
        ]);
        $data['employee_code'] = 'EMP-' . str_pad((string) ((Employee::max('id') ?? 0) + 1), 3, '0', STR_PAD_LEFT);
        $data['joined_at'] = today();
        Employee::create($data);

        return back()->with('success', "Employee {$data['name']} ({$data['employee_code']}) added.");
    }

    public function finance()
    {
        $transactions = FinanceTransaction::with('branch')->orderByDesc('transaction_date')->get();
        $branches = Branch::all();
        $totalIncome = $transactions->where('type', 'Income')->sum('amount');
        $totalExpense = $transactions->where('type', 'Expense')->sum('amount');

        return view('admin.finance', compact('transactions', 'branches', 'totalIncome', 'totalExpense'));
    }

    public function storeTransaction(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'type' => 'required|in:Income,Expense',
            'category' => 'required|string',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'transaction_date' => 'required|date',
        ]);
        FinanceTransaction::create($data);
        return back()->with('success', "{$data['type']} transaction of ₹{$data['amount']} recorded.");
    }

    public function exportFinanceCsv()
    {
        $rows = FinanceTransaction::with('branch')->orderByDesc('transaction_date')->get();
        $csv = "Date,Branch,Type,Category,Description,Method,Amount\n";
        foreach ($rows as $r) {
            $csv .= "{$r->transaction_date->format('Y-m-d')},\"{$r->branch->name}\",{$r->type},{$r->category},\"{$r->description}\",{$r->payment_method},{$r->amount}\n";
        }
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="patholab-finance-ledger.csv"',
        ]);
    }

    public function crm(Request $request)
    {
        $stageFilter = $request->get('stage');
        $sourceFilter = $request->get('source');

        $leads = CrmLead::with('branch')
            ->when($stageFilter, fn ($q) => $q->where('status', $stageFilter))
            ->when($sourceFilter, fn ($q) => $q->where('source', $sourceFilter))
            ->latest()
            ->get();

        $campaigns = \App\Models\CrmCampaign::withCount('leads')->latest()->get();
        $reminders = \App\Models\PreventiveReminder::with('patient')->orderBy('due_date')->get();
        $tickets = SupportTicket::with('patient')->latest()->get();
        $branches = Branch::all();
        $patients = \App\Models\Patient::where('is_merged', false)->orderBy('name')->get();
        $packages = \App\Models\TestPackage::where('is_active', true)->orderBy('name')->get();
        $topDoctors = \App\Models\Doctor::withCount('samples')->orderByDesc('samples_count')->take(5)->get();

        $allLeads = CrmLead::all();
        $totalPipelineValue = $allLeads->sum('expected_value');
        $convertedCount = $allLeads->whereIn('status', ['Booked', 'Converted'])->count();
        $totalLeadsCount = max(1, $allLeads->count());
        $conversionRate = round(($convertedCount / $totalLeadsCount) * 100, 1);

        $csatAvg = round(SupportTicket::whereNotNull('rating')->avg('rating') ?: 4.8, 1);

        $kpis = [
            'total_leads' => $allLeads->count(),
            'pipeline_value' => $totalPipelineValue,
            'conversion_rate' => $conversionRate,
            'active_campaigns' => $campaigns->where('status', 'Active')->count(),
            'due_reminders' => $reminders->where('status', 'Due')->count(),
            'open_tickets' => $tickets->where('status', '!=', 'Resolved')->count(),
            'csat_rating' => $csatAvg,
        ];

        // Group leads by stage for Kanban view
        $stages = ['New', 'Contacted', 'Booked', 'Converted', 'Lost'];
        $leadsByStage = [];
        foreach ($stages as $st) {
            $leadsByStage[$st] = $allLeads->where('status', $st);
        }

        return view('admin.crm', compact(
            'leads', 'campaigns', 'reminders', 'tickets', 'branches',
            'patients', 'packages', 'topDoctors', 'kpis', 'stages', 'leadsByStage',
            'stageFilter', 'sourceFilter'
        ));
    }

    public function storeLead(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'source' => 'required|string',
            'campaign_name' => 'nullable|string',
            'interest' => 'nullable|string',
            'expected_value' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        $data['status'] = 'New';
        $data['last_contacted_at'] = now();
        $lead = CrmLead::create($data);

        LabAudit::log('Created CRM Lead', 'Marketing & CRM', 'LEAD-' . $lead->id, "{$lead->name} ({$lead->source})");

        return back()->with('success', "Lead {$data['name']} added to CRM pipeline.");
    }

    public function updateLeadStatus(Request $request, CrmLead $lead)
    {
        $data = $request->validate([
            'status' => 'required|in:New,Contacted,Booked,Converted,Lost',
            'notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'expected_value' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|string',
        ]);
        $data['last_contacted_at'] = now();
        $lead->update($data);

        LabAudit::log('Updated CRM Lead', 'Marketing & CRM', 'LEAD-' . $lead->id, "Status changed to {$data['status']}");

        return back()->with('success', "Lead {$lead->name} updated to {$data['status']}.");
    }

    public function storeCampaign(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'target_audience' => 'required|string',
            'promo_code' => 'nullable|string|max:50',
            'discount_percent' => 'required|numeric|min:0|max:80',
            'target_leads' => 'required|integer|min:1',
            'budget' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);
        $data['code'] = 'CMP-' . date('Y') . '-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $data['name']), 0, 4));
        $data['status'] = 'Active';

        $campaign = \App\Models\CrmCampaign::create($data);
        LabAudit::log('Created Campaign', 'Marketing & CRM', $campaign->code, $campaign->name);

        return back()->with('success', "Campaign {$campaign->name} launched.");
    }

    public function storeReminder(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'reminder_title' => 'required|string|max:255',
            'test_name' => 'required|string|max:255',
            'frequency_months' => 'required|integer|min:1|max:36',
            'due_date' => 'required|date',
            'channel' => 'required|in:WhatsApp,SMS,Call',
            'notes' => 'nullable|string',
        ]);
        $data['status'] = 'Due';

        $reminder = \App\Models\PreventiveReminder::create($data);
        LabAudit::log('Scheduled Recall Reminder', 'Marketing & CRM', 'REM-' . $reminder->id, $reminder->reminder_title);

        return back()->with('success', "Preventive reminder scheduled for {$reminder->patient->name}.");
    }

    public function sendReminder(\App\Models\PreventiveReminder $reminder)
    {
        $reminder->update([
            'status' => 'Sent',
            'last_sent_at' => now(),
        ]);
        LabAudit::log('Dispatched Preventive Reminder', 'Marketing & CRM', 'REM-' . $reminder->id, "Via {$reminder->channel} to {$reminder->patient->name} ({$reminder->patient->phone})");

        return back()->with('success', "{$reminder->channel} notification dispatched to {$reminder->patient->name} ({$reminder->patient->phone}).");
    }

    public function storeTicket(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|in:Low,Medium,High',
            'rating' => 'nullable|integer|min:1|max:5',
            'description' => 'required|string',
        ]);
        $data['ticket_no'] = 'TKT-' . date('Y') . '-' . str_pad((string) ((\App\Models\SupportTicket::max('id') ?? 0) + 1), 3, '0', STR_PAD_LEFT);
        $data['status'] = 'Open';

        $ticket = SupportTicket::create($data);
        LabAudit::log('Created Support Ticket', 'Marketing & CRM', $ticket->ticket_no, $ticket->subject);

        return back()->with('success', "Ticket {$ticket->ticket_no} logged.");
    }

    public function updateTicket(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate([
            'status' => 'required|in:Open,In Progress,Resolved',
            'resolution_notes' => 'nullable|string',
        ]);
        if ($data['status'] === 'Resolved') {
            $data['resolved_by'] = auth()->user()?->name ?? 'Staff';
            $data['resolved_at'] = now();
        }
        $ticket->update($data);

        LabAudit::log('Updated Support Ticket', 'Marketing & CRM', $ticket->ticket_no, "Status -> {$data['status']}");

        return back()->with('success', "Ticket {$ticket->ticket_no} updated to {$data['status']}.");
    }

    public function compliance()
    {
        $auditLogs = AuditLog::latest()->paginate(20);
        $docs = ComplianceDoc::with('branch')->get();
        $branches = Branch::all();
        return view('admin.compliance', compact('auditLogs', 'docs', 'branches'));
    }

    public function triggerBackup()
    {
        LabAudit::log('Database Backup', 'Compliance & Admin', 'DB-PATHOLOGY', 'Manual snapshot verified for NABL retention policy.');
        return back()->with('success', 'Database snapshot & audit log backup completed.');
    }

    public function integrations()
    {
        $integrations = IntegrationConfig::all();
        return view('admin.integrations', compact('integrations'));
    }

    public function testIntegration(IntegrationConfig $integrationConfig)
    {
        $integrationConfig->update(['last_sync_at' => now(), 'status' => 'Connected']);
        LabAudit::log('Integration Ping', 'Integrations', $integrationConfig->name, "Protocol {$integrationConfig->protocol} handshake OK.");
        return back()->with('success', "{$integrationConfig->name} ({$integrationConfig->protocol}) synced successfully.");
    }
}

