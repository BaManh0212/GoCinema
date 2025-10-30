<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->subDays(30)->toDateString()))->startOfDay();
        $to   = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();
        $rapId = $request->integer('rap_id'); // optional

        $cacheKey = "dash:v1:{$from->timestamp}:{$to->timestamp}:" . ($rapId ?: 'all');

        $data = Cache::remember($cacheKey, 300, function () use ($from, $to, $rapId) {
            // base cho DON_DAT_VE
            $baseOrders = DB::table('don_dat_ve as d')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->whereBetween('d.created_at', [$from, $to]);

            $totalOrders   = (clone $baseOrders)->count();
            $paidOrders    = (clone $baseOrders)->where('d.trang_thai', 'da_thanh_toan')->count();
            $grossRevenue  = (clone $baseOrders)->where('d.trang_thai', 'da_thanh_toan')->sum('d.tong_tien');

            // Vé bán ra (đã thanh toán)
            $ticketsSold = DB::table('chi_tiet_ve as c')
                ->join('don_dat_ve as d', 'd.id', '=', 'c.don_dat_ve_id')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->whereBetween('d.created_at', [$from, $to])
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereIn('c.trang_thai', ['da_thanh_toan', 'da_su_dung'])
                ->count();

            // Doanh thu theo giờ bắt đầu suất chiếu
            $revenueByHourRows = DB::table('don_dat_ve as d')
                ->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->selectRaw('HOUR(s.gio_bat_dau) as h, SUM(d.tong_tien) as revenue, COUNT(*) as orders')
                ->groupBy('h')->orderBy('h')->get();

            $hours = collect(range(0, 23));
            $byHourMap = $revenueByHourRows->keyBy('h');
            $hourLabels  = $hours->map(fn($h) => sprintf('%02d:00', $h));
            $hourRevenue = $hours->map(fn($h) => (float)($byHourMap[$h]->revenue ?? 0));
            $hourOrders  = $hours->map(fn($h) => (int)($byHourMap[$h]->orders ?? 0));

            // Top phim
            $topMovies = DB::table('don_dat_ve as d')
                ->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                ->join('phim as p', 'p.id', '=', 's.phim_id')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->selectRaw('p.id, p.tieu_de, SUM(d.tong_tien) as revenue, COUNT(*) as orders')
                ->groupBy('p.id', 'p.tieu_de')
                ->orderByDesc('revenue')
                ->limit(10)->get();

            // Top khách hàng
            $topCustomers = DB::table('don_dat_ve as d')
                ->join('nguoi_dung as u', 'u.id', '=', 'd.nguoi_dung_id')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->selectRaw('u.id, u.ho_ten, u.email, COUNT(*) as orders, SUM(d.tong_tien) as revenue')
                ->groupBy('u.id', 'u.ho_ten', 'u.email')
                ->orderByDesc('revenue')
                ->limit(10)->get();

            // Doanh thu theo rạp
            $revenueByCinema = DB::table('don_dat_ve as d')
                ->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->selectRaw('r.id, r.ten, SUM(d.tong_tien) as revenue, COUNT(*) as orders')
                ->groupBy('r.id', 'r.ten')
                ->orderByDesc('revenue')
                ->get();

            // Doanh thu combo / sản phẩm
            $comboRevenue = DB::table('don_dat_ve_combo as dc')
                ->join('don_dat_ve as d', 'd.id', '=', 'dc.don_dat_ve_id')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->selectRaw('COALESCE(SUM(dc.so_luong * dc.gia), 0) as revenue')->value('revenue');

            $productRevenue = DB::table('don_hang_san_pham as sp')
                ->join('don_dat_ve as d', 'd.id', '=', 'sp.don_dat_ve_id')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->selectRaw('COALESCE(SUM(sp.so_luong * sp.gia), 0) as revenue')->value('revenue');

            // Xu hướng 12 tháng
            $monthlyTrend = DB::table('don_dat_ve as d')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [
                    $from->copy()->subMonthsNoOverflow(11)->startOfMonth(),
                    $to->copy()->endOfMonth()
                ])
                ->selectRaw("DATE_FORMAT(d.created_at, '%Y-%m') as ym, SUM(d.tong_tien) as revenue, COUNT(*) as orders")
                ->groupBy('ym')->orderBy('ym')->get();

            return [
                'from' => $from, 'to' => $to, 'rapId' => $rapId,
                'totalOrders'   => $totalOrders,
                'paidOrders'    => $paidOrders,
                'paymentRate'   => $totalOrders ? round($paidOrders / $totalOrders * 100, 2) : 0,
                'grossRevenue'  => (float) $grossRevenue,
                'ticketsSold'   => (int) $ticketsSold,
                'comboRevenue'  => (float) $comboRevenue,
                'productRevenue'=> (float) $productRevenue,
                'hourLabels'    => $hourLabels,
                'hourRevenue'   => $hourRevenue,
                'hourOrders'    => $hourOrders,
                'topMovies'     => $topMovies,
                'topCustomers'  => $topCustomers,
                'revenueByCinema'=> $revenueByCinema,
                'monthlyTrend'  => $monthlyTrend,
            ];
        });

        return view('admin.dashboard.index', $data);
    }
}
