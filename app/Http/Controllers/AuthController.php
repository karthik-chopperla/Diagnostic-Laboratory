<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LabAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function showRegistration()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $email = $request->input('email');
        if (is_string($email)) {
            $request->merge(['email' => strtolower(trim($email))]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(16)],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'Pending Approval',
            'is_active' => false,
        ]);

        return redirect()->route('login')->with(
            'success',
            'Your access request was submitted. An administrator must approve your account before you can sign in.'
        );
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt(array_merge($credentials, ['is_active' => true]), $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            LabAudit::log('User Login', 'Authentication', $user->email, "Signed in as {$user->role}", $user->name);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Invalid email, password, or inactive account.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            LabAudit::log('User Logout', 'Authentication', $user->email, 'Signed out of PathoLab LIS', $user->name);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been signed out.');
    }
}
