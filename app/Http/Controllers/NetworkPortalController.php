<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Corporate;
use App\Models\Doctor;
use App\Models\LabReport;
use App\Models\Patient;
use App\Models\Sample;
use App\Services\LabAudit;
use Illuminate\Http\Request;

class NetworkPortalController extends Controller
{
    public function doctors()
    {
        $doctors = Doctor::withCount('samples')->orderBy('name')->get();
        return view('network.doctors', compact('doctors'));
    }

    public function storeDoctor(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string',
            'qualification' => 'nullable|string',
            'hospital_clinic' => 'nullable|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'referral_source' => 'required|string',
            'commission_percent' => 'nullable|numeric|min:0|max:30',
        ]);
        $data['code'] = 'DOC-' . (100 + (Doctor::max('id') ?? 0) + 1);
        Doctor::create($data);
        LabAudit::log('Registered Doctor', 'Doctor Management', $data['code'], $data['name']);

        return back()->with('success', "Doctor {$data['name']} ({$data['code']}) registered.");
    }

    public function corporates()
    {
        $corporates = Corporate::withCount('patients')->orderBy('name')->get();
        return view('network.corporates', compact('corporates'));
    }

    public function storeCorporate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Corporate,Hospital,Nursing Home',
            'contact_person' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'credit_limit' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0|max:50',
        ]);
        $data['code'] = 'CORP-' . str_pad((string) ((Corporate::max('id') ?? 0) + 1), 3, '0', STR_PAD_LEFT);
        $data['contract_start'] = today();
        $data['contract_end'] = today()->addYear();
        Corporate::create($data);

        return back()->with('success', "Corporate client {$data['name']} registered.");
    }

    public function branches()
    {
        $branches = Branch::withCount(['patients', 'samples', 'employees'])
            ->withSum('payments', 'amount')
            ->get();
        return view('network.branches', compact('branches'));
    }

    public function storeBranch(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'phone' => 'required|string',
            'license_number' => 'nullable|string',
            'accreditation_status' => 'required|string',
        ]);
        $data['code'] = 'BR-' . str_pad((string) ((Branch::max('id') ?? 0) + 1), 3, '0', STR_PAD_LEFT);
        Branch::create($data);

        return back()->with('success', "Branch {$data['name']} added.");
    }

    public function doctorPortal(Request $request)
    {
        $doctors = Doctor::where('portal_enabled', true)->get();
        $selectedDoctor = $request->get('doctor_id') ? Doctor::find($request->get('doctor_id')) : $doctors->first();
        $reports = LabReport::with(['patient', 'labOrder.test', 'labOrder.sample'])
            ->when($selectedDoctor, fn ($q) => $q->whereHas('labOrder.sample', fn ($sq) => $sq->where('doctor_id', $selectedDoctor->id)))
            ->latest()
            ->get();

        return view('portals.doctor', compact('doctors', 'selectedDoctor', 'reports'));
    }

    public function patientPortal(Request $request)
    {
        $patients = Patient::where('is_merged', false)->get();
        $selectedPatient = $request->get('patient_id') ? Patient::find($request->get('patient_id')) : $patients->first();
        if ($selectedPatient) {
            $selectedPatient->load(['appointments.doctor', 'invoices', 'samples', 'labReports.labOrder.test', 'familyMembers']);
        }

        return view('portals.patient', compact('patients', 'selectedPatient'));
    }
}

