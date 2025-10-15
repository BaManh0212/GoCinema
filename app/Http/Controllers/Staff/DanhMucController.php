<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\Phim;
use Illuminate\Http\Request;

class DanhMucController extends Controller
{
    public function index()
    {
        // Lấy danh sách danh mục kèm số lượng phim
        $danhmucs = DanhMuc::withCount('phims')->get();
        return view('staff.danhmuc.index', compact('danhmucs'));
    }

    public function create()
    {
        return view('staff.danhmuc.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|string|max:255|unique:danh_muc,ten',
        ], [
            'ten.required' => 'Tên danh mục không được để trống.',
            'ten.unique' => 'Tên danh mục này đã tồn tại.',
        ]);

        DanhMuc::create([
            'ten' => $request->ten,
            'mo_ta' => $request->mo_ta ?? null,
        ]);

        return redirect()->route('staff.danhmuc.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function edit($id)
    {
        $danhmuc = DanhMuc::findOrFail($id);
        return view('staff.danhmuc.edit', compact('danhmuc'));
    }

    public function update(Request $request, $id)
    {
        $danhmuc = DanhMuc::findOrFail($id);

        $request->validate([
            'ten' => 'required|string|max:255|unique:danh_muc,ten,' . $id,
        ], [
            'ten.required' => 'Tên danh mục không được để trống.',
            'ten.unique' => 'Tên danh mục này đã tồn tại.',
        ]);

        $danhmuc->update([
            'ten' => $request->ten,
            'mo_ta' => $request->mo_ta ?? null,
        ]);

        return redirect()->route('staff.danhmuc.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy($id)
    {
        $danhmuc = DanhMuc::findOrFail($id);

        // 🔒 Không cho phép xóa nếu danh mục đang có phim
        if ($danhmuc->phims()->count() > 0) {
            return redirect()->route('staff.danhmuc.index')
                ->with('error', 'Không thể xóa danh mục vì vẫn còn phim bên trong!');
        }

        $danhmuc->delete();

        return redirect()->route('staff.danhmuc.index')->with('success', 'Xóa danh mục thành công!');
    }
}
