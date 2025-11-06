<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BaiViet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BaiVietController extends Controller
{
    // Danh sách bài viết
    public function index(Request $request)
{
    $query = BaiViet::query();

    // Lọc theo loại
    if ($request->filled('loai')) {
        $query->where('loai', $request->loai);
    }

    // Lọc theo trạng thái
    if ($request->filled('trang_thai')) {
        $today = now();
        switch ($request->trang_thai) {
            case 'dang_hien_thi':
                $query->where('is_active', 1)
                      ->where(function($q) use ($today) {
                          $q->whereNull('ngay_ket_thuc')
                            ->orWhereDate('ngay_ket_thuc', '>=', $today);
                      })
                      ->whereDate('ngay_phat_hanh', '<=', $today);
                break;
            case 'chua_phat_hanh':
                $query->whereDate('ngay_phat_hanh', '>', $today);
                break;
            case 'da_ket_thuc':
                $query->whereDate('ngay_ket_thuc', '<', $today);
                break;
            case 'an':
                $query->where('is_active', 0);
                break;
        }
    }

    // Tìm kiếm theo tiêu đề
    if ($request->filled('search')) {
        $query->where('tieu_de', 'like', '%' . $request->search . '%');
    }

    $baiviets = $query->latest()->paginate(10)->withQueryString();

    return view('admin.baiviet.index', compact('baiviets'));
}


    // Form thêm bài viết
    public function create()
    {
        $baiviet = null;
        return view('admin.baiviet.form');
    }

    // Lưu bài viết mới
    public function store(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required',
            'loai' => 'required',
            'ngay_phat_hanh' => 'required|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_phat_hanh',
        ], [
            'tieu_de.required' => 'Tiêu đề không được để trống.',
            'tieu_de.string' => 'Tiêu đề phải là chuỗi ký tự.',
            'tieu_de.max' => 'Tiêu đề không được dài quá 255 ký tự.',
            'noi_dung.required' => 'Nội dung không được để trống.',
            'loai.required' => 'Bạn chưa chọn loại bài viết.',
            'ngay_phat_hanh.required' => 'Ngày phát hành không được để trống.',
            'ngay_phat_hanh.date' => 'Ngày phát hành không hợp lệ.',
            'ngay_ket_thuc.date' => 'Ngày kết thúc không hợp lệ.',
            'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày phát hành.',
        ]);

        $path = $request->hasFile('hinh_anh')
            ? $request->file('hinh_anh')->store('uploads/baiviet', 'public')
            : null;

        BaiViet::create([
            'tieu_de'        => $request->tieu_de,
            'slug'           => Str::slug($request->tieu_de),
            'tom_tat'        => $request->tom_tat ?? '',
            'noi_dung'       => $request->noi_dung,
            'ngay_phat_hanh' => $request->ngay_phat_hanh,
            'ngay_ket_thuc'  => $request->ngay_ket_thuc,
            'loai'           => $request->loai,
            'hinh_anh'       => $path,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.baiviet.index')
                         ->with('success', 'Thêm bài viết thành công!');
    }

    // Form chỉnh sửa
    public function edit(BaiViet $baiviet)
    {
        return view('admin.baiviet.form', compact('baiviet'));
    }

    // Cập nhật bài viết
    public function update(Request $request, BaiViet $baiviet)
    {
        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required',
            'loai' => 'required',
            'ngay_phat_hanh' => 'required|date',
            'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_phat_hanh',
        ], [
            'tieu_de.required' => 'Tiêu đề không được để trống.',
            'tieu_de.string' => 'Tiêu đề phải là chuỗi ký tự.',
            'tieu_de.max' => 'Tiêu đề không được dài quá 255 ký tự.',
            'noi_dung.required' => 'Nội dung không được để trống.',
            'loai.required' => 'Bạn chưa chọn loại bài viết.',
            'ngay_phat_hanh.required' => 'Ngày phát hành không được để trống.',
            'ngay_phat_hanh.date' => 'Ngày phát hành không hợp lệ.',
            'ngay_ket_thuc.date' => 'Ngày kết thúc không hợp lệ.',
            'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày phát hành.',
        ]);
        $data = [
            'tieu_de'        => $request->tieu_de,
            'slug'           => Str::slug($request->tieu_de),
            'tom_tat'        => $request->tom_tat ?? $baiviet->tom_tat ?? '',
            'noi_dung'       => $request->noi_dung,
            'ngay_phat_hanh' => $request->ngay_phat_hanh,
            'ngay_ket_thuc'  => $request->ngay_ket_thuc,
            'loai'           => $request->loai,
            'is_active'      => $request->has('is_active') ? 1 : 0,
        ];

        if ($request->hasFile('hinh_anh')) {
            $data['hinh_anh'] = $request->file('hinh_anh')->store('uploads/baiviet', 'public');
        }

        $baiviet->update($data);

        return redirect()->route('admin.baiviet.index')
                         ->with('success', 'Cập nhật bài viết thành công!');
    }

    // Xóa bài viết
    public function destroy(BaiViet $baiviet)
    {
        $baiviet->delete();
        return back()->with('success', 'Xóa bài viết thành công!');
    }

    // Toggle trạng thái hiển thị
    public function toggleActive(BaiViet $baiviet)
    {
        $baiviet->is_active = !$baiviet->is_active;
        $baiviet->save();

        return back()->with('success', 'Bài viết "' . $baiviet->tieu_de . '" đã ' . ($baiviet->is_active ? 'bật' : 'tắt') . ' hiển thị.');
    }

    // Lấy bài viết công khai theo khoảng thời gian
    public function publicIndex(Request $request)
    {
        $today = Carbon::today();

        $baiviets = BaiViet::where('is_active', 1)
            ->whereDate('ngay_phat_hanh', '<=', $today)
            ->where(function($q) use ($today) {
                $q->whereNull('ngay_ket_thuc')->orWhereDate('ngay_ket_thuc', '>=', $today);
            })
            ->latest()
            ->paginate(10);

        return view('client.baiviet.index', compact('baiviets'));
    }
}
