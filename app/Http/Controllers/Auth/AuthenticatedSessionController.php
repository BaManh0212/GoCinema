<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cookie;

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

        // If explicit account type says admin, go to admin dashboard
        if (!empty($user->loai_tai_khoan) && $user->loai_tai_khoan === 'quan_ly') {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        // Robust fallback: check the id of the role named 'quan_ly'
        try {
            $managerRoleId = \App\Models\VaiTro::where('ten', 'quan_ly')->value('id');
            if ($managerRoleId && $user->vai_tro_id == $managerRoleId) {
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

        // Remove the remember-me cookie so the user isn't automatically
        // re-authenticated when they visit the login page.
        try {
            // forget the auth recaller cookie
            $recaller = Auth::getRecallerName();
            if ($recaller) {
                Cookie::queue(Cookie::forget($recaller));
            }

            // also forget the session cookie (config name)
            $sessionCookie = config('session.cookie');
            if ($sessionCookie) {
                Cookie::queue(Cookie::forget($sessionCookie));
            }

            // common fallback names
            Cookie::queue(Cookie::forget('remember_web_'.md5(config('app.key'))));
            Cookie::queue(Cookie::forget('laravel_session'));
        } catch (\Throwable $e) {
            // ignore if cookie facade not available for any reason
        }

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
