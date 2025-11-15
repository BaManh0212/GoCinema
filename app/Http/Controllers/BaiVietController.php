<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BaiViet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BaiVietController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        $query = BaiViet::query()
            ->where('is_active', true)
            ->where(function($q) use ($today) {
                $q->whereNull('ngay_phat_hanh')
                  ->orWhere('ngay_phat_hanh', '<=', $today);
            })
            ->where(function($q) use ($today) {
                $q->whereNull('ngay_ket_thuc')
                  ->orWhere('ngay_ket_thuc', '>=', $today);
            });

        // Lọc theo loại bài viết nếu có
        if ($request->filled('loai')) {
            $query->where('loai', $request->loai);
        }

        $baiviets = $query->latest()->paginate(6)->withQueryString();

        return view('client.baiviet.index', compact('baiviets'));
    }

    public function show($slug)
    {
        $today = Carbon::today();

        $baiviet = BaiViet::where('slug', $slug)
            ->where('is_active', true)
            ->where(function($q) use ($today) {
                $q->whereNull('ngay_phat_hanh')
                  ->orWhere('ngay_phat_hanh', '<=', $today);
            })
            ->where(function($q) use ($today) {
                $q->whereNull('ngay_ket_thuc')
                  ->orWhere('ngay_ket_thuc', '>=', $today);
            })
            ->firstOrFail();

        $lienquan = BaiViet::where('loai', $baiviet->loai)
            ->where('id', '!=', $baiviet->id)
            ->where('is_active', true)
            ->where(function($q) use ($today) {
                $q->whereNull('ngay_phat_hanh')
                  ->orWhere('ngay_phat_hanh', '<=', $today);
            })
            ->where(function($q) use ($today) {
                $q->whereNull('ngay_ket_thuc')
                  ->orWhere('ngay_ket_thuc', '>=', $today);
            })
            ->take(3)
            ->get();

        return view('client.baiviet.show', compact('baiviet', 'lienquan'));
    }
}
