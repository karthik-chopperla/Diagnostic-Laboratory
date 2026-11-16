<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Test;
use App\Services\LabAudit;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $type = $request->get('type');
        $appointments = Appointment::with(['branch', 'patient', 'doctor', 'department', 'test'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($type, fn ($q) => $q->where('appointment_type', $type))
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        $branches = Branch::where('is_active', true)->get();
        $patients = Patient::where('is_merged', false)->orderBy('name')->get();
        $doctors = Doctor::where('status', 'Active')->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $tests = Test::where('is_active', true)->orderBy('name')->get();

        return view('appointments.index', compact('appointments', 'branches', 'patients', 'doctors', 'departments', 'tests', 'status', 'type'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'department_id' => 'nullable|exists:departments,id',
            'test_id' => 'nullable|exists:tests,id',
            'appointment_type' => 'required|in:Walk-in,Online,Home Collection',
            'scheduled_at' => 'required|date',
            'time_slot' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $nextId = (Appointment::max('id') ?? 0) + 101;
        $data['appointment_no'] = 'APT-' . date('Y') . '-' . $nextId;
        $data['status'] = 'Confirmed';
        $data['sms_sent'] = $request->boolean('send_sms', true);
        $data['whatsapp_sent'] = $request->boolean('send_whatsapp', true);

        $apt = Appointment::create($data);
        LabAudit::log('Booked Appointment', 'Appointment Management', $apt->appointment_no, "Type: {$apt->appointment_type}");

        return back()->with('success', "Appointment {$apt->appointment_no} confirmed (SMS & WhatsApp notification queued).");
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => 'required|in:Scheduled,Confirmed,Checked-in,Completed,Cancelled,Rescheduled',
            'scheduled_at' => 'nullable|date',
        ]);
        $appointment->update($data);
        LabAudit::log('Updated Appointment', 'Appointment Management', $appointment->appointment_no, "Status -> {$data['status']}");

        return back()->with('success', "Appointment {$appointment->appointment_no} updated to {$data['status']}.");
    }
}

