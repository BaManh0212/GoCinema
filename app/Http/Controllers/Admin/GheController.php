<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ghe;
use App\Models\PhongChieu;
use Illuminate\Http\Request;

class GheController extends Controller
{
    public function index($id)
    {
        $phong = PhongChieu::findOrFail($id);

        // Lấy tất cả ghế, nhóm theo hàng
        $ghes = Ghe::where('phong_id', $id)
            ->orderBy('hang')
            ->orderBy('cot')
            ->get()
            ->groupBy('hang');

        return view('admin.ghe.index', compact('phong', 'ghes'));
    }

    public function store(Request $request, $phong_id)
    {
        $request->validate([
            'hang' => 'required|string|max:5',
            'cot' => 'required|integer|min:1',
            'loai' => 'required|in:thuong,vip,doi',
        ]);

        Ghe::create([
            'phong_id' => $phong_id,
            'hang' => strtoupper($request->hang),
            'cot' => $request->cot,
            'loai' => $request->loai,
            'trang_thai' => 'hoat_dong',
            'ngay_tao' => now(),
        ]);

        return back()->with('success', 'Đã thêm ghế mới thành công!');
    }

    public function destroy($id)
    {
        $ghe = Ghe::findOrFail($id);
        $ghe->delete();

        return back()->with('success', 'Đã xóa ghế thành công!');
    }
}
