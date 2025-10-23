<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use Illuminate\Http\Request;
use App\Models\ComboChiTiet;

class SanPhamController extends Controller
{
    /**
     * 🧾 Hiển thị danh sách sản phẩm
     */
    public function index(Request $request)
    {
        // Lấy query builder để thêm điều kiện lọc
        $query = SanPham::query();

        // Lọc theo từ khóa tên
        if ($request->filled('q')) {
            $query->where('ten', 'like', '%' . $request->q . '%');
        }

        // Sắp xếp
        $sortType = $request->sort ?? 'gia_desc'; // mặc định giá giảm dần
        switch ($sortType) {
            case 'gia_asc':
                $query->orderBy('gia', 'asc');
                break;
            case 'gia_desc':
                $query->orderBy('gia', 'desc');
                break;
        }

        $sanPhams = $query->get();
        $filters = $request->only(['q', 'sort']);

        return view('admin.san_pham.index', compact('sanPhams', 'filters'));
    }

    /**
     * ➕ Form thêm sản phẩm
     */
    public function create()
    {
        return view('admin.san_pham.create');
    }

    /**
     * 💾 Lưu sản phẩm mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|string|max:255|unique:san_pham,ten',
            'gia' => 'required|numeric|min:0',
            'so_luong' => 'required|integer|min:0',
        ], [
            'ten.required' => 'Bắt buộc điền tên sản phẩm.',
            'ten.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'ten.unique' => 'Tên sản phẩm đã tồn tại.',
            'gia.required' => 'Bắt buộc nhập giá sản phẩm.',
            'gia.numeric' => 'Giá sản phẩm phải là số.',
            'gia.min' => 'Giá sản phẩm không được nhỏ hơn 0.',
            'so_luong.required' => 'Bắt buộc nhập số lượng.',
            'so_luong.integer' => 'Số lượng phải là số nguyên.',
            'so_luong.min' => 'Số lượng không được nhỏ hơn 0.',
        ]);

        SanPham::create($request->only(['ten', 'gia', 'so_luong']));

        return redirect()->route('admin.san_pham.index')
                         ->with('success', '🟢 Sản phẩm đã được thêm thành công!');
    }

    /**
     * ✏️ Form sửa sản phẩm
     */
    public function edit(SanPham $sanPham)
    {
        return view('admin.san_pham.edit', compact('sanPham'));
    }

    /**
     * 🔄 Cập nhật sản phẩm
     */
    public function update(Request $request, SanPham $sanPham)
    {
        $request->validate([
            'ten' => 'required|string|max:255|unique:san_pham,ten,' . $sanPham->id,
            'gia' => 'required|numeric|min:0',
            'so_luong' => 'required|integer|min:0',
        ], [
            'ten.required' => 'Bắt buộc điền tên sản phẩm.',
            'ten.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'ten.unique' => 'Tên sản phẩm đã tồn tại.',
            'gia.required' => 'Bắt buộc nhập giá sản phẩm.',
            'gia.numeric' => 'Giá sản phẩm phải là số.',
            'gia.min' => 'Giá sản phẩm không được nhỏ hơn 0.',
            'so_luong.required' => 'Bắt buộc nhập số lượng.',
            'so_luong.integer' => 'Số lượng phải là số nguyên.',
            'so_luong.min' => 'Số lượng không được nhỏ hơn 0.',
        ]);

        $sanPham->update($request->only(['ten', 'gia', 'so_luong']));

        return redirect()->route('admin.san_pham.index')
                         ->with('success', '🟢 Sản phẩm đã được cập nhật thành công!');
    }

    /**
     * 🗑️ Xóa mềm sản phẩm
     */
    public function destroy(SanPham $sanPham)
{
    // 🔍 Kiểm tra xem sản phẩm có nằm trong combo nào không
    $comboDangDung = ComboChiTiet::where('san_pham_id', $sanPham->id)
                                 ->with('combo')
                                 ->get();

    // Nếu đang nằm trong combo → chặn xóa
    if ($comboDangDung->isNotEmpty()) {
        $danhSachCombo = $comboDangDung->pluck('combo.ten')->implode(', ');
        return redirect()->route('admin.san_pham.index')
                         ->with('error', "❌ Không thể xóa sản phẩm '{$sanPham->ten}' vì đang nằm trong các combo: {$danhSachCombo}.");
    }

    // ✅ Nếu không nằm trong combo nào → cho phép xóa
    $sanPham->delete();

    return redirect()->route('admin.san_pham.index')
                     ->with('success', '🗑️ Sản phẩm đã được xóa thành công.');
}

    /**
     * 🧺 Hiển thị danh sách sản phẩm đã xóa (thùng rác)
     */
    public function trashed()
    {
        $sanPhams = SanPham::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('admin.san_pham.trashed', compact('sanPhams'));
    }

    /**
     * ♻️ Khôi phục sản phẩm đã xóa
     */
    public function restore($id)
    {
        $sanPham = SanPham::onlyTrashed()->findOrFail($id);
        $sanPham->restore();

        return redirect()->route('admin.san_pham.trashed')
                         ->with('success', '✅ Sản phẩm đã được khôi phục.');
    }

    /**
     * ❌ Xóa vĩnh viễn sản phẩm
     */
    public function forceDelete($id)
    {
        $sanPham = SanPham::onlyTrashed()->findOrFail($id);
        $sanPham->forceDelete();

        return redirect()->route('admin.san_pham.trashed')
                         ->with('success', '🗑️ Sản phẩm đã bị xóa vĩnh viễn.');
    }
}
