<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhongChieu;
use App\Models\DinhDang;
use App\Models\Rap; // 👈 nếu bạn có model Rap
use App\Models\SoDoGhe;

class PhongChieuController extends Controller
{
    public function index(Request $request)
    {
        $query = PhongChieu::with(['dinhDang']);
            // ->withCount('ghes');

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


        return view('admin.phong_chieu.index', compact('phongchieus', 'raps'));
    }

public function create()
{
    $dinhdangs = DinhDang::orderBy('ten')->get();
    return view('admin.phong_chieu.create', compact('dinhdangs'));
}

public function store(Request $request)
{
    // Validate dữ liệu phòng chiếu
    $validated = $request->validate([
        'ten' => 'required|string|max:255',
        'dinh_dang_id' => 'nullable|exists:dinh_dang,id',
        'trang_thai' => 'required|in:hoat_dong,bao_tri,ngung_su_dung',
        'so_do' => 'nullable|string',
        'ma_tran' => 'nullable|json', // cho phép gửi ma trận ghế
    ], [
        'ten.required' => 'Tên phòng chiếu không được để trống.',
        'ten.max' => 'Tên phòng không được vượt quá 255 ký tự.',
        'dinh_dang_id.exists' => 'Định dạng không hợp lệ.',
        'trang_thai.required' => 'Trạng thái là bắt buộc.',
        'ma_tran.json' => 'Sơ đồ ghế không hợp lệ.'
    ]);

    $validated['rap_id'] = 1;

    // Kiểm tra trùng tên phòng
    if (PhongChieu::where('ten', $validated['ten'])->exists()) {
        return back()->withInput()->withErrors([
            'ten' => 'Phòng chiếu này đã tồn tại.'
        ]);
    }

    try {
        // Tạo phòng chiếu
        $phong = PhongChieu::create($validated);

        // Nếu có sơ đồ ghế thì tạo luôn
        if (!empty($validated['ma_tran'])) {
            SoDoGhe::create([
                'phong_id' => $phong->id,
                'ma_tran' => $validated['ma_tran']
            ]);
        }

        return redirect()->route('admin.phongchieu.index')
            ->with('success', 'Thêm phòng chiếu thành công.');
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
