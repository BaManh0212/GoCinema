<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhongChieu;
use App\Models\DinhDang;
use App\Models\Rap; // 👈 nếu bạn có model Rap

class PhongChieuController extends Controller
{
    public function index(Request $request)
    {
        $query = PhongChieu::with(['dinhDang', 'ghes'])
            ->withCount('ghes');

        // 🔍 Tìm kiếm theo tên hoặc mã phòng
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('ten', 'like', "%$q%")
                    ->orWhere('id', 'like', "%$q%");
            });
        }

        // 🏢 Lọc theo rạp chiếu (nếu có cột rap_id)
        if ($request->filled('rap_id')) {
            $query->where('rap_id', $request->rap_id);
        }

        // 📏 Sắp xếp
        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('ten', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('ten', 'desc');
                break;
            case 'seats_asc':
                $query->orderBy('ghes_count', 'asc');
                break;
            case 'seats_desc':
                $query->orderBy('ghes_count', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('id', 'asc');
        }

        $phongchieus = $query->paginate(10)->withQueryString();

        // 🏢 Lấy danh sách rạp cho dropdown lọc
      $raps = collect(); // tạm thời rỗng


        return view('staff.phong_chieu.index', compact('phongchieus', 'raps'));
    }

    public function create()
    {
        $dinhdangs = DinhDang::orderBy('ten')->get();
        return view('staff.phong_chieu.create', compact('dinhdangs'));
    }

    public function store(Request $request)
    {
    $validated = $request->validate([
        'ten' => 'required|string|max:255',
        'dinh_dang_id' => 'nullable|exists:dinh_dang,id',
        'trang_thai' => 'required|in:hoat_dong,bao_tri,ngung_su_dung',
        'so_do' => 'nullable|string', // thêm validate
    ], [
        'ten.required' => 'Tên phòng chiếu không được để trống.',
        'ten.max' => 'Tên phòng không được vượt quá 255 ký tự.',
        'dinh_dang_id.exists' => 'Định dạng không hợp lệ.',
        'trang_thai.required' => 'Trạng thái là bắt buộc.',
    ]);

    $validated['rap_id'] = 1;
    $validated['so_do'] = $request->input('so_do'); // thêm so_do vào validated

    if (PhongChieu::where('ten', $validated['ten'])->exists()) {
        return back()->withInput()->withErrors([
            'ten' => 'Phòng chiếu này đã tồn tại.'
        ]);
    }

    try {
        PhongChieu::create($validated);
        return redirect()->route('staff.phongchieu.index')
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
        return view('staff.phong_chieu.edit', compact('phongchieu', 'dinhdangs'));
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

        $validated['rap_id'] = 1;

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
            return redirect()->route('staff.phongchieu.index')
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
            return redirect()->route('staff.phongchieu.index')
                ->with('success', 'Xóa phòng chiếu thành công');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }
}
