@extends('layouts.app')
@section('title', 'Doctors & Referrers')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-900">Doctor & Referrer Management</h1>
    <p class="text-sm text-slate-500">Doctor registration, specialization, hospital tie-ups, referral volume & digital report sharing.</p>
</div>

<div class="grid xl:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="text-base font-bold text-slate-900 mb-4">Register Referring Doctor</h2>
        <form method="POST" action="{{ route('network.doctors.store') }}" class="space-y-3">
            @csrf
            <input type="text" name="name" placeholder="Dr. Full Name *" required class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="specialization" placeholder="Specialization *" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                <input type="text" name="qualification" placeholder="MD / DM / MS" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <input type="text" name="hospital_clinic" placeholder="Hospital / Clinic Name" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg">
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="phone" placeholder="Phone *" required class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                <input type="email" name="email" placeholder="Email" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <select name="referral_source" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
                    <option>Direct</option><option>Clinic Tie-up</option><option>Hospital Network</option>
                </select>
                <input type="number" step="0.5" name="commission_percent" value="0" placeholder="Commission %" class="px-3 py-2 text-sm border border-slate-200 rounded-lg">
            </div>
            <button class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg">Register Doctor</button>
        </form>
    </div>

    <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase">
                <tr>
                    <th class="px-4 py-3">Code / Doctor</th>
                    <th class="px-4 py-3">Hospital / Clinic</th>
                    <th class="px-4 py-3">Referrals</th>
                    <th class="px-4 py-3">Portal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($doctors as $d)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900">{{ $d->name }}</div>
                            <div class="text-xs text-slate-500">{{ $d->code }} · {{ $d->specialization }} ({{ $d->qualification }})</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600">{{ $d->hospital_clinic }}<br>{{ $d->phone }}</td>
                        <td class="px-4 py-3 font-bold text-teal-700 tabular-nums">{{ $d->samples_count }} orders</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('portal.doctor', ['doctor_id' => $d->id]) }}" class="px-3 py-1 rounded-lg bg-teal-50 text-teal-700 text-xs font-semibold">Open Portal</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

