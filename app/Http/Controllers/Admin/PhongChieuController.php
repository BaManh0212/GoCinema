<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhongChieu;
use App\Models\DinhDang;

class PhongChieuController extends Controller
{
    public function index()
    {
        $phongchieus = PhongChieu::with('dinhDang')
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('admin.phong_chieu.index', compact('phongchieus'));
    }

    public function create()
    {
        $dinhdangs = DinhDang::orderBy('ten')->get();
        return view('admin.phong_chieu.create', compact('dinhdangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten' => 'required|string|max:255',
            'tong_ghe' => 'required|integer|min:1',
            'dinh_dang_id' => 'nullable|exists:dinh_dang,id',
            'trang_thai' => 'required|in:hoat_dong,bao_tri,ngung_su_dung',
        ], [
            'ten.required' => 'Tên phòng chiếu không được để trống.',
            'ten.max' => 'Tên phòng không được vượt quá 255 ký tự.',
            'tong_ghe.required' => 'Tổng số ghế là bắt buộc.',
            'tong_ghe.integer' => 'Tổng ghế phải là số nguyên.',
            'tong_ghe.min' => 'Phòng chiếu phải có ít nhất 1 ghế.',
            'dinh_dang_id.exists' => 'Định dạng không hợp lệ.',
            'trang_thai.required' => 'Trạng thái là bắt buộc.',
        ]);

        // Thêm mặc định rạp_id = 1 (vì chỉ có 1 rạp)
        $validated['rap_id'] = 1;

        // Kiểm tra trùng tên phòng chiếu trong rạp duy nhất
        if (PhongChieu::where('ten', $validated['ten'])->exists()) {
            return back()->withInput()->withErrors([
                'ten' => 'Phòng chiếu này đã tồn tại.'
            ]);
        }

        try {
            PhongChieu::create($validated);
            return redirect()->route('admin.phongchieu.index')
                ->with('success', 'Thêm phòng chiếu thành công');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        $phongchieu = PhongChieu::findOrFail($id);
        $dinhdangs = DinhDang::orderBy('ten')->get();
        return view('admin.phong_chieu.edit', compact('phongchieu', 'dinhdangs'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'ten' => 'required|string|max:255',
            'tong_ghe' => 'required|integer|min:1',
            'dinh_dang_id' => 'nullable|exists:dinh_dang,id',
            'trang_thai' => 'required|in:hoat_dong,bao_tri,ngung_su_dung',
        ], [
            'ten.required' => 'Tên phòng chiếu không được để trống.',
            'tong_ghe.required' => 'Tổng ghế là bắt buộc.',
            'trang_thai.required' => 'Trạng thái là bắt buộc.',
        ]);

        // Mặc định rạp_id = 1
        $validated['rap_id'] = 1;

        // Kiểm tra trùng tên (bỏ qua chính nó)
        if (PhongChieu::where('ten', $validated['ten'])
            ->where('id', '<>', $id)
            ->exists()) {
            return back()->withInput()->withErrors([
                'ten' => 'Phòng chiếu này đã tồn tại.'
            ]);
        }

        try {
            $phongchieu = PhongChieu::findOrFail($id);
            $phongchieu->update($validated);
            return redirect()->route('admin.phongchieu.index')
                ->with('success', 'Cập nhật phòng chiếu thành công');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $phongchieu = PhongChieu::findOrFail($id);
            $phongchieu->delete();
            return redirect()->route('admin.phongchieu.index')
                ->with('success', 'Xóa phòng chiếu thành công');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }
}
