@extends('layouts.app')
@section('title', $patient->name . ' (' . $patient->uhid . ')')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-slate-900">{{ $patient->name }}</h1>
            <span class="px-2.5 py-1 text-xs font-mono font-bold rounded-full bg-teal-50 text-teal-700 border border-teal-200">{{ $patient->uhid }}</span>
        </div>
        <p class="text-sm text-slate-500 mt-1">{{ $patient->age }} yrs · {{ $patient->gender }} · Blood Group: {{ $patient->blood_group ?? 'N/A' }} · Phone: {{ $patient->phone }}</p>
    </div>
    <a href="{{ route('patients.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">← Back to Patients</a>
</div>

<div class="grid lg:grid-cols-3 gap-6 mb-6">
    <!-- Clinical Profile -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-3">
        <h2 class="font-bold text-slate-900">Demographics & Clinical History</h2>
        <div class="text-xs space-y-2 text-slate-600">
            <div><span class="font-semibold text-slate-800">Address:</span> {{ $patient->address }}, {{ $patient->city }}</div>
            <div><span class="font-semibold text-slate-800">Emergency Contact:</span> {{ $patient->emergency_contact_name ?? 'N/A' }} ({{ $patient->emergency_contact_phone ?? '-' }})</div>
            <div><span class="font-semibold text-slate-800">Allergies:</span> <span class="text-rose-600 font-semibold">{{ $patient->allergies ?? 'None reported' }}</span></div>
            <div><span class="font-semibold text-slate-800">Medical History:</span> {{ $patient->medical_history ?? 'None' }}</div>
            <div><span class="font-semibold text-slate-800">Clinical Notes:</span> {{ $patient->clinical_history ?? 'None' }}</div>
            @if($patient->familyHead)
                <div><span class="font-semibold text-slate-800">Family Head:</span> <a href="{{ route('patients.show', $patient->familyHead) }}" class="text-teal-600 hover:underline">{{ $patient->familyHead->name }}</a> ({{ $patient->family_relation }})</div>
            @endif
        </div>

        @if($potentialDuplicates->isNotEmpty())
        <div class="pt-3 border-t border-slate-200">
            <h3 class="text-xs font-bold text-amber-800 mb-2">Potential Duplicate Records Detected</h3>
            @foreach($potentialDuplicates as $dup)
                <form method="POST" action="{{ route('patients.merge', $patient) }}" class="flex items-center justify-between gap-2 p-2 rounded-lg bg-amber-50 border border-amber-200 mb-1.5">
                    @csrf
                    <input type="hidden" name="duplicate_id" value="{{ $dup->id }}">
                    <div class="text-xs">
                        <div class="font-semibold text-slate-900">{{ $dup->name }}</div>
                        <div class="text-slate-500">{{ $dup->uhid }} · {{ $dup->phone }}</div>
                    </div>
                    <button class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded">Merge Into This</button>
                </form>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Document Uploads -->
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h2 class="font-bold text-slate-900 mb-3">Patient Documents & Prescriptions</h2>
        <div class="space-y-2 mb-4">
            @forelse($patient->documents as $doc)
                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-slate-800">{{ $doc->title }}</div>
                        <div class="text-[11px] text-slate-500">{{ $doc->document_type }} · {{ $doc->created_at->format('d M Y') }}</div>
                    </div>
                    <span class="text-[11px] font-mono text-teal-700">{{ basename($doc->file_path) }}</span>
                </div>
            @empty
                <p class="text-xs text-slate-400">No documents uploaded yet.</p>
            @endforelse
        </div>
        <form method="POST" action="{{ route('patients.documents', $patient) }}" enctype="multipart/form-data" class="space-y-2 border-t border-slate-100 pt-3">
            @csrf
            <input type="text" name="title" placeholder="Document Title (e.g. Referral Rx)" required class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg">
            <select name="document_type" class="w-full px-3 py-1.5 text-xs border border-slate-200 rounded-lg">
                <option>Prescription</option>
                <option>Previous Report</option>
                <option>ID / Insurance</option>
                <option>Consent Form</option>
            </select>
            <input type="file" name="file" class="w-full text-xs text-slate-500">
            <button class="w-full py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-lg">Upload Document</button>
        </form>
    </div>

    <!-- Investigation & Visit Summary -->
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h2 class="font-bold text-slate-900 mb-3">Investigation & Report History</h2>
        <div class="space-y-2">
            @forelse($patient->labOrders as $ord)
                <div class="p-2.5 rounded-lg border border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-slate-900">{{ $ord->test->name }}</div>
                        <div class="text-[11px] text-slate-500">{{ $ord->created_at->format('d M Y') }} · {{ $ord->status }}</div>
                    </div>
                    @if($ord->report)
                        <a href="{{ route('lab.reports.show', $ord->report) }}" class="px-2.5 py-1 bg-teal-50 text-teal-700 rounded text-xs font-semibold">View Report</a>
                    @endif
                </div>
            @empty
                <p class="text-xs text-slate-400">No investigations recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

