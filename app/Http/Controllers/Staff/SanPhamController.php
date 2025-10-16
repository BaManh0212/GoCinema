<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use Illuminate\Http\Request;

class SanPhamController extends Controller
{
    /**
     * 🧾 Hiển thị danh sách sản phẩm
     */
    public function index()
    {
        $sanPhams = SanPham::orderBy('id', 'desc')->get();
        return view('staff.san_pham.index', compact('sanPhams'));
    }

    /**
     * ➕ Form thêm sản phẩm
     */
    public function create()
    {
        if (auth()->user()->vaiTro->ten !== 'quan_ly') {
            return redirect()->back()->with('error', '🚫 Bạn không có quyền thêm sản phẩm!');
        }
        return view('staff.san_pham.create');
    }

    /**
     * 💾 Lưu sản phẩm mới
     */
    public function store(Request $request)
    {
        if (auth()->user()->vaiTro->ten !== 'quan_ly') {
    abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }
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

        return redirect()->route('staff.san_pham.index')
                         ->with('success', '🟢 Sản phẩm đã được thêm thành công!');
    }

    /**
     * ✏️ Form sửa sản phẩm
     */
    public function edit(SanPham $sanPham)
    {
        return view('staff.san_pham.edit', compact('sanPham'));
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

        return redirect()->route('staff.san_pham.index')
                         ->with('success', '🟢 Sản phẩm đã được cập nhật thành công!');
    }

    /**
     * 🗑️ Xóa mềm sản phẩm
     */
    public function destroy(SanPham $sanPham)
    {
        if (auth()->user()->vaiTro->ten !== 'quan_ly') {
            return redirect()->back()->with('error', '🚫 Bạn không có quyền xóa sản phẩm!');
        }
        $sanPham->delete();

        return redirect()->route('staff.san_pham.index')
                         ->with('success', '🗑️ Sản phẩm đã được xóa thành công.');
    }

    /**
     * 🧺 Hiển thị danh sách sản phẩm đã xóa (thùng rác)
     */
    public function trashed()
    {
        $sanPhams = SanPham::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('staff.san_pham.trashed', compact('sanPhams'));
    }

    /**
     * ♻️ Khôi phục sản phẩm đã xóa
     */
    public function restore($id)
    {
        $sanPham = SanPham::onlyTrashed()->findOrFail($id);
        $sanPham->restore();

        return redirect()->route('staff.san_pham.trashed')
                         ->with('success', '✅ Sản phẩm đã được khôi phục.');
    }

    /**
     * ❌ Xóa vĩnh viễn sản phẩm
     */
    public function forceDelete($id)
    {
        $sanPham = SanPham::onlyTrashed()->findOrFail($id);
        $sanPham->forceDelete();

        return redirect()->route('staff.san_pham.trashed')
                         ->with('success', '🗑️ Sản phẩm đã bị xóa vĩnh viễn.');
    }
}
