<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Hiển thị trang đăng nhập
     */
    public function create(): View
    {
        return view('client.auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // --- ✅ Xác định tên vai trò (từ quan hệ vaiTro hoặc cột loai_tai_khoan)
        $roleName = strtolower($user->vaiTro->ten ?? $user->loai_tai_khoan ?? '');

        // --- ✅ Tạo thông báo đăng nhập thành công
        $successMessage = 'Đăng nhập thành công! Chào mừng ' . $user->ho_ten ?? $user->name ?? 'bạn';

        // --- ✅ Chuyển hướng theo vai trò
        switch ($roleName) {
            case 'quan_ly':
                return redirect()
                    ->intended(route('admin.dashboard', absolute: false))
                    ->with('success', $successMessage);

            case 'nhan_vien':
                return redirect()
                    ->intended(route('staff.dashboard', absolute: false))
                    ->with('success', $successMessage);

            default:
                return redirect()
                    ->intended(route('home', absolute: false))
                    ->with('success', $successMessage);
        }
    }

    /**
     * Đăng xuất
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        try {
            // Xóa cookie ghi nhớ đăng nhập
            $recaller = Auth::getRecallerName();
            if ($recaller) {
                Cookie::queue(Cookie::forget($recaller));
            }

            // Xóa session cookie
            $sessionCookie = config('session.cookie');
            if ($sessionCookie) {
                Cookie::queue(Cookie::forget($sessionCookie));
            }

            Cookie::queue(Cookie::forget('remember_web_' . md5(config('app.key'))));
            Cookie::queue(Cookie::forget('laravel_session'));
        } catch (\Throwable $e) {
            // Bỏ qua nếu Cookie facade gặp lỗi
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Bạn đã đăng xuất thành công!');
    }
}
