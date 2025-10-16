<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\ComboChiTiet;
use App\Models\SanPham;
use Illuminate\Http\Request;

class ComboController extends Controller
{
    public function index()
    {
        // Lấy tất cả combo chưa bị xóa mềm
        $combos = Combo::all();
        return view('admin.combo.index', compact('combos'));
    }

    public function create()
    {
        $sanPhams = SanPham::all();
        return view('admin.combo.create', compact('sanPhams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten' => 'required|string|max:255|unique:combo,ten',
            'gia' => 'required|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'so_luong' => 'required|integer|min:1', // Thêm số lượng combo
            'chi_tiet.*.san_pham_id' => 'required|exists:san_pham,id',
            'chi_tiet.*.so_luong' => 'required|integer|min:1',
        ], [
            'ten.required' => 'Trường tên là bắt buộc.',
            'ten.unique' => 'Tên combo đã tồn tại.',
            'gia.required' => 'Trường giá là bắt buộc.',
            'gia.numeric' => 'Trường giá phải là số.',
            'so_luong.required' => 'Trường số lượng là bắt buộc.',
            'so_luong.integer' => 'Trường số lượng phải là số nguyên.',
            'so_luong.min' => 'Trường số lượng phải lớn hơn hoặc bằng 1.',
            'chi_tiet.*.san_pham_id.required' => 'Trường sản phẩm là bắt buộc.',
            'chi_tiet.*.san_pham_id.exists' => 'Sản phẩm không hợp lệ.',
            'chi_tiet.*.so_luong.required' => 'Trường số lượng là bắt buộc.',
            'chi_tiet.*.so_luong.integer' => 'Trường số lượng phải là số nguyên.',
            'chi_tiet.*.so_luong.min' => 'Trường số lượng phải lớn hơn hoặc bằng 1.',
        ]);

        // Kiểm tra chi tiết combo không được trùng lặp
        $sanPhamIds = array_column($request->chi_tiet, 'san_pham_id');
        if (count($sanPhamIds) !== count(array_unique($sanPhamIds))) {
            return redirect()->back()->withErrors(['chi_tiet' => 'Chi tiết combo không được chứa sản phẩm trùng lặp.'])->withInput();
        }

        $errors = []; // Mảng lưu trữ lỗi

        foreach ($request->chi_tiet as $index => $chiTiet) {
            $sanPham = SanPham::findOrFail($chiTiet['san_pham_id']);

            // Kiểm tra số lượng không vượt quá số lượng trong bảng sản phẩm
            if ($chiTiet['so_luong'] > $sanPham->so_luong) {
                $errors["chi_tiet.$index.so_luong"] = 
                    'Số lượng sản phẩm "' . $sanPham->ten . '" không được vượt quá ' . $sanPham->so_luong;
            }
        }

        // Nếu có lỗi, trả về thông báo
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // Lưu combo
        $combo = Combo::create([
            'ten' => $request->ten,
            'gia' => $request->gia,
            'mo_ta' => $request->mo_ta,
            'so_luong' => $request->so_luong, // Lưu số lượng combo
        ]);

        // Lưu chi tiết combo và trừ số lượng sản phẩm
        foreach ($request->chi_tiet as $chiTiet) {
            ComboChiTiet::create([
                'combo_id' => $combo->id,
                'san_pham_id' => $chiTiet['san_pham_id'],
                'so_luong' => $chiTiet['so_luong'],
            ]);

            // Trừ số lượng sản phẩm
            $sanPham = SanPham::findOrFail($chiTiet['san_pham_id']);
            $sanPham->so_luong -= $request->so_luong * $chiTiet['so_luong']; // Trừ tổng số lượng sản phẩm
            $sanPham->save();
        }

        return redirect()->route('admin.combo.index')->with('success', 'Combo đã được thêm thành công.');
    }
    public function edit(Combo $combo)
    {
        $sanPhams = SanPham::all();
        return view('admin.combo.edit', compact('combo', 'sanPhams'));
    }

    public function update(Request $request, Combo $combo)
    {
        $request->validate([
            'ten' => 'required|string|max:255|unique:combo,ten,' . $combo->id,
            'gia' => 'required|numeric|min:0',
            'mo_ta' => 'nullable|string',
            'so_luong' => 'required|integer|min:1',
            'chi_tiet.*.san_pham_id' => 'required|exists:san_pham,id',
            'chi_tiet.*.so_luong' => 'required|integer|min:1',
        ], [
            'ten.required' => 'Trường tên là bắt buộc.',
            'ten.unique' => 'Tên combo đã tồn tại.',
            'gia.required' => 'Trường giá là bắt buộc.',
            'gia.numeric' => 'Trường giá phải là số.',
            'so_luong.required' => 'Trường số lượng là bắt buộc.',
            'so_luong.integer' => 'Trường số lượng phải là số nguyên.',
            'so_luong.min' => 'Trường số lượng phải lớn hơn hoặc bằng 1.',
            'chi_tiet.*.san_pham_id.required' => 'Trường sản phẩm là bắt buộc.',
            'chi_tiet.*.san_pham_id.exists' => 'Sản phẩm không hợp lệ.',
            'chi_tiet.*.so_luong.required' => 'Trường số lượng là bắt buộc.',
            'chi_tiet.*.so_luong.integer' => 'Trường số lượng phải là số nguyên.',
            'chi_tiet.*.so_luong.min' => 'Trường số lượng phải lớn hơn hoặc bằng 1.',
        ]);

        // Kiểm tra chi tiết combo không được trùng lặp
        $sanPhamIds = array_column($request->chi_tiet, 'san_pham_id');
        if (count($sanPhamIds) !== count(array_unique($sanPhamIds))) {
            return redirect()->back()->withErrors(['chi_tiet' => 'Chi tiết combo không được chứa sản phẩm trùng lặp.'])->withInput();
        }


        $errors = []; // Mảng lưu trữ lỗi

        // Hoàn lại tổng số lượng sản phẩm từ combo cũ
        $currentChiTiet = ComboChiTiet::where('combo_id', $combo->id)->get();
        foreach ($currentChiTiet as $chiTiet) {
            $sanPham = SanPham::findOrFail($chiTiet->san_pham_id);
            $sanPham->so_luong += $combo->so_luong * $chiTiet->so_luong; // Hoàn lại tổng số lượng
            $sanPham->save();
        }

        // Kiểm tra tổng số lượng sản phẩm cho combo mới
        foreach ($request->chi_tiet as $index => $chiTiet) {
            $sanPham = SanPham::findOrFail($chiTiet['san_pham_id']);

            // Tính tổng số lượng sản phẩm cần trừ
            $requiredSoLuong = $request->so_luong * $chiTiet['so_luong'];
            if ($requiredSoLuong > $sanPham->so_luong) {
                $errors["chi_tiet.$index.so_luong"] = 
                    'Số lượng sản phẩm "' . $sanPham->ten . '" không đủ. Cần ' . $requiredSoLuong . ' nhưng chỉ còn ' . $sanPham->so_luong;
            }
        }

        // Nếu có lỗi, trả về thông báo
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        // Cập nhật combo
        $combo->update([
            'ten' => $request->ten,
            'gia' => $request->gia,
            'mo_ta' => $request->mo_ta,
            'so_luong' => $request->so_luong,
        ]);

        // Xóa tất cả chi tiết combo cũ
        ComboChiTiet::where('combo_id', $combo->id)->delete();

        // Lưu chi tiết combo mới và trừ tổng số lượng sản phẩm
        foreach ($request->chi_tiet as $chiTiet) {
            ComboChiTiet::create([
                'combo_id' => $combo->id,
                'san_pham_id' => $chiTiet['san_pham_id'],
                'so_luong' => $chiTiet['so_luong'],
            ]);

            // Trừ tổng số lượng sản phẩm
            $sanPham = SanPham::findOrFail($chiTiet['san_pham_id']);
            $sanPham->so_luong -= $request->so_luong * $chiTiet['so_luong']; // Trừ tổng số lượng sản phẩm
            $sanPham->save();
        }

        return redirect()->route('admin.combo.index')->with('success', 'Combo đã được cập nhật thành công.');
    }

    public function destroy(Combo $combo)
    {
        $combo->delete();
        return redirect()->route('admin.combo.index')->with('success', 'Combo đã được xóa mềm.');
    }

    public function trashed()
    {
        // Lấy danh sách combo đã bị xóa mềm
        $combos = Combo::onlyTrashed()->get();

        return view('admin.combo.trashed', compact('combos'));
    }

    public function restore($id)
    {
        $combo = Combo::onlyTrashed()->findOrFail($id);
        $combo->restore();

        return redirect()->route('admin.combo.trashed')->with('success', 'Combo đã được khôi phục thành công.');
    }

    public function forceDelete($id)
    {
        $combo = Combo::onlyTrashed()->findOrFail($id);

        // Xóa tất cả chi tiết combo liên quan
        ComboChiTiet::where('combo_id', $combo->id)->delete();

        // Xóa combo
        $combo->forceDelete();

        return redirect()->route('admin.combo.trashed')->with('success', 'Combo đã được xóa vĩnh viễn.');
    }
   
}
