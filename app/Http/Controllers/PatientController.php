<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Corporate;
use App\Models\Patient;
use App\Models\PatientDocument;
use App\Services\LabAudit;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q');
        $patients = Patient::with(['branch', 'corporate', 'familyHead'])
            ->where('is_merged', false)
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('uhid', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15);

        $branches = Branch::where('is_active', true)->get();
        $corporates = Corporate::where('status', 'Active')->get();

        // Detect potential duplicates by matching phone + name
        $duplicates = Patient::where('is_merged', false)
            ->selectRaw('phone, name, COUNT(*) as cnt')
            ->groupBy('phone', 'name')
            ->having('cnt', '>', 1)
            ->get();

        return view('patients.index', compact('patients', 'branches', 'corporates', 'duplicates', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'age' => 'required|integer|min:0|max:120',
            'dob' => 'nullable|date',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'blood_group' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'clinical_history' => 'nullable|string',
            'patient_type' => 'required|in:Walk-in,Registered,Corporate',
            'corporate_id' => 'nullable|exists:corporates,id',
            'family_head_id' => 'nullable|exists:patients,id',
            'family_relation' => 'nullable|string|max:50',
        ]);

        $nextId = (Patient::max('id') ?? 0) + 1;
        $data['uhid'] = 'UHID-' . date('Y') . '-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
        $patient = Patient::create($data);

        LabAudit::log('Registered Patient', 'Patient Management', $patient->uhid, "Registered {$patient->name} ({$patient->patient_type})");

        return redirect()->route('patients.show', $patient)->with('success', "Patient registered with UHID {$patient->uhid}");
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'branch', 'corporate', 'familyHead', 'familyMembers', 'documents',
            'appointments.doctor', 'invoices.items', 'samples', 'labOrders.test', 'labReports',
        ]);
        $allPatients = Patient::where('is_merged', false)->where('id', '!=', $patient->id)->orderBy('name')->get();
        $potentialDuplicates = Patient::where('is_merged', false)
            ->where('id', '!=', $patient->id)
            ->where(fn ($q) => $q->where('phone', $patient->phone)->orWhere('name', $patient->name))
            ->get();

        return view('patients.show', compact('patient', 'allPatients', 'potentialDuplicates'));
    }

    public function uploadDocument(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'document_type' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:5120',
        ]);

        $path = 'docs/doc-' . $patient->id . '-' . time() . '.pdf';
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('patient_docs', 'public');
        }

        PatientDocument::create([
            'patient_id' => $patient->id,
            'title' => $data['title'],
            'document_type' => $data['document_type'],
            'file_path' => $path,
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Document attached to patient profile.');
    }

    public function merge(Request $request, Patient $patient)
    {
        $request->validate(['duplicate_id' => 'required|exists:patients,id|different:patient']);
        $dup = Patient::findOrFail($request->duplicate_id);

        $dup->appointments()->update(['patient_id' => $patient->id]);
        $dup->invoices()->update(['patient_id' => $patient->id]);
        $dup->samples()->update(['patient_id' => $patient->id]);
        $dup->labOrders()->update(['patient_id' => $patient->id]);
        $dup->labReports()->update(['patient_id' => $patient->id]);

        $dup->update(['is_merged' => true, 'merged_into_id' => $patient->id]);
        LabAudit::log('Merged Duplicate Patient', 'Patient Management', $patient->uhid, "Merged {$dup->uhid} into {$patient->uhid}");

        return back()->with('success', "Merged duplicate record {$dup->uhid} into {$patient->uhid}.");
    }
}

