<?php

namespace App\Http\Controllers;

use App\Models\CriticalAlert;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LabOrder;
use App\Models\LabReport;
use App\Models\TestResult;
use App\Services\LabAudit;
use Illuminate\Http\Request;

class LabWorkflowController extends Controller
{
    public function departments(Request $request)
    {
        $slug = $request->get('dept');
        $departments = Department::withCount([
            'labOrders as pending_count' => fn ($q) => $q->whereIn('status', ['Pending', 'Sample Received', 'In-Progress']),
            'labOrders as completed_count' => fn ($q) => $q->whereIn('status', ['Approved', 'Delivered']),
        ])->get();

        $activeDept = $slug ? $departments->firstWhere('slug', $slug) : $departments->first();
        $orders = LabOrder::with(['patient', 'test.parameters', 'sample', 'technician', 'results', 'report'])
            ->when($activeDept, fn ($q) => $q->where('department_id', $activeDept->id))
            ->latest()
            ->take(20)
            ->get();

        return view('lab.departments', compact('departments', 'activeDept', 'orders'));
    }

    public function worklist(Request $request)
    {
        $deptId = $request->get('department_id');
        $status = $request->get('status');
        $priority = $request->get('priority');

        $orders = LabOrder::with(['patient', 'test.parameters', 'department', 'sample', 'technician', 'results'])
            ->when($deptId, fn ($q) => $q->where('department_id', $deptId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($priority, fn ($q) => $q->where('priority', $priority))
            ->orderByRaw("FIELD(priority, 'STAT', 'Urgent', 'Routine')")
            ->latest()
            ->paginate(15);

        $departments = Department::orderBy('name')->get();
        $technicians = Employee::whereIn('role', ['Technician', 'Pathologist', 'Radiologist'])->get();

        return view('lab.worklist', compact('orders', 'departments', 'technicians', 'deptId', 'status', 'priority'));
    }

    public function updateWorklist(Request $request, LabOrder $labOrder)
    {
        $data = $request->validate([
            'status' => 'required|in:Pending,Sample Received,In-Progress,Rerun,Result Entered,Verified,Approved,Delivered',
            'assigned_technician_id' => 'nullable|exists:employees,id',
            'rerun_reason' => 'nullable|string',
            'dilution_factor' => 'nullable|string',
        ]);

        if ($data['status'] === 'Rerun') {
            $data['is_repeat'] = true;
        }
        $labOrder->update($data);
        LabAudit::log('Updated Worklist', 'Lab Worklist', 'ORD-' . $labOrder->id, "Status -> {$data['status']}");

        return back()->with('success', "Order #ORD-{$labOrder->id} updated to {$data['status']}.");
    }

    public function results(Request $request)
    {
        $deptId = $request->get('department_id');
        $status = $request->get('status');
        $priority = $request->get('priority');
        $search = $request->get('q');

        $orders = LabOrder::with(['patient', 'test.parameters', 'department', 'sample', 'results', 'technician', 'report'])
            ->when($deptId, fn ($q) => $q->where('department_id', $deptId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($priority, fn ($q) => $q->where('priority', $priority))
            ->when($search, fn ($q) => $q->where(function ($sub) use ($search) {
                $sub->whereHas('patient', fn ($pq) => $pq->where('name', 'like', "%{$search}%")->orWhere('uhid', 'like', "%{$search}%"))
                    ->orWhereHas('sample', fn ($sq) => $sq->where('sample_id_code', 'like', "%{$search}%")->orWhere('barcode_value', 'like', "%{$search}%"));
            }))
            ->orderByRaw("FIELD(priority, 'STAT', 'Urgent', 'Routine')")
            ->latest()
            ->paginate(15);

        $departments = Department::orderBy('name')->get();

        $counts = [
            'all' => LabOrder::count(),
            'pending' => LabOrder::whereIn('status', ['Pending', 'Sample Received', 'In-Progress'])->count(),
            'entered' => LabOrder::where('status', 'Result Entered')->count(),
            'verified' => LabOrder::where('status', 'Verified')->count(),
            'approved' => LabOrder::whereIn('status', ['Approved', 'Delivered'])->count(),
        ];

        return view('lab.results', compact('orders', 'departments', 'deptId', 'status', 'priority', 'search', 'counts'));
    }

    public function showResultDetail(LabOrder $labOrder)
    {
        $labOrder->load([
            'patient',
            'test.parameters',
            'department',
            'sample.collector',
            'sample.doctor',
            'technician',
            'results',
            'report',
            'branch',
            'criticalAlerts',
        ]);

        return view('lab.results_detail', compact('labOrder'));
    }

    public function saveResults(Request $request, LabOrder $labOrder)
    {
        $labOrder->load(['patient', 'test.parameters', 'sample']);
        $values = $request->input('results', []);
        $action = $request->input('action_type', 'save'); // save, verify, approve

        foreach ($labOrder->test->parameters as $param) {
            $val = $values[$param->id] ?? null;
            if ($val === null || $val === '') {
                continue;
            }

            $isFemale = $labOrder->patient->gender === 'Female';
            $min = $isFemale ? ($param->ref_min_female ?? $param->ref_min_male) : $param->ref_min_male;
            $max = $isFemale ? ($param->ref_max_female ?? $param->ref_max_male) : $param->ref_max_male;
            $refRange = ($min !== null && $max !== null) ? "{$min} - {$max}" : 'Reference';

            $flag = 'Normal';
            if (is_numeric($val)) {
                $num = (float) $val;
                if ($param->critical_high !== null && $num >= (float) $param->critical_high) {
                    $flag = 'Critical High';
                } elseif ($param->critical_low !== null && $num <= (float) $param->critical_low) {
                    $flag = 'Critical Low';
                } elseif ($max !== null && $num > (float) $max) {
                    $flag = 'High';
                } elseif ($min !== null && $num < (float) $min) {
                    $flag = 'Low';
                }
            }

            TestResult::updateOrCreate(
                ['lab_order_id' => $labOrder->id, 'test_parameter_id' => $param->id],
                [
                    'parameter_name' => $param->name,
                    'result_value' => (string) $val,
                    'unit' => $param->unit,
                    'reference_range' => $refRange,
                    'flag' => $flag,
                    'entered_by' => 'Rohan Kulkarni',
                ]
            );

            if (in_array($flag, ['Critical High', 'Critical Low'], true)) {
                CriticalAlert::firstOrCreate(
                    ['lab_order_id' => $labOrder->id, 'parameter_name' => $param->name, 'status' => 'Open'],
                    [
                        'patient_id' => $labOrder->patient_id,
                        'doctor_id' => $labOrder->sample->doctor_id ?? null,
                        'result_value' => "{$val} {$param->unit}",
                        'threshold_info' => $flag === 'Critical High' ? "Critical High (>= {$param->critical_high})" : "Critical Low (<= {$param->critical_low})",
                        'alert_level' => 'Emergency',
                        'notified_pathologist' => true,
                        'notified_technician' => true,
                        'notified_doctor' => true,
                        'communication_notes' => 'Auto-detected on result entry.',
                    ]
                );
            }
        }

        if ($action === 'approve') {
            $labOrder->update([
                'status' => 'Approved',
                'completed_at' => now(),
                'verified_by' => $labOrder->verified_by ?? 'Rohan Kulkarni',
                'verified_at' => $labOrder->verified_at ?? now(),
                'approved_by' => 'Dr. Ananya Sharma',
                'approved_at' => now(),
                'pathologist_signature' => 'Dr. Ananya Sharma, MD (Pathology) | KMC-45821',
            ]);

            LabReport::firstOrCreate(
                ['lab_order_id' => $labOrder->id],
                [
                    'patient_id' => $labOrder->patient_id,
                    'invoice_id' => $labOrder->sample->invoice_id ?? null,
                    'report_no' => 'RPT-' . date('Y') . '-' . str_pad((string) ((LabReport::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT),
                    'verification_code' => 'QR-PL-' . rand(100000, 999999),
                    'status' => 'Approved',
                    'approved_by' => 'Dr. Ananya Sharma',
                    'approved_at' => now(),
                ]
            );
            LabAudit::log('Approved Result', 'Result Verification', 'ORD-' . $labOrder->id, 'Electronically signed & report generated.');
        } elseif ($action === 'verify') {
            $labOrder->update(['status' => 'Verified', 'verified_by' => 'Rohan Kulkarni', 'verified_at' => now()]);
            LabAudit::log('Verified Result', 'Result Verification', 'ORD-' . $labOrder->id, 'Submitted for Pathologist review.');
        } else {
            $labOrder->update(['status' => 'Result Entered']);
            LabAudit::log('Entered Result', 'Result Verification', 'ORD-' . $labOrder->id, 'Saved draft results.');
        }

        return back()->with('success', "Results saved for {$labOrder->test->name} ({$labOrder->patient->name}).");
    }

    public function reports()
    {
        $reports = LabReport::with(['patient', 'labOrder.test', 'labOrder.results', 'labOrder.sample.doctor'])
            ->latest()
            ->paginate(15);

        return view('lab.reports', compact('reports'));
    }

    public function showReport(Request $request, LabReport $labReport)
    {
        $lang = $request->get('lang', $labReport->language ?? 'en');
        $labReport->load(['patient', 'labOrder.test', 'labOrder.results', 'labOrder.sample.doctor', 'labOrder.branch']);
        return view('lab.report_print', compact('labReport', 'lang'));
    }

    public function deliverReport(Request $request, LabReport $labReport)
    {
        $channels = $request->input('channels', ['WhatsApp', 'Email']);
        $labReport->update([
            'status' => 'Delivered',
            'delivered_via' => $channels,
            'delivered_at' => now(),
        ]);
        if ($labReport->labOrder) {
            $labReport->labOrder->update(['status' => 'Delivered']);
        }
        LabAudit::log('Delivered Report', 'Report Delivery', $labReport->report_no, 'Channels: ' . implode(', ', $channels));

        return back()->with('success', "Report {$labReport->report_no} delivered via " . implode(', ', $channels) . '.');
    }

    public function criticalAlerts()
    {
        $alerts = CriticalAlert::with(['patient', 'doctor', 'labOrder.test', 'labOrder.department'])
            ->latest()
            ->paginate(15);

        return view('lab.critical_alerts', compact('alerts'));
    }

    public function acknowledgeAlert(Request $request, CriticalAlert $criticalAlert)
    {
        $criticalAlert->update([
            'status' => $request->input('status', 'Acknowledged'),
            'acknowledged_by' => 'Dr. Ananya Sharma',
            'acknowledged_at' => now(),
            'notified_doctor' => true,
            'communication_notes' => $request->input('communication_notes', 'Doctor notified via phone & acknowledged.'),
        ]);
        LabAudit::log('Acknowledged Critical Alert', 'Critical Alerts', 'ALT-' . $criticalAlert->id, $criticalAlert->parameter_name . ' = ' . $criticalAlert->result_value);

        return back()->with('success', 'Critical value alert acknowledged and logged.');
    }
}

