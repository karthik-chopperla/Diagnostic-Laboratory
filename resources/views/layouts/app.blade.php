<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PathoLab LIS') — Complete Diagnostic Lab Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-teal-100 selection:text-teal-900">
@php
    $openAlerts = \App\Models\CriticalAlert::where('status', 'Open')->count();
    $currentUser = auth()->user();

    $navGroups = [
        'Overview' => [
            ['route' => 'dashboard', 'label' => 'Dashboard & Analytics', 'match' => 'dashboard'],
        ],
        'Front Desk & Billing' => [
            ['route' => 'patients.index', 'label' => 'Patients (UHID)', 'match' => 'patients.*'],
            ['route' => 'appointments.index', 'label' => 'Appointments', 'match' => 'appointments.*'],
            ['route' => 'billing.index', 'label' => 'Billing & Invoicing', 'match' => 'billing.*'],
        ],
        'Laboratory Workflow' => [
            ['route' => 'samples.index', 'label' => 'Samples & Barcodes', 'match' => 'samples.*'],
            ['route' => 'phlebotomy.index', 'label' => 'Home Phlebotomy', 'match' => 'phlebotomy.*'],
            ['route' => 'lab.departments', 'label' => '11 Lab Departments', 'match' => 'lab.departments'],
            ['route' => 'lab.worklist', 'label' => 'Worklist & Processing', 'match' => 'lab.worklist'],
            ['route' => 'lab.results', 'label' => 'Result Verification', 'match' => 'lab.results*'],
            ['route' => 'lab.reports', 'label' => 'Reports & QR Delivery', 'match' => 'lab.reports*'],
            ['route' => 'lab.critical', 'label' => 'Critical Alerts', 'match' => 'lab.critical', 'badge' => $openAlerts],
        ],
        'Catalog & Quality' => [
            ['route' => 'catalog.tests', 'label' => 'Test Master (16 Cats)', 'match' => 'catalog.tests*'],
            ['route' => 'catalog.packages', 'label' => 'Health Packages', 'match' => 'catalog.packages*'],
            ['route' => 'ops.equipment', 'label' => 'Analyzers & Equipment', 'match' => 'ops.equipment*'],
            ['route' => 'ops.inventory', 'label' => 'Reagents & Inventory', 'match' => 'ops.inventory*'],
            ['route' => 'ops.qc', 'label' => 'QC (Levey-Jennings)', 'match' => 'ops.qc*'],
        ],
        'Network & Portals' => [
            ['route' => 'network.doctors', 'label' => 'Doctors & Referrers', 'match' => 'network.doctors*'],
            ['route' => 'network.corporates', 'label' => 'Corporates & Hospitals', 'match' => 'network.corporates*'],
            ['route' => 'network.branches', 'label' => 'Multi-Branch Network', 'match' => 'network.branches*'],
            ['route' => 'portal.doctor', 'label' => 'Doctor Portal', 'match' => 'portal.doctor'],
            ['route' => 'portal.patient', 'label' => 'Patient Portal', 'match' => 'portal.patient'],
        ],
        'Admin & Integrations' => [
            ['route' => 'admin.hr', 'label' => 'HR & Staff', 'match' => 'admin.hr*'],
            ['route' => 'admin.finance', 'label' => 'Finance & Accounting', 'match' => 'admin.finance*'],
            ['route' => 'admin.crm', 'label' => 'Marketing & CRM', 'match' => 'admin.crm*'],
            ['route' => 'admin.compliance', 'label' => 'Compliance & NABL', 'match' => 'admin.compliance*'],
            ['route' => 'admin.integrations', 'label' => 'HL7 / DICOM / APIs', 'match' => 'admin.integrations*'],
        ],
    ];
@endphp

<div class="min-h-screen flex">
    <!-- Sidebar -->
    <aside id="sidebar" class="no-print fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 flex flex-col transition-transform duration-200 -translate-x-full lg:translate-x-0">
        <div class="h-16 px-5 flex items-center gap-3 border-b border-slate-200 shrink-0">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-600 to-cyan-600 flex items-center justify-center text-white shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 5h6l1 7.5a5 5 0 01-4 4.9v0a5 5 0 01-4-4.9L9 5zm-2 15h10" />
                </svg>
            </div>
            <div>
                <div class="font-bold text-slate-900 tracking-tight leading-none">PathoLab <span class="text-teal-600">LIS</span></div>
                <div class="text-[11px] text-slate-500 mt-1 font-medium">Diagnostic Suite · NABL</div>
            </div>
        </div>

        <!-- Role Banner inside Sidebar -->
        <div class="px-4 py-2 border-b border-slate-100 bg-slate-50/50">
            <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Active Role Scope</div>
            <div class="flex items-center gap-2 mt-0.5">
                <span class="w-2 h-2 rounded-full {{ $currentUser?->isChiefPathologist() ? 'bg-purple-600' : ($currentUser?->isTechnician() ? 'bg-sky-600' : 'bg-emerald-600') }}"></span>
                <span class="text-xs font-bold text-slate-800 truncate">{{ $currentUser?->role ?? 'Authorized Staff' }}</span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5">
            @foreach($navGroups as $groupName => $items)
                @php
                    $visibleItems = collect($items)->filter(function($item) use ($currentUser) {
                        return $currentUser?->canAccessRoute($item['route']);
                    });
                @endphp
                @if($visibleItems->isNotEmpty())
                    <div>
                        <div class="px-3 mb-1.5 text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ $groupName }}</div>
                        <div class="space-y-0.5">
                            @foreach($visibleItems as $item)
                                @php $active = request()->routeIs($item['match']); @endphp
                                <a href="{{ route($item['route']) }}"
                                   class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $active ? 'bg-teal-50 text-teal-700 border border-teal-200/70' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <span class="truncate">{{ $item['label'] }}</span>
                                    @if(!empty($item['badge']))
                                        <span class="ml-2 px-1.5 py-0.5 text-[11px] font-bold rounded-full bg-rose-100 text-rose-700">{{ $item['badge'] }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>

        <div class="p-3 border-t border-slate-200 bg-slate-50/70">
            @php
                $userName = $currentUser?->name ?? 'Staff User';
                $userRole = $currentUser?->role ?? 'Authorized Staff';
                $initials = collect(explode(' ', $userName))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');
                $badgeClasses = $currentUser?->isChiefPathologist() 
                    ? 'bg-purple-100 text-purple-800 border-purple-200' 
                    : ($currentUser?->isTechnician() ? 'bg-sky-100 text-sky-800 border-sky-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200');
            @endphp
            <div class="flex items-center justify-between gap-2 px-2 py-1.5">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs font-bold shrink-0">{{ $initials }}</div>
                    <div class="min-w-0">
                        <div class="text-xs font-semibold text-slate-900 truncate">{{ $userName }}</div>
                        <span class="inline-block px-1.5 py-0.5 text-[10px] font-semibold rounded border {{ $badgeClasses }} truncate max-w-full leading-tight">
                            {{ $userRole }}
                        </span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Sign Out" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
        <!-- Top Header -->
        <header class="no-print sticky top-0 z-30 h-16 bg-white/95 backdrop-blur border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-1 max-w-xl">
                <button type="button" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')"
                        class="lg:hidden p-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <form action="{{ route('search') }}" method="GET" class="flex-1">
                    <div class="relative">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/></svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search UHID, Patient, Sample Barcode, Invoice..."
                               class="w-full pl-9 pr-4 py-1.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500">
                    </div>
                </form>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <div class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-xs font-medium text-slate-700">
                    <span class="w-2 h-2 rounded-full {{ $currentUser?->isChiefPathologist() ? 'bg-purple-600' : ($currentUser?->isTechnician() ? 'bg-sky-600' : 'bg-emerald-600') }}"></span>
                    <span class="hidden sm:inline text-slate-500">Role:</span>
                    <span class="font-bold text-slate-800 max-w-[120px] sm:max-w-[160px] truncate">{{ $currentUser?->role }}</span>
                </div>

                @if($currentUser?->canAccessRoute('lab.critical') && $openAlerts > 0)
                    <a href="{{ route('lab.critical') }}" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold hover:bg-rose-100">
                        <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                        {{ $openAlerts }} Critical Alert{{ $openAlerts > 1 ? 's' : '' }}
                    </a>
                @endif

                @if($currentUser?->canAccessRoute('billing.index'))
                    <a href="{{ route('billing.index') }}" class="px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-xs transition">
                        + New Bill / Order
                    </a>
                @elseif($currentUser?->canAccessRoute('lab.worklist'))
                    <a href="{{ route('lab.worklist') }}" class="px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-xs transition">
                        ⚡ Clinical Worklist
                    </a>
                @endif
            </div>
        </header>

        <!-- Page Body -->
        <main class="flex-1 p-4 sm:p-6 max-w-[1600px] w-full mx-auto">
            @if(session('success'))
                <div class="no-print mb-5 px-4 py-3 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-sm font-medium flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-teal-600 hover:text-teal-900 text-xs font-bold">✕</button>
                </div>
            @endif
            @if(session('error'))
                <div class="no-print mb-5 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 text-xs font-bold">✕</button>
                </div>
            @endif
            @if($errors->any())
                <div class="no-print mb-5 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
// Click outside to close role menu
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('roleSwitcherWrap');
    const menu = document.getElementById('roleMenu');
    if (wrap && menu && !wrap.contains(e.target)) {
        menu.classList.add('hidden');
    }
});
</script>
</body>
</html>
