<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaGiamGia;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MaGiamGiaController extends Controller
{
    /**
     * Danh sách mã giảm giá
     */
    public function index(Request $request)
    {
        $query = MaGiamGia::query();

        if ($request->filled('search')) {
            $query->where('ma', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kich_hoat')) {
            $query->where('kich_hoat', $request->kich_hoat);
        }

        $maGiamGia = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.ma_giam_gia.index', compact('maGiamGia'));
    }

    /**
     * Form thêm mới
     */
    public function create()
    {
        return view('admin.ma_giam_gia.create');
    }

    /**
     * Lưu mã giảm giá mới
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'ma' => 'required|unique:ma_giam_gia,ma|max:50',
                'loai' => 'required|in:phan_tram,so_tien',
                'gia_tri' => 'required|numeric|min:1',
                'giam_toi_da' => 'nullable|numeric|min:0',
                'gia_tri_don_hang_toi_thieu' => 'nullable|numeric|min:0',
                'ap_dung_cho' => 'required|string|in:tat_ca,ve,san_pham',
                'so_luong' => 'required|integer|min:1',
                'so_lan_su_dung' => 'nullable|integer|min:1',
                'ngay_bat_dau' => 'nullable|date',
                'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            ], [
                // 🔹 Thông báo lỗi tiếng Việt
                'ma.required' => 'Vui lòng nhập mã giảm giá.',
                'ma.unique' => 'Mã giảm giá này đã tồn tại.',
                'ma.max' => 'Mã giảm giá không được vượt quá 50 ký tự.',
                'loai.required' => 'Vui lòng chọn loại giảm giá.',
                'loai.in' => 'Loại giảm giá không hợp lệ.',
                'gia_tri.required' => 'Vui lòng nhập giá trị giảm.',
                'gia_tri.numeric' => 'Giá trị giảm phải là số.',
                'gia_tri.min' => 'Giá trị giảm phải lớn hơn 0.',
                'giam_toi_da.numeric' => 'Giảm tối đa phải là số.',
                'ap_dung_cho.required' => 'Vui lòng chọn đối tượng áp dụng.',
                'so_luong.required' => 'Vui lòng nhập số lượng.',
                'so_luong.integer' => 'Số lượng phải là số nguyên.',
                'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            ]);

            MaGiamGia::create($validated);

            return redirect()->route('admin.ma_giam_gia.index')->with('success', '✅ Thêm mã giảm giá thành công!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Form chỉnh sửa
     */
    public function edit(MaGiamGia $maGiamGia)
    {
        return view('admin.ma_giam_gia.edit', compact('maGiamGia'));
    }

    /**
     * Cập nhật mã giảm giá
     */
    public function update(Request $request, MaGiamGia $maGiamGia)
    {
        try {
            $validated = $request->validate([
                'ma' => 'required|unique:ma_giam_gia,ma,' . $maGiamGia->id . '|max:50',
                'loai' => 'required|in:phan_tram,so_tien',
                'gia_tri' => 'required|numeric|min:1',
                'giam_toi_da' => 'nullable|numeric|min:0',
                'gia_tri_don_hang_toi_thieu' => 'nullable|numeric|min:0',
                'ap_dung_cho' => 'required|string|in:tat_ca,ve,san_pham',
                'so_luong' => 'required|integer|min:1',
                'so_lan_su_dung' => 'nullable|integer|min:1',
                'ngay_bat_dau' => 'nullable|date',
                'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_bat_dau',
            ]);

            $maGiamGia->update($validated);

            return redirect()->route('admin.ma_giam_gia.index')->with('success', '✏️ Cập nhật mã giảm giá thành công!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }
    public function show($id)
{
    $maGiamGia = MaGiamGia::withTrashed()->findOrFail($id);

    return view('admin.ma_giam_gia.show', compact('maGiamGia'));
}

    /**
     * Xóa mềm
     */
    public function destroy(MaGiamGia $maGiamGia)
    {
        $maGiamGia->delete();
        return back()->with('success', '🗑️ Đã chuyển mã giảm giá vào thùng rác!');
    }

    /**
     * Thùng rác
     */
    public function trash()
    {
        $maGiamGia = MaGiamGia::onlyTrashed()->orderByDesc('deleted_at')->paginate(10)->withQueryString();
        return view('admin.ma_giam_gia.trash', compact('maGiamGia'));
    }

    /**
     * Khôi phục
     */
    public function restore($id)
    {
        $maGiamGia = MaGiamGia::onlyTrashed()->findOrFail($id);
        $maGiamGia->restore();
        return back()->with('success', '♻️ Khôi phục mã giảm giá thành công!');
    }

    /**
     * Xóa vĩnh viễn
     */
    public function forceDelete($id)
    {
        $maGiamGia = MaGiamGia::onlyTrashed()->findOrFail($id);
        $maGiamGia->forceDelete();
        return back()->with('success', '🚨 Đã xóa vĩnh viễn mã giảm giá!');
    }

    /**
     * Kích hoạt / vô hiệu hóa
     */
    public function toggle($id)
    {
        $maGiamGia = MaGiamGia::findOrFail($id);
        $maGiamGia->update(['kich_hoat' => !$maGiamGia->kich_hoat]);
        return back()->with('success', '🔁 Cập nhật trạng thái thành công!');
    }
}
