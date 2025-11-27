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

        $cacheKey = "dash:v5:{$from->timestamp}:{$to->timestamp}:" . ($rapId ?: 'all');

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
            $topMovies = DB::table('chi_tiet_ve as c')
                ->join('don_dat_ve as d', 'd.id', '=', 'c.don_dat_ve_id')
                ->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                ->join('phim as p', 'p.id', '=', 's.phim_id')
                ->leftJoin('danh_muc as dm', 'dm.id', '=', 'p.danh_muc_id')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->whereIn('c.trang_thai', ['da_thanh_toan', 'da_su_dung'])
                ->selectRaw('p.id, p.tieu_de, dm.ten as the_loai, SUM(d.tong_tien) as revenue, COUNT(*) as tickets_sold, p.danh_gia as rating, p.anh_poster as poster_url')
                ->groupBy('p.id', 'p.tieu_de', 'dm.ten', 'p.danh_gia', 'p.anh_poster')
                ->orderByDesc('revenue')
                ->limit(10)
                ->get();

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
                
            // Total unique customers
            $totalCustomers = DB::table('don_dat_ve as d')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->distinct('d.nguoi_dung_id')
                ->count('d.nguoi_dung_id');
                
            // New customers (first order in period)
            $newCustomers = DB::table('don_dat_ve as d')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->whereNotExists(function ($query) use ($from) {
                    $query->select(DB::raw(1))
                          ->from('don_dat_ve as d2')
                          ->whereRaw('d2.nguoi_dung_id = d.nguoi_dung_id')
                          ->where('d2.trang_thai', 'da_thanh_toan')
                          ->where('d2.created_at', '<', $from);
                })
                ->distinct('d.nguoi_dung_id')
                ->count('d.nguoi_dung_id');

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

            // Daily tickets data for chart
            $dailyTickets = DB::table('don_dat_ve as d')
                ->join('chi_tiet_ve as c', 'c.don_dat_ve_id', '=', 'd.id')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->whereIn('c.trang_thai', ['da_thanh_toan', 'da_su_dung'])
                ->selectRaw("DATE(d.created_at) as date, COUNT(*) as tickets")
                ->groupBy('date')->orderBy('date')->get();

            // Daily revenue data for chart
            $dailyRevenue = DB::table('don_dat_ve as d')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->selectRaw("DATE(d.created_at) as date, SUM(d.tong_tien) as revenue")
                ->groupBy('date')->orderBy('date')->get();

            // Order status counts for pie chart
            $orderStatusCounts = DB::table('don_dat_ve as d')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->whereBetween('d.created_at', [$from, $to])
                ->selectRaw("d.trang_thai, COUNT(*) as count")
                ->groupBy('d.trang_thai')->get();

            // Customer growth data
            $customerGrowth = DB::table('don_dat_ve as d')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->selectRaw("DATE(d.created_at) as date, COUNT(DISTINCT d.nguoi_dung_id) as customers")
                ->groupBy('date')->orderBy('date')->get();

            // Recent orders
            $recentOrders = DB::table('don_dat_ve as d')
                ->join('nguoi_dung as u', 'u.id', '=', 'd.nguoi_dung_id')
                ->leftJoin('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                ->leftJoin('phim as p', 'p.id', '=', 's.phim_id')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->whereBetween('d.created_at', [$from, $to])
                ->selectRaw("d.id, d.ma_don, d.tong_tien as total_amount, d.trang_thai, d.created_at, u.ho_ten as customer_name, p.tieu_de as movie_title, COUNT(c.id) as ticket_count")
                ->leftJoin('chi_tiet_ve as c', 'c.don_dat_ve_id', '=', 'd.id')
                ->groupBy('d.id', 'd.ma_don', 'd.tong_tien', 'd.trang_thai', 'd.created_at', 'u.ho_ten', 'p.tieu_de')
                ->orderByDesc('d.created_at')
                ->limit(10)
                ->get()
                ->map(function ($order) {
                    $order->created_at = Carbon::parse($order->created_at);
                    $order->status_text = match($order->trang_thai) {
                        'cho_thanh_toan' => 'Chờ thanh toán',
                        'da_thanh_toan' => 'Đã thanh toán',
                        'da_huy' => 'Đã hủy',
                        default => 'Không xác định'
                    };
                    $order->status_color = match($order->trang_thai) {
                        'cho_thanh_toan' => 'warning',
                        'da_thanh_toan' => 'success',
                        'da_huy' => 'danger',
                        default => 'secondary'
                    };
                    return $order;
                });

            // Top combos
            $topCombos = DB::table('don_dat_ve_combo as dc')
                ->join('don_dat_ve as d', 'd.id', '=', 'dc.don_dat_ve_id')
                ->join('combo as c', 'c.id', '=', 'dc.combo_id')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->selectRaw('c.id, c.ten, SUM(dc.so_luong) as quantity_sold, SUM(dc.so_luong * dc.gia) as revenue')
                ->groupBy('c.id', 'c.ten')
                ->orderByDesc('revenue')
                ->limit(10)->get();

            // Top vouchers used
            $topVouchers = DB::table('don_dat_ve as d')
                ->join('ma_giam_gia as mg', 'mg.id', '=', 'd.ma_giam_gia_id')
                ->when($rapId, function ($q) use ($rapId) {
                    $q->join('suat_chieu as s', 's.id', '=', 'd.suat_chieu_id')
                      ->join('phong_chieu as pc', 'pc.id', '=', 's.phong_id')
                      ->where('pc.rap_id', $rapId);
                })
                ->where('d.trang_thai', 'da_thanh_toan')
                ->whereBetween('d.created_at', [$from, $to])
                ->whereNotNull('d.ma_giam_gia_id')
                ->selectRaw('mg.id, mg.ma as ten, COUNT(*) as usage_count')
                ->groupBy('mg.id', 'mg.ma')
                ->orderByDesc('usage_count')
                ->limit(10)->get();

            // Get cinemas for filter dropdown
            $cinemas = DB::table('rap')->select('id', 'ten')->get();

            return [
                'from' => $from, 'to' => $to, 'rapId' => $rapId,
                'cinemas' => $cinemas,
                'totalOrders'   => $totalOrders,
                'paidOrders'    => $paidOrders,
                'paymentRate'   => $totalOrders ? round($paidOrders / $totalOrders * 100, 2) : 0,
                'grossRevenue'  => (float) $grossRevenue,
                'ticketRevenue' => (float) $grossRevenue,
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
                'dailyTickets'  => $dailyTickets,
                'dailyRevenue'  => $dailyRevenue,
                'orderStatusCounts' => $orderStatusCounts,
                'customerGrowth' => $customerGrowth,
                'totalCustomers' => $totalCustomers,
                'newCustomers'  => $newCustomers,
                'recentOrders'  => $recentOrders,
                'topCombos'     => $topCombos,
                'topVouchers'   => $topVouchers,
            ];
        });

        return view('admin.dashboard.index', $data);
    }
}
