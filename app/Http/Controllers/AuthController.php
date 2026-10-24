<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle user login.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->with('error', 'Your account has been suspended by an administrator. Please contact support.');
        }

        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user)
            ->with('success', 'Welcome back, '.$user->name.'!');
    }

    /**
     * Show registration form.
     */
    public function showRegister(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $selectedRole = $request->query('role', 'applicant');
        if (! in_array($selectedRole, ['applicant', 'employer'], true)) {
            $selectedRole = 'applicant';
        }

        return view('auth.register', compact('selectedRole'));
    }

    /**
     * Handle registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:applicant,employer'],
            'company_name' => ['nullable', 'required_if:role,employer', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'company_name' => $validated['role'] === 'employer' ? ($validated['company_name'] ?? null) : null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user)
            ->with('success', 'Account created successfully! Welcome to FindJobPh.');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('jobs.feed')
            ->with('info', 'You have been logged out.');
    }

    /**
     * Redirect user to their respective dashboard based on role.
     */
    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isEmployer()) {
            return redirect()->route('employer.dashboard');
        }

        return redirect()->route('jobs.feed');
    }
}
