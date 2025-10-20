<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\ComboChiTiet;
use App\Models\SanPham;
use Illuminate\Http\Request;

class ComboController extends Controller
{
    /** =============================
     * 🧩 DANH SÁCH COMBO
     * ============================== */
    public function index()
    {
        $combos = Combo::all();
        return view('admin.combo.index', compact('combos'));
    }

    /** =============================
     * ➕ FORM THÊM COMBO
     * ============================== */
    public function create()
    {
        $sanPhams = SanPham::all();
        return view('admin.combo.create', compact('sanPhams'));
    }

    /** =============================
     * 💾 LƯU COMBO MỚI
     * ============================== */
public function store(Request $request)
{
    // ✅ 1. Validate dữ liệu đầu vào
    $request->validate([
        'ten' => 'required|string|max:255|unique:combo,ten',
        'gia' => 'required|numeric|min:0',
        'mo_ta' => 'nullable|string',
        'so_luong' => 'required|integer|min:1',
        'chi_tiet.*.san_pham_id' => 'required|exists:san_pham,id',
        'chi_tiet.*.so_luong' => 'required|integer|min:1',
    ], [
        'ten.required' => 'Vui lòng nhập tên combo.',
        'ten.unique' => 'Tên combo đã tồn tại.',
        'gia.required' => 'Vui lòng nhập giá combo.',
        'gia.numeric' => 'Giá combo phải là số.',
        'gia.min' => 'Giá combo phải lớn hơn hoặc bằng 0.',
        'so_luong.required' => 'Vui lòng nhập số lượng combo.',
        'so_luong.integer' => 'Số lượng combo phải là số nguyên.',
        'so_luong.min' => 'Số lượng combo phải lớn hơn 0.',
        'chi_tiet.*.san_pham_id.required' => 'Vui lòng chọn sản phẩm cho combo.',
        'chi_tiet.*.san_pham_id.exists' => 'Sản phẩm không hợp lệ.',
        'chi_tiet.*.so_luong.required' => 'Vui lòng nhập số lượng sản phẩm trong combo.',
        'chi_tiet.*.so_luong.integer' => 'Số lượng sản phẩm phải là số nguyên.',
        'chi_tiet.*.so_luong.min' => 'Số lượng sản phẩm phải lớn hơn 0.',
    ]);

    // ✅ 2. Kiểm tra trùng sản phẩm
    $sanPhamIds = array_column($request->chi_tiet, 'san_pham_id');
    if (count($sanPhamIds) !== count(array_unique($sanPhamIds))) {
        return back()
            ->withErrors(['chi_tiet' => 'Không được chọn trùng sản phẩm trong combo.'])
            ->withInput();
    }

    // ✅ 3. Tính số combo tối đa có thể tạo
    $maxCombo = PHP_INT_MAX;
    $errors = [];

    foreach ($request->chi_tiet as $index => $ct) {
        $sp = SanPham::find($ct['san_pham_id']);
        if (!$sp) continue;

        // Số combo tối đa theo từng sản phẩm
        $comboToiDaTheoSP = intdiv($sp->so_luong, $ct['so_luong']);
        $maxCombo = min($maxCombo, $comboToiDaTheoSP);

        // Nếu yêu cầu vượt kho → báo lỗi chi tiết
        $required = $request->so_luong * $ct['so_luong'];
        if ($required > $sp->so_luong) {
            $errors["chi_tiet.$index.so_luong"] =
                "Sản phẩm '{$sp->ten}' không đủ hàng (cần $required, còn {$sp->so_luong}).";
        }
    }

    // ✅ 4. Nếu có lỗi tồn kho thì trả về
    if (!empty($errors)) {
        return back()->withErrors($errors)->withInput();
    }

    // ✅ 5. Nếu số lượng combo yêu cầu > số tối đa cho phép → cảnh báo
    if ($request->so_luong > $maxCombo) {
        return back()
            ->withErrors([
                'so_luong' => "Số lượng combo vượt quá giới hạn tồn kho. 
                Chỉ có thể tạo tối đa {$maxCombo} combo dựa trên số lượng sản phẩm hiện có."
            ])
            ->withInput();
    }

    // ✅ 6. Lưu combo
    $combo = Combo::create([
        'ten' => $request->ten,
        'gia' => $request->gia,
        'mo_ta' => $request->mo_ta,
        'so_luong' => $request->so_luong,
    ]);

    // ✅ 7. Lưu chi tiết combo và trừ kho
    foreach ($request->chi_tiet as $ct) {
        ComboChiTiet::create([
            'combo_id' => $combo->id,
            'san_pham_id' => $ct['san_pham_id'],
            'so_luong' => $ct['so_luong'],
        ]);

        $sp = SanPham::find($ct['san_pham_id']);
        $sp->so_luong -= $request->so_luong * $ct['so_luong'];
        $sp->save();
    }

    return redirect()
        ->route('admin.combo.index')
        ->with('success', "✅ Combo đã được thêm thành công! 
        (Tối đa bạn có thể tạo {$maxCombo} combo dựa trên tồn kho hiện tại.)");
}


    /** =============================
     * ✏️ CHỈNH SỬA COMBO
     * ============================== */
    public function edit(Combo $combo)
    {
        $sanPhams = SanPham::all();
        return view('admin.combo.edit', compact('combo', 'sanPhams'));
    }

    /** =============================
     * 🔁 CẬP NHẬT COMBO
     * ============================== */
    public function update(Request $request, Combo $combo)
{
    // 1️⃣ Validate dữ liệu
    $request->validate([
        'ten' => 'required|string|max:255|unique:combo,ten,' . $combo->id,
        'gia' => 'required|numeric|min:0',
        'mo_ta' => 'nullable|string',
        'so_luong' => 'required|integer|min:1',
        'chi_tiet.*.san_pham_id' => 'required|exists:san_pham,id',
        'chi_tiet.*.so_luong' => 'required|integer|min:1',
    ], [
        'ten.required' => 'Vui lòng nhập tên combo.',
        'ten.unique' => 'Tên combo đã tồn tại.',
        'gia.required' => 'Vui lòng nhập giá combo.',
        'gia.numeric' => 'Giá combo phải là số.',
        'gia.min' => 'Giá combo phải lớn hơn hoặc bằng 0.',
        'so_luong.required' => 'Vui lòng nhập số lượng combo.',
        'so_luong.integer' => 'Số lượng combo phải là số nguyên.',
        'so_luong.min' => 'Số lượng combo phải lớn hơn 0.',
        'chi_tiet.*.san_pham_id.required' => 'Vui lòng chọn sản phẩm cho combo.',
        'chi_tiet.*.san_pham_id.exists' => 'Sản phẩm không hợp lệ.',
        'chi_tiet.*.so_luong.required' => 'Vui lòng nhập số lượng sản phẩm trong combo.',
        'chi_tiet.*.so_luong.integer' => 'Số lượng sản phẩm phải là số nguyên.',
        'chi_tiet.*.so_luong.min' => 'Số lượng sản phẩm phải lớn hơn 0.',
    ]);

     // 2️⃣ Check trùng sản phẩm
    $sanPhamIds = array_column($request->chi_tiet, 'san_pham_id');
    if (count($sanPhamIds) !== count(array_unique($sanPhamIds))) {
        return back()->withErrors(['chi_tiet' => 'Không được chọn trùng sản phẩm trong combo.'])->withInput();
    }

    // 3️⃣ Kiểm tra tồn kho thực tế (cộng combo cũ vào)
    $errors = [];
    foreach ($request->chi_tiet as $index => $ct) {
        $sp = SanPham::find($ct['san_pham_id']);
        if (!$sp) continue;

        // Tồn kho thực tế = tồn kho hiện tại + số lượng combo cũ của sản phẩm
        $oldCt = $combo->chiTiet->firstWhere('san_pham_id', $sp->id);
        $available = $sp->so_luong + ($oldCt ? $oldCt->so_luong * $combo->so_luong : 0);

        $required = $request->so_luong * $ct['so_luong'];
        if ($required > $available) {
            $errors["chi_tiet.$index.so_luong"] = 
                "Sản phẩm '{$sp->ten}' không đủ hàng (cần $required, còn $available).";
        }
    }

    if (!empty($errors)) {
        return back()->withErrors($errors)->withInput();
    }

    // 4️⃣ Hoàn kho cũ
    foreach ($combo->chiTiet as $ct) {
        $sp = SanPham::find($ct->san_pham_id);
        $sp->so_luong += $ct->so_luong * $combo->so_luong;
        $sp->save();
    }

    // 5️⃣ Cập nhật combo
    $combo->update([
        'ten' => $request->ten,
        'gia' => $request->gia,
        'mo_ta' => $request->mo_ta,
        'so_luong' => $request->so_luong,
    ]);

    // 6️⃣ Xóa chi tiết cũ
    ComboChiTiet::where('combo_id', $combo->id)->delete();

    // 7️⃣ Lưu chi tiết mới và trừ tồn kho
    foreach ($request->chi_tiet as $ct) {
        ComboChiTiet::create([
            'combo_id' => $combo->id,
            'san_pham_id' => $ct['san_pham_id'],
            'so_luong' => $ct['so_luong'],
        ]);

        $sp = SanPham::find($ct['san_pham_id']);
        $sp->so_luong -= $request->so_luong * $ct['so_luong'];
        $sp->save();
    }

    return redirect()->route('admin.combo.index')->with('success', '✅ Combo đã được cập nhật thành công.');
}


    /** =============================
     * 🗑️ XÓA MỀM COMBO
     * ============================== */
    public function destroy(Combo $combo)
    {
        // ✅ Hoàn lại số lượng khi xóa
        $chiTiet = ComboChiTiet::where('combo_id', $combo->id)->get();
        foreach ($chiTiet as $ct) {
            $sp = SanPham::find($ct->san_pham_id);
            $sp->so_luong += $combo->so_luong * $ct->so_luong;
            $sp->save();
        }

        $combo->delete();
        return redirect()->route('admin.combo.index')->with('success', '🗑️ Combo đã được xóa (và hoàn kho).');
    }

    /** =============================
     * 🧰 DANH SÁCH COMBO ĐÃ XÓA
     * ============================== */
    public function trashed()
    {
        $combos = Combo::onlyTrashed()->get();
        return view('admin.combo.trashed', compact('combos'));
    }

    /** =============================
     * 🔄 KHÔI PHỤC COMBO
     * ============================== */
    public function restore($id)
{
    $combo = Combo::onlyTrashed()->findOrFail($id);
    $combo->restore();

    // ✅ Trừ lại kho khi khôi phục combo
    $chiTiet = ComboChiTiet::where('combo_id', $combo->id)->get();
    $errors = [];

    foreach ($chiTiet as $ct) {
        $sp = SanPham::find($ct->san_pham_id);

        // Nếu sản phẩm không đủ hàng để khôi phục → báo lỗi
        if ($sp->so_luong < $combo->so_luong * $ct->so_luong) {
            $errors[] = "Sản phẩm '{$sp->ten}' không đủ hàng để khôi phục combo.";
        }
    }

    // Nếu có lỗi → rollback restore (soft delete lại) và báo lỗi
    if (!empty($errors)) {
        $combo->delete(); // soft delete lại combo
        return redirect()->route('admin.combo.trashed')->withErrors($errors);
    }

    // Nếu đủ hàng → trừ kho
    foreach ($chiTiet as $ct) {
        $sp = SanPham::find($ct->san_pham_id);
        $sp->so_luong -= $combo->so_luong * $ct->so_luong;
        $sp->save();
    }

    return redirect()->route('admin.combo.trashed')->with('success', '✅ Combo đã được khôi phục và trừ kho thành công.');
}


    /** =============================
     * 🚮 XÓA VĨNH VIỄN COMBO
     * ============================== */
    public function forceDelete($id)
    {
        ComboChiTiet::where('combo_id', $id)->delete();
        Combo::onlyTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('admin.combo.trashed')->with('success', '🗑️ Combo đã bị xóa vĩnh viễn.');
    }
}
