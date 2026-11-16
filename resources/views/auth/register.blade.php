<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Staff Access — PathoLab LIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 flex items-center justify-center p-4">
<div class="w-full max-w-md">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">PathoLab <span class="text-teal-600">LIS</span></h1>
        <p class="text-sm text-slate-500 mt-1">Request authorized staff access</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-1">Create Staff Account</h2>
        <p class="text-xs text-slate-500 mb-5">Your account will remain inactive until an administrator reviews and approves your request.</p>

        @if($errors->any())
            <div class="mb-4 px-3.5 py-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name"
                       class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Staff Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email"
                       class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Password (16 characters minimum)</label>
                <input id="password" type="password" name="password" required minlength="16" autocomplete="new-password"
                       class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required minlength="16" autocomplete="new-password"
                       class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500">
            </div>

            <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl shadow-xs transition cursor-pointer">
                Submit Access Request
            </button>
        </form>

        <p class="mt-5 text-center text-xs text-slate-500">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-teal-700 hover:text-teal-800">Sign in</a>
        </p>
    </div>
</div>
</body>
</html>
