@extends('layouts.app')
@section('title', 'Sample Collection & Accession')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Sample Collection, Barcoding & Accession</h1>
        <p class="text-sm text-slate-500">Unique Sample ID, barcode labels, container tracking, accept/reject & inter-branch transfer.</p>
    </div>
    <div class="flex gap-1.5 flex-wrap">
        @foreach(['', 'Collected', 'Received', 'Processing', 'Completed', 'Rejected'] as $st)
            <a href="{{ route('samples.index', ['status' => $st]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold border {{ $status === $st ? 'bg-teal-600 text-white border-teal-600' : 'bg-white text-slate-600 border-slate-200' }}">
                {{ $st ?: 'All Samples' }}
            </a>
        @endforeach
    </div>
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Register & Barcode Sample</h2>
        <form method="POST" action="{{ route('samples.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Patient *</label>
                <select name="patient_id" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->uhid }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Branch</label>
                    <select name="branch_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Collector</label>
                    <select name="collector_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        @foreach($collectors as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Sample Type</label>
                    <select name="sample_type" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option>Whole Blood</option>
                        <option>Serum</option>
                        <option>Plasma</option>
                        <option>Urine</option>
                        <option>Nasopharyngeal Swab</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Container</label>
                    <select name="container_type" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                        <option>EDTA (Lavender)</option>
                        <option>SST (Yellow)</option>
                        <option>Fluoride (Grey)</option>
                        <option>Sterile Container</option>
                        <option>VTM Tube</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Quantity (mL)</label>
                    <input type="number" name="quantity_ml" value="3.0" step="0.5" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Storage Rack</label>
                    <input type="text" name="storage_location" value="Rack A-05" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Collection Location</label>
                <input type="text" name="collection_location" value="Main Lab Phlebotomy Bay" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Create Lab Order (Optional)</label>
                <select name="test_id" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    <option value="">Sample Only</option>
                    @foreach($tests as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <label class="flex items-center gap-2 text-xs font-semibold text-rose-700">
                <input type="checkbox" name="is_stat" value="1" class="rounded text-rose-600"> Mark as STAT / Emergency Priority
            </label>
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Generate Barcode & Accession</button>
        </form>
    </div>

    <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-900">Accessioned Samples & Barcodes</h2>
            <span class="text-xs text-slate-500">{{ $samples->total() }} samples</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">Sample & Barcode</th>
                        <th class="px-4 py-3">Patient</th>
                        <th class="px-4 py-3">Specimen</th>
                        <th class="px-4 py-3">Status & Traceability</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($samples as $s)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <div class="font-mono text-xs font-bold text-teal-700">{{ $s->sample_id_code }}</div>
                                <div class="text-[11px] text-slate-500">{{ $s->accession_no }}</div>
                                <!-- Visual Barcode Bars -->
                                <div class="mt-1 inline-flex items-center gap-[1.5px] px-2 py-1 bg-white border border-slate-200 rounded">
                                    @foreach(str_split($s->barcode_value) as $ch)
                                        <span class="inline-block bg-slate-900 h-4" style="width: {{ (ord($ch) % 2) + 1 }}px"></span>
                                    @endforeach
                                    <span class="ml-1.5 font-mono text-[10px] text-slate-700">{{ $s->barcode_value }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $s->patient->name }}</div>
                                <div class="text-xs text-slate-500">{{ $s->patient->uhid }}</div>
                                @if($s->is_stat)<span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-rose-100 text-rose-700">STAT</span>@endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                <div class="font-semibold text-slate-800">{{ $s->sample_type }}</div>
                                <div>{{ $s->container_type }} · {{ $s->quantity_ml }} mL</div>
                                <div class="text-slate-400">Loc: {{ $s->storage_location }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('samples.status', $s) }}" class="space-y-1.5">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="px-2 py-1 text-xs border border-slate-200 rounded-lg bg-white font-semibold">
                                        @foreach(['Registered','Collected','In-Transit','Received','Processing','Completed','Rejected','Disposed'] as $st)
                                            <option @selected($s->status === $st)>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                    @if($s->rejection_reason)
                                        <div class="text-[11px] text-rose-600 font-medium">Reason: {{ $s->rejection_reason }}</div>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $samples->links() }}</div>
    </div>
</div>
@endsection

