<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\HomeCollection;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\Sample;
use App\Models\Test;
use App\Services\LabAudit;
use Illuminate\Http\Request;

class SampleController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $samples = Sample::with(['branch', 'patient', 'collector', 'transferredToBranch', 'labOrders.test'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        $patients = Patient::where('is_merged', false)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->get();
        $collectors = Employee::whereIn('role', ['Phlebotomist', 'Technician'])->get();
        $tests = Test::where('is_active', true)->orderBy('name')->get();

        return view('samples.index', compact('samples', 'patients', 'branches', 'collectors', 'tests', 'status'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'patient_id' => 'required|exists:patients,id',
            'sample_type' => 'required|string',
            'container_type' => 'required|string',
            'quantity_ml' => 'required|numeric|min:0.5',
            'collection_location' => 'required|string',
            'collector_id' => 'nullable|exists:employees,id',
            'storage_location' => 'nullable|string',
            'test_id' => 'nullable|exists:tests,id',
        ]);

        $seq = str_pad((string) ((Sample::max('id') ?? 0) + 1), 3, '0', STR_PAD_LEFT);
        $sample = Sample::create([
            'branch_id' => $data['branch_id'],
            'patient_id' => $data['patient_id'],
            'sample_id_code' => 'SMP-' . date('ymd') . '-' . $seq,
            'accession_no' => 'ACC-' . date('Y') . '-' . (8800 + (Sample::max('id') ?? 0) + 1),
            'barcode_value' => 'PL' . date('ymd') . $seq,
            'sample_type' => $data['sample_type'],
            'container_type' => $data['container_type'],
            'quantity_ml' => $data['quantity_ml'],
            'collected_at' => now(),
            'collection_location' => $data['collection_location'],
            'collector_id' => $data['collector_id'] ?? null,
            'storage_location' => $data['storage_location'] ?? 'Rack A-01',
            'status' => 'Collected',
            'is_stat' => $request->boolean('is_stat'),
        ]);

        if (!empty($data['test_id'])) {
            $test = Test::find($data['test_id']);
            LabOrder::create([
                'branch_id' => $sample->branch_id,
                'sample_id' => $sample->id,
                'patient_id' => $sample->patient_id,
                'test_id' => $test->id,
                'department_id' => $test->department_id,
                'priority' => $sample->is_stat ? 'STAT' : 'Routine',
                'status' => 'Pending',
                'tat_due_at' => now()->addHours($test->tat_hours),
            ]);
        }

        LabAudit::log('Registered Sample', 'Sample & Accession', $sample->sample_id_code, "Barcode: {$sample->barcode_value}");

        return back()->with('success', "Sample {$sample->sample_id_code} registered with barcode {$sample->barcode_value}.");
    }

    public function updateStatus(Request $request, Sample $sample)
    {
        $data = $request->validate([
            'status' => 'required|in:Registered,Collected,In-Transit,Received,Processing,Completed,Rejected,Disposed',
            'rejection_reason' => 'nullable|string',
            'transferred_to_branch_id' => 'nullable|exists:branches,id',
            'storage_location' => 'nullable|string',
        ]);

        if ($data['status'] === 'Received' && !$sample->received_at) {
            $data['received_at'] = now();
        }
        if ($data['status'] === 'Disposed' && !$sample->disposal_date) {
            $data['disposal_date'] = today();
        }

        $sample->update($data);
        LabAudit::log('Updated Sample Status', 'Sample & Accession', $sample->sample_id_code, "Status -> {$data['status']}");

        return back()->with('success', "Sample {$sample->sample_id_code} updated to {$data['status']}.");
    }

    public function phlebotomy(Request $request)
    {
        $collections = HomeCollection::with(['branch', 'patient', 'phlebotomist'])->latest()->paginate(15);
        $phlebotomists = Employee::where('role', 'Phlebotomist')->get();
        $patients = Patient::where('is_merged', false)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->get();

        return view('phlebotomy.index', compact('collections', 'phlebotomists', 'patients', 'branches'));
    }

    public function storeHomeCollection(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'patient_id' => 'required|exists:patients,id',
            'phlebotomist_id' => 'nullable|exists:employees,id',
            'address' => 'required|string',
            'route_zone' => 'required|string',
            'scheduled_date' => 'required|date',
            'time_slot' => 'required|string',
            'collection_charge' => 'required|numeric|min:0',
            'cash_to_collect' => 'nullable|numeric|min:0',
        ]);

        $data['booking_code'] = 'HC-' . date('Y') . '-' . (500 + (HomeCollection::max('id') ?? 0) + 1);
        $data['status'] = !empty($data['phlebotomist_id']) ? 'Assigned' : 'Pending';
        $data['checklist'] = ['identity_verified' => false, 'fasting_confirmed' => false, 'barcode_labeled' => false, 'ice_pack_used' => false];

        $hc = HomeCollection::create($data);
        LabAudit::log('Booked Home Collection', 'Phlebotomy', $hc->booking_code, "Route: {$hc->route_zone}");

        return back()->with('success', "Home collection {$hc->booking_code} scheduled.");
    }

    public function updateHomeCollection(Request $request, HomeCollection $homeCollection)
    {
        $data = $request->validate([
            'status' => 'required|in:Pending,Assigned,En-Route,Collected,Delivered to Lab,Cancelled',
            'cash_collected' => 'nullable|numeric|min:0',
        ]);

        if ($data['status'] === 'Collected' && !$homeCollection->picked_up_at) {
            $data['picked_up_at'] = now();
            $data['checklist'] = ['identity_verified' => true, 'fasting_confirmed' => true, 'barcode_labeled' => true, 'ice_pack_used' => true];
        }

        $homeCollection->update($data);
        return back()->with('success', "Home collection {$homeCollection->booking_code} updated to {$data['status']}.");
    }
}

