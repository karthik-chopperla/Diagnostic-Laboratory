<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — PathoLab LIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="text-center mb-6">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-600 to-cyan-600 flex items-center justify-center text-white shadow-sm mx-auto mb-3">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 5h6l1 7.5a5 5 0 01-4 4.9v0a5 5 0 01-4-4.9L9 5zm-2 15h10" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">PathoLab <span class="text-teal-600">LIS</span></h1>
        <p class="text-sm text-slate-500 mt-1">NABL Diagnostic Laboratory Information System</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-1">Authorized Staff Sign In</h2>
        <p class="text-xs text-slate-500 mb-5">Authentication is required to access patient records, lab results, and billing.</p>

        @if(session('success'))
            <div class="mb-4 px-3.5 py-2.5 rounded-lg bg-teal-50 border border-teal-200 text-teal-800 text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 px-3.5 py-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Staff Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="name@example.com"
                       class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password</label>
                <input id="password" type="password" name="password" required
                       placeholder="••••••••"
                       class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-teal-600 border-slate-300">
                    Keep me signed in
                </label>
                <span class="text-slate-400">Protected Session</span>
            </div>

            <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                Sign In to Laboratory
            </button>
        </form>

    </div>
</div>
</body>
</html>

