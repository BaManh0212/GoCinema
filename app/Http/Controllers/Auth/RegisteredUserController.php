<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Hiển thị form đăng ký
     */
    public function create(): View
    {
        return view('client.auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản mới
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'ho_ten' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:nguoi_dung,email'],
            'so_dien_thoai' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = NguoiDung::create([
            'ho_ten' => $request->ho_ten,
            'email' => $request->email,
            'so_dien_thoai' => $request->so_dien_thoai,
            'password' => $request->password, // sẽ tự động hash nhờ mutator
            'vai_tro_id' => 1, // khách hàng mặc định
            'kich_hoat' => true,
            'loai_tai_khoan' => 'khach_hang',
            'diem_tich_luy' => 0,
        ]);

        event(new Registered($user));

        // Đăng nhập ngay sau khi đăng ký
        Auth::login($user);

        // ✅ Thông báo đăng ký thành công
        return redirect()
            ->route('home')
            ->with('success', 'Đăng ký tài khoản thành công! Chào mừng ' . $user->ho_ten . ' đến với GoCinema 🎉');
    }
}
