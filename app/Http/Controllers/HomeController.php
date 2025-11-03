<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phim;
use App\Models\Banner;

class HomeController extends Controller
{
    /**
     * Display the client home with featured movies and banners.
     */
    public function index(Request $request)
    {
        // Lấy phim nổi bật: đang chiếu, sắp xếp theo lượt xem
        $featured = Phim::dangChieu()
            ->orderByDesc('luot_xem')
            ->limit(8)
            ->get();

        // Lấy banner động từ bảng banners (ảnh hoặc video)
        $banners = Banner::where('is_active', 1)
            ->where(function($q){
                $q->whereNull('start_at')->orWhere('start_at', '<=', now());
            })
            ->where(function($q){
                $q->whereNull('end_at')->orWhere('end_at', '>=', now());
            })
            ->orderBy('display_order', 'asc')
            ->get();

        return view('client.home', compact('featured', 'banners'));
    }
}
