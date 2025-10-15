<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckVaiTro
{
    /**
     * Sử dụng: ->middleware('role:quan_ly') hoặc ->middleware('role:quan_ly,nhan_vien')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Lấy tên vai trò của người dùng từ quan hệ
        $roleName = strtolower($user->vaiTro->ten ?? '');

        // Nếu middleware không yêu cầu vai trò cụ thể → cho qua
        if (empty($roles)) {
            return $next($request);
        }

        // Chuẩn hóa mảng role yêu cầu
        $allowed = array_map('strtolower', array_map('trim', $roles));

        if (in_array($roleName, $allowed)) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập vào trang này.');
    }
}
