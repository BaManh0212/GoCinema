<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VaiTro;

class CheckVaiTro
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:quan_ly') or ->middleware('role:nhan_vien,quan_ly')
     */
    public function handle(Request $request, Closure $next, $roles = null)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$roles) {
            return $next($request);
        }

        $allowed = array_map('trim', explode(',', $roles));

        // load role name from DB
        $role = VaiTro::find($user->vai_tro_id);
        $roleName = $role? $role->ten : null;

        if ($roleName && in_array($roleName, $allowed)) {
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập');
    }
}
