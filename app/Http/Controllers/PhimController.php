<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\DanhMuc;
use App\Models\DanhGia;
use App\Models\Phim;
use App\Models\TheLoai;
use App\Models\Rap;
use App\Models\SuatChieu;
use Carbon\Carbon;

class PhimController extends Controller
{

    /**  Kiểm tra User đã check-in thành công cho phim này chưa? */
    private function userHasCheckedInForMovie(int $userId, int $phimId): bool
    {
        return DB::table('don_dat_ve as ddv')
            ->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
            ->where('ddv.nguoi_dung_id', $userId)
            ->where('sc.phim_id', $phimId)
            ->where('ddv.trang_thai', 'da_checkin')
            ->exists();
    }

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

        // Lịch chiếu 7 ngày tới, chỉ lấy trạng thái hoạt động
        $start = now();
        $end   = now()->addDays(7)->endOfDay();

        $suatChieus = SuatChieu::with(['phong.rap'])
            ->where('phim_id', $phim->id)
            ->where('trang_thai', 'hoat_dong')   // đổi nếu DB bạn lưu khác giá trị
            ->whereBetween('gio_bat_dau', [$start, $end])
            ->orderBy('gio_bat_dau')
            ->get();

        // Gom theo ngày (Y-m-d)
        $lichChieuTheoNgay = $suatChieus->groupBy(fn($s) => \Carbon\Carbon::parse($s->gio_bat_dau)->format('Y-m-d'));

        // Thống kê điểm
        $soDanhGia = DanhGia::where('phim_id', $phim->id)->count();
        $diemTB    = DanhGia::where('phim_id', $phim->id)->avg('so_sao') ?? 0;

        // Các ID danh mục của phim hiện tại
        $catIds = $phim->danhMucs->pluck('id')->all();

        // Lấy phim liên quan theo danh mục
        $relatedMovies = Phim::with('danhMucs')
            ->where('id', '!=', $phim->id)
            ->whereHas('danhMucs', function ($q) use ($catIds) {
                // Chú ý: bảng danh mục là 'danh_muc' (đúng theo code bạn đang dùng)
                $q->whereIn('danh_muc.id', $catIds);
            })
            ->orderByDesc('created_at')
            ->distinct()
            ->take(8)
            ->get();

        // ✅ Quyền đánh giá
        $eligible = false;
        if (auth()->check()) {
            $eligible = $this->userHasCheckedInForMovie(auth()->id(), $phim->id);
        }

        return view('client.movies.show', compact(
            'phim',
            'lichChieuTheoNgay',
            'soDanhGia',
            'diemTB',
            'relatedMovies',
            'eligible' // 👈 truyền xuống view
        ));

        return view('client.movies.show', compact('phim', 'lichChieuTheoNgay', 'soDanhGia', 'diemTB', 'relatedMovies'));
    }

    // GET /api/phim/{slug}/lich-chieu
    public function lichChieuJson(string $slug)
    {
        $phim = Phim::where('slug', $slug)->firstOrFail();
        $now = now();
        $end = now()->addDays(7)->endOfDay();

        $items = SuatChieu::with(['phong.rap'])
            ->where('phim_id', $phim->id)
            ->where('trang_thai', 'hoat_dong')
            ->whereBetween('gio_bat_dau', [$now, $end])
            ->orderBy('gio_bat_dau')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'gio_bat_dau' => $s->gio_bat_dau,
                    'gio_ket_thuc' => $s->gio_ket_thuc,
                    'gia_ve' => $s->gia_ve,
                    'rap' => $s->phong?->rap?->ten ?? null,
                    'phong' => $s->phong?->ten ?? null,
                ];
            });

        return response()->json([
            'phim_id' => $phim->id,
            'lich_chieu' => $items,
        ]);
    }

    // POST /phim/{slug}/danh-gia
    public function luuDanhGia(Request $request, string $slug)
    {
        $phim = Phim::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'so_sao'    => 'required|integer|min:1|max:5',
            'binh_luan' => 'nullable|string|max:2000',
        ]);

        $userId = auth()->id();
        if (!$userId) {
            return back()->with('error', 'Vui lòng đăng nhập để đánh giá.');
        }

        // ✅ Chặn server-side: chỉ cho đánh giá nếu đã check-in thành công
        if (!$this->userHasCheckedInForMovie($userId, $phim->id)) {
            return back()->with('error', 'Bạn chỉ có thể đánh giá sau khi đã mua vé và check-in thành công cho phim này.');
        }

        DanhGia::create([
            'phim_id'       => $phim->id,
            'nguoi_dung_id' => $userId,
            'so_sao'        => $data['so_sao'],
            'binh_luan'     => $data['binh_luan'] ?? null,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }
}
