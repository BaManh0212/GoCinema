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
            $q->where('danh_muc.id', $request->danh_muc_id);
        });
    }

    // 🗣️ Lọc theo ngôn ngữ
    if ($request->filled('ngon_ngu_id')) {
        $query->where('ngon_ngu_id', $request->ngon_ngu_id);
    }

    // 🎞️ Lọc theo trạng thái dựa trên ngày
    $today = now()->toDateString();

    if ($request->filled('trang_thai')) {
        switch ($request->trang_thai) {
            case 0: // Ngưng chiếu
                $query->whereNotNull('ngay_ket_thuc')
                      ->whereDate('ngay_ket_thuc', '<', $today);
                break;

            case 1: // Đang chiếu
                $query->whereDate('ngay_cong_chieu', '<=', $today)
                      ->where(function($q) use ($today) {
                          $q->whereNull('ngay_ket_thuc')
                            ->orWhereDate('ngay_ket_thuc', '>=', $today);
                      });
                break;

            case 2: // Sắp chiếu
                $query->whereDate('ngay_cong_chieu', '>', $today);
                break;
        }
    }

    // 📅 Sắp xếp theo ngày công chiếu mới nhất
    $query->orderByDesc('ngay_cong_chieu');

    // 📄 Phân trang
    $phims = $query->paginate(5)->appends($request->query());

    // ⚙️ Thêm trạng thái chiếu cho hiển thị
    foreach ($phims as $phim) {
        $ngayBatDau = $phim->ngay_cong_chieu ? Carbon::parse($phim->ngay_cong_chieu) : null;
        $ngayKetThuc = $phim->ngay_ket_thuc ? Carbon::parse($phim->ngay_ket_thuc) : null;

        if ($ngayBatDau && now()->lt($ngayBatDau)) {
            $phim->trang_thai_chieu = 'Sắp chiếu';
            $phim->trang_thai_mau = 'bg-info text-dark';
        } elseif ($ngayKetThuc && now()->gt($ngayKetThuc)) {
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
