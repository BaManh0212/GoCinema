<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Check by explicit account type first
        if (!empty($user->loai_tai_khoan) && $user->loai_tai_khoan === 'quan_ly') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        // Fallback: check related vaiTro name (if relation exists)
        try {
            if ($user->relationLoaded('vaiTro') ? ($user->vaiTro?->ten === 'quan_ly') : ($user->vaiTro()->exists() && $user->vaiTro->ten === 'quan_ly')) {
                return redirect()->intended(route('admin.dashboard', absolute: false));
            }
        } catch (\Throwable $e) {
            // ignore and continue to default
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
