<?php

namespace App\Http\Controllers;

use App\Models\Analyzer;
use App\Models\AnalyzerLog;
use App\Models\Branch;
use App\Models\CapaIncident;
use App\Models\Department;
use App\Models\PurchaseOrder;
use App\Models\QcRecord;
use App\Models\Reagent;
use App\Services\LabAudit;
use Illuminate\Http\Request;

class QualityInventoryController extends Controller
{
    public function equipment()
    {
        $analyzers = Analyzer::with(['branch', 'department', 'logs'])->get();
        $branches = Branch::where('is_active', true)->get();
        $departments = Department::orderBy('name')->get();

        return view('operations.equipment', compact('analyzers', 'branches', 'departments'));
    }

    public function storeAnalyzer(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string',
            'manufacturer' => 'required|string',
            'model' => 'required|string',
            'location' => 'required|string',
            'interface_type' => 'required|string',
        ]);
        $data['equipment_code'] = 'EQ-' . str_pad((string) ((Analyzer::max('id') ?? 0) + 1), 3, '0', STR_PAD_LEFT);
        $data['last_calibrated_at'] = today();
        $data['next_calibration_at'] = today()->addDays(30);
        Analyzer::create($data);

        return back()->with('success', "Analyzer {$data['name']} registered.");
    }

    public function logAnalyzerEvent(Request $request, Analyzer $analyzer)
    {
        $data = $request->validate([
            'log_type' => 'required|in:Calibration,Preventive Maintenance,Breakdown,Service',
            'description' => 'required|string',
            'downtime_minutes' => 'nullable|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
        ]);
        AnalyzerLog::create([
            'analyzer_id' => $analyzer->id,
            'log_type' => $data['log_type'],
            'description' => $data['description'],
            'performed_by' => 'Biomedical Engineer',
            'logged_at' => now(),
            'downtime_minutes' => $data['downtime_minutes'] ?? 0,
            'cost' => $data['cost'] ?? 0,
        ]);
        if ($data['log_type'] === 'Breakdown') {
            $analyzer->update(['status' => 'Breakdown', 'downtime_hours' => $analyzer->downtime_hours + (($data['downtime_minutes'] ?? 60) / 60)]);
        } elseif ($data['log_type'] === 'Calibration') {
            $analyzer->update(['status' => 'Online', 'last_calibrated_at' => today(), 'next_calibration_at' => today()->addDays(30)]);
        } else {
            $analyzer->update(['status' => 'Online', 'last_maintenance_at' => today(), 'next_maintenance_at' => today()->addDays(30)]);
        }

        return back()->with('success', "{$data['log_type']} logged for {$analyzer->name}.");
    }

    public function inventory()
    {
        $reagents = Reagent::with(['branch', 'department'])->orderBy('expiry_date')->get();
        $purchaseOrders = PurchaseOrder::with('branch')->latest()->get();
        $branches = Branch::where('is_active', true)->get();
        $departments = Department::orderBy('name')->get();
        $totalValuation = $reagents->sum(fn ($r) => $r->stock_qty * $r->unit_cost);

        return view('operations.inventory', compact('reagents', 'purchaseOrders', 'branches', 'departments', 'totalValuation'));
    }

    public function storeReagent(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string',
            'category' => 'required|in:Reagent,Consumable,Control',
            'supplier_name' => 'required|string',
            'batch_no' => 'required|string',
            'expiry_date' => 'required|date',
            'stock_qty' => 'required|numeric|min:0',
            'min_stock_qty' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'unit_cost' => 'required|numeric|min:0',
        ]);
        $data['code'] = 'INV-' . str_pad((string) ((Reagent::max('id') ?? 0) + 101), 3, '0', STR_PAD_LEFT);
        $data['status'] = $data['stock_qty'] <= $data['min_stock_qty'] ? 'Low Stock' : 'In Stock';
        Reagent::create($data);

        return back()->with('success', "Reagent {$data['name']} added to inventory.");
    }

    public function adjustStock(Request $request, Reagent $reagent)
    {
        $delta = (float) $request->input('delta', 0);
        $newQty = max(0, $reagent->stock_qty + $delta);
        $status = $newQty <= 0 ? 'Out of Stock' : ($newQty <= $reagent->min_stock_qty ? 'Low Stock' : 'In Stock');
        $reagent->update(['stock_qty' => $newQty, 'status' => $status]);

        return back()->with('success', "Stock updated for {$reagent->name}: {$newQty} {$reagent->unit}.");
    }

    public function qualityControl()
    {
        $qcRecords = QcRecord::with(['analyzer', 'department'])->orderBy('recorded_at')->get();
        $capas = CapaIncident::with('branch')->latest()->get();
        $analyzers = Analyzer::all();
        $departments = Department::all();
        $branches = Branch::all();

        return view('operations.quality_control', compact('qcRecords', 'capas', 'analyzers', 'departments', 'branches'));
    }

    public function storeQc(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'analyzer_id' => 'nullable|exists:analyzers,id',
            'department_id' => 'required|exists:departments,id',
            'material_name' => 'required|string',
            'lot_number' => 'required|string',
            'control_level' => 'required|string',
            'parameter_name' => 'required|string',
            'target_mean' => 'required|numeric',
            'sd_value' => 'required|numeric|min:0.001',
            'measured_value' => 'required|numeric',
        ]);

        $z = round(($data['measured_value'] - $data['target_mean']) / $data['sd_value'], 2);
        $absZ = abs($z);
        $rule = $absZ >= 3 ? '1-3s' : ($absZ >= 2 ? '1-2s' : 'OK');
        $status = $absZ >= 3 ? 'Rejected' : ($absZ >= 2 ? 'Warning' : 'Accepted');

        QcRecord::create(array_merge($data, [
            'expiry_date' => today()->addMonths(4),
            'z_score' => $z,
            'westgard_rule' => $rule,
            'status' => $status,
            'recorded_by' => 'Rohan Kulkarni',
            'approved_by' => $status === 'Accepted' ? 'Dr. Ananya Sharma' : null,
            'recorded_at' => now(),
        ]));

        LabAudit::log('Recorded QC Run', 'Quality Control', $data['parameter_name'], "Z-Score: {$z} ({$rule})");

        return back()->with('success', "QC run recorded: Z-Score {$z} ({$rule} - {$status}).");
    }
}

