<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckVaiTro
{
    /**
     * Dùng: ->middleware('role:quan_ly') hoặc ->middleware('role:quan_ly,nhan_vien')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // Nếu chưa đăng nhập → chuyển về trang login
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
        }

        // Lấy tên vai trò hiện tại của người dùng
        $roleName = strtolower($user->vaiTro->ten ?? $user->loai_tai_khoan ?? '');

        // Nếu middleware không yêu cầu vai trò cụ thể → cho qua
        if (empty($roles)) {
            return $next($request);
        }

        // Chuẩn hóa mảng role được phép
        $allowed = array_map('strtolower', array_map('trim', $roles));

        // Nếu có quyền hợp lệ → cho phép
        if (in_array($roleName, $allowed)) {
            return $next($request);
        }

        // Nếu không có quyền → quay lại trang trước, hiển thị alert
        return redirect()->back()->with('error', '🚫 Bạn không có quyền thực hiện thao tác này!');
    }
}
