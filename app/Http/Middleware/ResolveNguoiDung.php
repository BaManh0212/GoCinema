<?php

namespace App\Http\Middleware;

use App\Models\NguoiDung;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveNguoiDung
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->query('nguoi_dung_id') ?: $request->input('nguoi_dung_id');
        if ($userId) {
            $user = NguoiDung::find($userId);
            if ($user) {
                // Bind user instance to request via a macro using setUserResolver
                $request->setUserResolver(function () use ($user) {
                    return $user;
                });
            }
        }

        return $next($request);
    }
}
