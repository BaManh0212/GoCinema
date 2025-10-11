<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use Illuminate\Http\Request;

class SanPhamController extends Controller
{
    public function index()
    {
        $sanPhams = SanPham::all();
        return view('admin.san_pham.index', compact('sanPhams'));
    }

    public function create()
    {
        return view('admin.san_pham.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|string|max:255',
            'gia' => 'required|numeric|min:0',
            'so_luong' => 'required|integer|min:0',
        ]);

        SanPham::create([
            'ten' => $request->ten,
            'gia' => $request->gia,
            'so_luong' => $request->so_luong,
            'ngay_tao' => now(),
            'ngay_cap_nhat' => now(),
        ]);

        return redirect()->route('admin.san_pham.index')->with('success', 'Sản phẩm đã được thêm thành công.');
    }

    public function edit(SanPham $sanPham)
    {
        return view('admin.san_pham.edit', compact('sanPham'));
    }

    public function update(Request $request, SanPham $sanPham)
    {
        $request->validate([
            'ten' => 'required|string|max:255',
            'gia' => 'required|numeric|min:0',
            'so_luong' => 'required|integer|min:0',
        ]);

        $sanPham->update([
            'ten' => $request->ten,
            'gia' => $request->gia,
            'so_luong' => $request->so_luong,
            'ngay_cap_nhat' => now(),
        ]);

        return redirect()->route('admin.san_pham.index')->with('success', 'Sản phẩm đã được cập nhật thành công.');
    }

    public function destroy(SanPham $sanPham)
    {
        $sanPham->delete();

        return redirect()->route('admin.san_pham.index')->with('success', 'Sản phẩm đã được xóa thành công.');
    }
}