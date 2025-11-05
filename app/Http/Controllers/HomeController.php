<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phim;
use App\Models\Banner;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::now()->startOfDay();

        // Lấy phim nổi bật: đang chiếu, sắp xếp theo lượt xem
        $featured = Phim::where('trang_thai', 1)
            ->whereDate('ngay_cong_chieu', '<=', $today)
            ->where(function($q) use ($today) {
                $q->whereNull('ngay_ket_thuc')
                  ->orWhereDate('ngay_ket_thuc', '>=', $today);
            })
            ->orderByDesc('luot_xem')
            ->limit(4)
            ->get();

        // Phim đang chiếu: trang_thai = 1 và đang trong khoảng ngày chiếu
        $nowShowing = Phim::where('trang_thai', 1)
            ->whereDate('ngay_cong_chieu', '<=', $today)
            ->where(function($q) use ($today) {
                $q->whereNull('ngay_ket_thuc')
                  ->orWhereDate('ngay_ket_thuc', '>=', $today);
            })
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        // Phim sắp chiếu: trang_thai = 1 và ngày công chiếu trong tương lai
        $comingSoon = Phim::where('trang_thai', 1)
            ->whereDate('ngay_cong_chieu', '>', $today)
            ->orderBy('ngay_cong_chieu', 'asc')
            ->limit(4)
            ->get();

        // Lấy banner động
        $banners = Banner::where('is_active', 1)
            ->where(function($q){
                $q->whereNull('start_at')->orWhere('start_at', '<=', now());
            })
            ->where(function($q){
                $q->whereNull('end_at')->orWhere('end_at', '>=', now());
            })
            ->orderBy('display_order', 'asc')
            ->get();

        return view('client.home', compact('featured', 'nowShowing', 'comingSoon', 'banners'));
    }
}
