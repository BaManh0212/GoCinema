<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DanhMuc;
use App\Models\Phim;
use App\Models\TheLoai;
use App\Models\Rap;
use Carbon\Carbon;

class PhimController extends Controller
{
    /**
     * ✅ Hiển thị tất cả phim (có lọc & phân trang)
     */
    public function index(Request $request)
    {
        $today = Carbon::now()->startOfDay();
        $query = Phim::with('danhMucs'); // Quan hệ nhiều-nhiều

        // 🔍 Lọc theo từ khóa
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where('tieu_de', 'like', "%{$search}%");
        }

        // 🏷️ Lọc theo danh mục (many-to-many)
        if ($request->filled('danh_muc')) {
            $query->whereHas('danhMucs', function ($q) use ($request) {
                $q->where('danh_muc.id', $request->danh_muc);
            });
        }

        // 🎬 Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            if ($request->trang_thai === 'dang_chieu') {
                $query->whereDate('ngay_cong_chieu', '<=', $today)
                      ->where(function ($q) use ($today) {
                          $q->whereNull('ngay_ket_thuc')
                            ->orWhereDate('ngay_ket_thuc', '>=', $today);
                      });
            } elseif ($request->trang_thai === 'sap_chieu') {
                $query->whereDate('ngay_cong_chieu', '>', $today);
            }
        }

        // 📅 Sắp xếp & phân trang
        $movies = $query->orderByDesc('created_at')
                        ->paginate(12)
                        ->withQueryString();

        // 📂 Lấy danh mục để hiển thị bộ lọc
        $danhMucs = DanhMuc::orderBy('ten')->get();

        return view('client.movies.index', compact('movies', 'danhMucs'));
    }

    /**
     * 🎞️ Hiển thị phim theo danh mục + các bộ lọc mở rộng
     */
    public function category(Request $request, $slug)
    {
        $danhMuc = DanhMuc::where('slug', $slug)->firstOrFail();

        $theLoaiId = $request->integer('the_loai');
        $rapId = $request->integer('rap');
        $date = $request->filled('date') ? $request->date('date') : null;

        // ✅ Sử dụng quan hệ many-to-many: danhMuc -> phims()
        $query = $danhMuc->phims()->with(['theLoais', 'danhMucs']);

        // 🎭 Lọc theo thể loại
        if ($theLoaiId) {
            $query->whereHas('theLoais', function ($q) use ($theLoaiId) {
                $q->where('id', $theLoaiId);
            });
        }

        // 🎥 Lọc theo rạp và ngày chiếu
        if ($rapId || $date) {
            $query->where(function ($q) use ($rapId, $date) {
                if ($rapId) {
                    $q->whereHas('suatChieus', function ($sq) use ($rapId, $date) {
                        $sq->whereHas('phong', function ($pq) use ($rapId) {
                            $pq->where('rap_id', $rapId);
                        });
                        if ($date) {
                            $sq->whereDate('gio_bat_dau', $date);
                        }
                    });
                }

                if ($date) {
                    // Hoặc có ngày công chiếu trùng
                    $q->orWhereDate('ngay_cong_chieu', $date);
                }
            });
        }

        // 🔢 Phân trang
        $movies = $query->orderByDesc('created_at')
                        ->paginate(12)
                        ->withQueryString();

        // 🧩 Dữ liệu cho bộ lọc
        $theLoais = TheLoai::orderBy('ten')->get();
        $raps = Rap::orderBy('ten')->get();

        return view('client.movies.category', compact('danhMuc', 'movies', 'theLoais', 'raps'));
    }

    /**
     * 📘 Chi tiết phim
     */
    public function show($slug)
    {
        $phim = Phim::where('slug', $slug)
            ->with(['theLoais', 'danhMucs'])
            ->firstOrFail();

        return view('client.movies.show', compact('phim'));
    }
}
