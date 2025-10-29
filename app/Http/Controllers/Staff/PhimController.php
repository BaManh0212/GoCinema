<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Phim;
use App\Models\DanhMuc;
use App\Models\NgonNgu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PhimController extends Controller
{
    public function index(Request $request)
{
    $query = Phim::with(['danhMucs', 'ngonNgu']);

    // 🔍 Tìm kiếm theo tiêu đề
    if ($request->filled('search')) {
        $query->where('tieu_de', 'like', '%' . $request->search . '%');
    }

    // 🗂️ Lọc theo danh mục
    if ($request->filled('danh_muc_id')) {
        $query->whereHas('danhMucs', function ($q) use ($request) {
            // so sánh theo id của danh mục liên quan (fully-qualified to avoid ambiguity)
            $q->where('danh_muc.id', $request->danh_muc_id);
        });
    }

    // 🗣️ Lọc theo ngôn ngữ
    if ($request->filled('ngon_ngu_id')) {
        $query->where('ngon_ngu_id', $request->ngon_ngu_id);
    }

    // 🎞️ Lọc theo trạng thái (0: ngưng chiếu, 1: đang chiếu, 2: sắp chiếu)
    if ($request->filled('trang_thai')) {
        $query->where('trang_thai', $request->trang_thai);
    }

    // 📅 Sắp xếp theo ngày công chiếu mới nhất
    $query->orderByDesc('ngay_cong_chieu');

    // 📄 Phân trang
    $phims = $query->paginate(10)->appends($request->query());

    // ⚙️ Xác định trạng thái chiếu theo ngày
    foreach ($phims as $phim) {
        $today = now();
        $ngayBatDau = $phim->ngay_cong_chieu ? Carbon::parse($phim->ngay_cong_chieu) : null;
        $ngayKetThuc = $phim->ngay_ket_thuc ? Carbon::parse($phim->ngay_ket_thuc) : null;

        if ($ngayBatDau && $today->lt($ngayBatDau)) {
            $phim->trang_thai_chieu = 'Sắp chiếu';
            $phim->trang_thai_mau = 'bg-info text-dark';
        } elseif ($ngayKetThuc && $today->gt($ngayKetThuc)) {
            $phim->trang_thai_chieu = 'Ngưng chiếu';
            $phim->trang_thai_mau = 'bg-secondary text-white';
        } else {
            $phim->trang_thai_chieu = 'Đang chiếu';
            $phim->trang_thai_mau = 'bg-success text-white';
        }
    }

    $danhMucs = DanhMuc::all();
    $ngonNgus = NgonNgu::all();

    return view('staff.phim.index', compact('phims', 'danhMucs', 'ngonNgus'));
}

    public function show($id)
    {
        $phim = Phim::with(['danhMucs', 'ngonNgu'])->findOrFail($id);
        return view('staff.phim.show', compact('phim'));
    }
}
