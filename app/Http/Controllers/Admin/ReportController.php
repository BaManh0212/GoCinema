<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /** ====================== Helpers ====================== */

    protected function range(Request $request): array
    {
        $from = $request->input('from');
        $to   = $request->input('to');

        try {
            $from = $from ? Carbon::parse($from)->startOfDay() : now()->subDays(29)->startOfDay();
        } catch (\Throwable $e) {
            $from = now()->subDays(29)->startOfDay();
        }

        try {
            $to = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();
        } catch (\Throwable $e) {
            $to = now()->endOfDay();
        }

        return [$from, $to];
    }

    protected function wantsJson(Request $request): bool
    {
        return $request->ajax() || $request->wantsJson() || $request->get('format') === 'json';
    }

    protected function paidOrderStatus(): string
    {
        return 'da_thanh_toan';
    }

    protected function paidTicketStatuses(): array
    {
        return ['da_thanh_toan', 'da_su_dung'];
    }


    protected function viewOrJson(Request $request, string $view, array $data)
    {
        // Luôn truyền danh sách rạp cho filter nếu chưa có
        if (!array_key_exists('cinemas', $data)) {
            $data['cinemas'] = $this->cinemasList();
        }

        // Chỉ trả JSON khi thật sự yêu cầu bằng query `?format=json`
        if ($request->get('format') === 'json') {
            return response()->json($data);
        }

        // Nếu view không tồn tại -> báo lỗi rõ ràng để dễ dò
        if (!view()->exists($view)) {
            abort(500, "View '{$view}' not found. Hãy kiểm tra đường dẫn resources/views/" . str_replace('.', '/', $view) . ".blade.php");
        }

        return view($view, $data);
    }

    protected function cinemasList()
    {
        return DB::table('rap')->select('id', 'ten')->orderBy('ten')->get();
    }

    /** ====================== Tổng quan doanh thu ====================== */

    public function revenueTotal(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        // Tổng doanh thu = tổng tiền đơn đã thanh toán
        $total = DB::table('don_dat_ve as ddv')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->sum('ddv.tong_tien');

        // Breakdown theo ngày từ các nguồn
        $tickets = DB::table('chi_tiet_ve as ctv')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'ctv.don_dat_ve_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->whereIn('ctv.trang_thai', $this->paidTicketStatuses())
            ->selectRaw('DATE(ddv.created_at) as ngay, SUM(ctv.gia) as revenue')
            ->groupBy('ngay')
            ->pluck('revenue', 'ngay');

        $combos = DB::table('don_dat_ve_combo as dvc')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'dvc.don_dat_ve_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->selectRaw('DATE(ddv.created_at) as ngay, SUM(dvc.gia * dvc.so_luong) as revenue')
            ->groupBy('ngay')
            ->pluck('revenue', 'ngay');

        $products = DB::table('don_hang_san_pham as dhsp')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'dhsp.don_dat_ve_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->selectRaw('DATE(ddv.created_at) as ngay, SUM(dhsp.gia * dhsp.so_luong) as revenue')
            ->groupBy('ngay')
            ->pluck('revenue', 'ngay');

        // Gộp theo ngày
        $days = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $days[] = [
                'ngay'     => $key,
                've'       => (float) ($tickets[$key] ?? 0),
                'combo'    => (float) ($combos[$key] ?? 0),
                'san_pham' => (float) ($products[$key] ?? 0),
                'tong'     => (float) (($tickets[$key] ?? 0) + ($combos[$key] ?? 0) + ($products[$key] ?? 0)),
            ];
        }

        $summary = [
            'from'       => $from->toDateTimeString(),
            'to'         => $to->toDateTimeString(),
            'tong_doanh_thu' => (float) $total,
            've'        => array_sum(array_column($days, 've')),
            'combo'     => array_sum(array_column($days, 'combo')),
            'san_pham'  => array_sum(array_column($days, 'san_pham')),
        ];

        return $this->viewOrJson($request, 'admin.reports.revenue_total', compact('summary', 'days'));
    }

    public function revenueTickets(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        // Theo phim (top)
        $byMovie = DB::table('chi_tiet_ve as ctv')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'ctv.don_dat_ve_id')
            ->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
            ->join('phim as p', 'p.id', '=', 'sc.phim_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->whereIn('ctv.trang_thai', $this->paidTicketStatuses())
            ->groupBy('p.id', 'p.tieu_de')
            ->selectRaw('p.id, p.tieu_de, COUNT(*) as so_ve, SUM(ctv.gia) as doanh_thu')
            ->orderByDesc('doanh_thu')
            ->limit(50)
            ->get();

        // Theo rạp
        $byCinema = DB::table('chi_tiet_ve as ctv')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'ctv.don_dat_ve_id')
            ->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
            ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
            ->join('rap as r', 'r.id', '=', 'pc.rap_id')
            ->when($rapId, fn($q) => $q->where('r.id', $rapId))
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->whereIn('ctv.trang_thai', $this->paidTicketStatuses())
            ->groupBy('r.id', 'r.ten')
            ->selectRaw('r.id, r.ten, COUNT(*) as so_ve, SUM(ctv.gia) as doanh_thu')
            ->orderByDesc('doanh_thu')
            ->get();

        // Theo ngày (chart)
        $byDay = DB::table('chi_tiet_ve as ctv')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'ctv.don_dat_ve_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->whereIn('ctv.trang_thai', $this->paidTicketStatuses())
            ->groupBy(DB::raw('DATE(ddv.created_at)'))
            ->selectRaw('DATE(ddv.created_at) as ngay, COUNT(*) as so_ve, SUM(ctv.gia) as doanh_thu')
            ->orderBy('ngay')
            ->get();

        $summary = [
            'tong_ve'  => (int) $byDay->sum('so_ve'),
            'doanh_thu' => (float) $byDay->sum('doanh_thu'),
            'from'     => $from->toDateTimeString(),
            'to'       => $to->toDateTimeString(),
        ];

        return $this->viewOrJson($request, 'admin.reports.revenue_tickets', compact('summary', 'byMovie', 'byCinema', 'byDay'));
    }

    public function revenueCombos(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        $rows = DB::table('don_dat_ve_combo as dvc')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'dvc.don_dat_ve_id')
            ->join('combo as c', 'c.id', '=', 'dvc.combo_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->groupBy('c.id', 'c.ten')
            ->selectRaw('c.id, c.ten, SUM(dvc.so_luong) as so_luong, SUM(dvc.gia * dvc.so_luong) as doanh_thu')
            ->orderByDesc('doanh_thu')
            ->get();

        $byDay = DB::table('don_dat_ve_combo as dvc')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'dvc.don_dat_ve_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->groupBy(DB::raw('DATE(ddv.created_at)'))
            ->selectRaw('DATE(ddv.created_at) as ngay, SUM(dvc.so_luong) as so_luong, SUM(dvc.gia * dvc.so_luong) as doanh_thu')
            ->orderBy('ngay')
            ->get();

        $summary = [
            'tong_so_luong' => (int) $rows->sum('so_luong'),
            'doanh_thu'     => (float) $rows->sum('doanh_thu'),
            'from'          => $from->toDateTimeString(),
            'to'            => $to->toDateTimeString(),
        ];

        return $this->viewOrJson($request, 'admin.reports.revenue_combos', compact('summary', 'rows', 'byDay'));
    }

    public function revenueProducts(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        $rows = DB::table('don_hang_san_pham as dhsp')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'dhsp.don_dat_ve_id')
            ->join('san_pham as sp', 'sp.id', '=', 'dhsp.san_pham_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->groupBy('sp.id', 'sp.ten')
            ->selectRaw('sp.id, sp.ten, SUM(dhsp.so_luong) as so_luong, SUM(dhsp.gia * dhsp.so_luong) as doanh_thu')
            ->orderByDesc('doanh_thu')
            ->get();

        $byDay = DB::table('don_hang_san_pham as dhsp')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'dhsp.don_dat_ve_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->groupBy(DB::raw('DATE(ddv.created_at)'))
            ->selectRaw('DATE(ddv.created_at) as ngay, SUM(dhsp.so_luong) as so_luong, SUM(dhsp.gia * dhsp.so_luong) as doanh_thu')
            ->orderBy('ngay')
            ->get();

        $summary = [
            'tong_so_luong' => (int) $rows->sum('so_luong'),
            'doanh_thu'     => (float) $rows->sum('doanh_thu'),
            'from'          => $from->toDateTimeString(),
            'to'            => $to->toDateTimeString(),
        ];

        return $this->viewOrJson($request, 'admin.reports.revenue_products', compact('summary', 'rows', 'byDay'));
    }

    /** ====================== Vé / Đơn / Thanh toán ====================== */

    public function tickets(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        $rows = DB::table('chi_tiet_ve as ctv')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'ctv.don_dat_ve_id')
            ->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
            ->join('phim as p', 'p.id', '=', 'sc.phim_id')
            ->leftJoin('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
            ->leftJoin('rap as r', 'r.id', '=', 'pc.rap_id')
            ->when($rapId, fn($q) => $q->where('r.id', $rapId))
            ->whereBetween('ddv.created_at', [$from, $to])
            ->selectRaw('
                ctv.id,
                p.tieu_de as phim,
                r.ten as rap,
                ctv.gia,
                ctv.loai_ghe,
                ctv.trang_thai,
                ddv.created_at as ngay_mua
            ')
            ->orderByDesc('ctv.id')
            ->limit(200)
            ->get();

        $summary = [
            'tong_ve'  => (int) $rows->count(),
            'da_thanh_toan' => (int) $rows->whereIn('trang_thai', $this->paidTicketStatuses())->count(),
            'da_huy'   => (int) $rows->where('trang_thai', 'da_huy')->count(),
        ];

        return $this->viewOrJson($request, 'admin.reports.tickets', compact('summary', 'rows', 'from', 'to'));
    }

    public function orders(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        $orders = DB::table('don_dat_ve as ddv')
            ->join('nguoi_dung as nd', 'nd.id', '=', 'ddv.nguoi_dung_id')
            ->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
            ->join('phim as p', 'p.id', '=', 'sc.phim_id')
            ->leftJoin('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
            ->leftJoin('rap as r', 'r.id', '=', 'pc.rap_id')
            ->when($rapId, fn($q) => $q->where('r.id', $rapId))
            ->whereBetween('ddv.created_at', [$from, $to])
            ->selectRaw('
                ddv.id, ddv.ma_don, ddv.tong_tien, ddv.trang_thai, ddv.created_at,
                nd.ho_ten as khach_hang,
                p.tieu_de as phim,
                r.ten as rap
            ')
            ->orderByDesc('ddv.id')
            ->limit(200)
            ->get();

        $total = (int) $orders->count();
        $paid  = (int) $orders->where('trang_thai', $this->paidOrderStatus())->count();
        $canceled = (int) $orders->where('trang_thai', 'da_huy')->count();

        $summary = [
            'tong_don' => $total,
            'da_thanh_toan' => $paid,
            'ti_le_thanh_toan' => $total > 0 ? round($paid * 100 / $total, 2) : 0,
            'da_huy' => $canceled,
            'doanh_thu' => (float) $orders->where('trang_thai', $this->paidOrderStatus())->sum('tong_tien'),
        ];

        return $this->viewOrJson($request, 'admin.reports.orders', compact('summary', 'orders', 'from', 'to'));
    }

    public function ordersCanceled(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        $orders = DB::table('don_dat_ve as ddv')
            ->join('nguoi_dung as nd', 'nd.id', '=', 'ddv.nguoi_dung_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('ddv.trang_thai', 'da_huy')
            ->whereBetween('ddv.created_at', [$from, $to])
            ->orderByDesc('ddv.id')
            ->select('ddv.*', 'nd.ho_ten as khach_hang')
            ->get();

        return $this->viewOrJson($request, 'admin.reports.orders_canceled', compact('orders', 'from', 'to'));
    }

    public function payments(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        $byMethod = DB::table('thanh_toan as tt')
            ->join('phuong_thuc_thanh_toan as pttt', 'pttt.id', '=', 'tt.phuong_thuc_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('don_dat_ve as ddv', 'ddv.id', '=', 'tt.don_dat_ve_id')
                    ->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->whereBetween('tt.created_at', [$from, $to])
            ->groupBy('pttt.id', 'pttt.ten')
            ->selectRaw('pttt.id, pttt.ten, COUNT(*) as so_giao_dich, SUM(tt.so_tien) as tong_tien')
            ->orderByDesc('tong_tien')
            ->get();

        $byStatus = DB::table('thanh_toan as tt')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('don_dat_ve as ddv', 'ddv.id', '=', 'tt.don_dat_ve_id')
                    ->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->whereBetween('tt.created_at', [$from, $to])
            ->groupBy('tt.trang_thai')
            ->selectRaw('tt.trang_thai, COUNT(*) as so_luong, SUM(tt.so_tien) as tong_tien')
            ->get();

        $summary = [
            'tong_giao_dich' => (int) $byStatus->sum('so_luong'),
            'thanh_cong'     => (int) ($byStatus->firstWhere('trang_thai', 'thanh_cong')->so_luong ?? 0),
            'ti_le_thanh_cong' => (float) ($byStatus->sum('so_luong') ? round(($byStatus->firstWhere('trang_thai', 'thanh_cong')->so_luong ?? 0) * 100 / $byStatus->sum('so_luong'), 2) : 0),
            'tong_tien_xu_ly' => (float) $byStatus->sum('tong_tien'),
        ];

        return $this->viewOrJson($request, 'admin.reports.payments', compact('summary', 'byMethod', 'byStatus', 'from', 'to'));
    }

    public function refunds(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        $rows = DB::table('thanh_toan as tt')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'tt.don_dat_ve_id')
            ->join('nguoi_dung as nd', 'nd.id', '=', 'ddv.nguoi_dung_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('tt.trang_thai', 'hoan_tien')
            ->whereBetween('tt.created_at', [$from, $to])
            ->orderByDesc('tt.id')
            ->selectRaw('tt.*, ddv.ma_don, nd.ho_ten as khach_hang')
            ->get();

        $summary = [
            'so_giao_dich' => (int) $rows->count(),
            'tong_tien'    => (float) $rows->sum('so_tien'),
        ];

        return $this->viewOrJson($request, 'admin.reports.refunds', compact('summary', 'rows', 'from', 'to'));
    }

    /** ====================== Movies / Customers / Cinemas ====================== */

    public function movies(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        $rows = DB::table('chi_tiet_ve as ctv')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'ctv.don_dat_ve_id')
            ->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
            ->join('phim as p', 'p.id', '=', 'sc.phim_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereIn('ctv.trang_thai', $this->paidTicketStatuses())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->groupBy('p.id', 'p.tieu_de')
            ->selectRaw('p.id, p.tieu_de, COUNT(*) as so_ve, SUM(ctv.gia) as doanh_thu')
            ->orderByDesc('doanh_thu')
            ->limit(100)
            ->get();

        return $this->viewOrJson($request, 'admin.reports.movies', compact('rows', 'from', 'to'));
    }

    public function movieDetail(Request $request, int $id)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        $info = DB::table('phim')->where('id', $id)->first();

        $byDay = DB::table('chi_tiet_ve as ctv')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'ctv.don_dat_ve_id')
            ->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('sc.phim_id', $id)
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereIn('ctv.trang_thai', $this->paidTicketStatuses())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->groupBy(DB::raw('DATE(ddv.created_at)'))
            ->selectRaw('DATE(ddv.created_at) as ngay, COUNT(*) as so_ve, SUM(ctv.gia) as doanh_thu')
            ->orderBy('ngay')
            ->get();

        $byShowtime = DB::table('chi_tiet_ve as ctv')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'ctv.don_dat_ve_id')
            ->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
            ->leftJoin('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
            ->leftJoin('rap as r', 'r.id', '=', 'pc.rap_id')
            ->when($rapId, fn($q) => $q->where('r.id', $rapId))
            ->where('sc.phim_id', $id)
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereIn('ctv.trang_thai', $this->paidTicketStatuses())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->groupBy('sc.id', 'sc.gio_bat_dau', 'sc.gio_ket_thuc', 'r.ten')
            ->selectRaw('sc.id, sc.gio_bat_dau, sc.gio_ket_thuc, r.ten as rap, COUNT(*) as so_ve, SUM(ctv.gia) as doanh_thu')
            ->orderByDesc('doanh_thu')
            ->get();

        $summary = [
            'phim'       => $info->tieu_de ?? 'N/A',
            'tong_ve'    => (int) $byDay->sum('so_ve'),
            'doanh_thu'  => (float) $byDay->sum('doanh_thu'),
        ];

        return $this->viewOrJson($request, 'admin.reports.movie_detail', compact('summary', 'byDay', 'byShowtime', 'from', 'to'));
    }

    public function customers(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        $rows = DB::table('don_dat_ve as ddv')
            ->join('nguoi_dung as nd', 'nd.id', '=', 'ddv.nguoi_dung_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
                    ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->whereBetween('ddv.created_at', [$from, $to])
            ->groupBy('nd.id', 'nd.ho_ten', 'nd.email')
            ->selectRaw('
                nd.id, nd.ho_ten, nd.email,
                COUNT(ddv.id) as so_don,
                SUM(CASE WHEN ddv.trang_thai = "da_thanh_toan" THEN ddv.tong_tien ELSE 0 END) as chi_tieu
            ')
            ->orderByDesc('chi_tieu')
            ->limit(100)
            ->get();

        return $this->viewOrJson($request, 'admin.reports.customers', compact('rows', 'from', 'to'));
    }

    public function customerDetail(Request $request, int $id)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        $info = DB::table('nguoi_dung')->where('id', $id)->first();

        $orders = DB::table('don_dat_ve as ddv')
            ->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
            ->join('phim as p', 'p.id', '=', 'sc.phim_id')
            ->when($rapId, function ($q) use ($rapId) {
                $q->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
                    ->join('rap as r', 'r.id', '=', 'pc.rap_id')
                    ->where('r.id', $rapId);
            })
            ->where('ddv.nguoi_dung_id', $id)
            ->whereBetween('ddv.created_at', [$from, $to])
            ->orderByDesc('ddv.id')
            ->selectRaw('ddv.id, ddv.ma_don, ddv.tong_tien, ddv.trang_thai, ddv.created_at, p.tieu_de as phim')
            ->get();

        $summary = [
            'khach_hang' => $info->ho_ten ?? 'N/A',
            'email'      => $info->email ?? null,
            'tong_don'   => (int) $orders->count(),
            'da_thanh_toan' => (int) $orders->where('trang_thai', $this->paidOrderStatus())->count(),
            'chi_tieu'   => (float) $orders->where('trang_thai', $this->paidOrderStatus())->sum('tong_tien'),
        ];

        return $this->viewOrJson($request, 'admin.reports.customer_detail', compact('summary', 'orders', 'from', 'to'));
    }

    public function cinemas(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rapId = (int) $request->rap_id;

        $rows = DB::table('chi_tiet_ve as ctv')
            ->join('don_dat_ve as ddv', 'ddv.id', '=', 'ctv.don_dat_ve_id')
            ->join('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
            ->join('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
            ->join('rap as r', 'r.id', '=', 'pc.rap_id')
            ->when($rapId, fn($q) => $q->where('r.id', $rapId))
            ->where('ddv.trang_thai', $this->paidOrderStatus())
            ->whereIn('ctv.trang_thai', $this->paidTicketStatuses())
            ->whereBetween('ddv.created_at', [$from, $to])
            ->groupBy('r.id', 'r.ten')
            ->selectRaw('r.id, r.ten, COUNT(*) as so_ve, SUM(ctv.gia) as doanh_thu')
            ->orderByDesc('doanh_thu')
            ->get();

        return $this->viewOrJson($request, 'admin.reports.cinemas', compact('rows', 'from', 'to'));
    }

    /** ====================== Order detail ====================== */

    public function orderDetail(Request $request, int $id)
    {
        $order = DB::table('don_dat_ve as ddv')
            ->leftJoin('nguoi_dung as nd', 'nd.id', '=', 'ddv.nguoi_dung_id')
            ->leftJoin('suat_chieu as sc', 'sc.id', '=', 'ddv.suat_chieu_id')
            ->leftJoin('phim as p', 'p.id', '=', 'sc.phim_id')
            ->leftJoin('phong_chieu as pc', 'pc.id', '=', 'sc.phong_id')
            ->leftJoin('rap as r', 'r.id', '=', 'pc.rap_id')
            ->where('ddv.id', $id)
            ->selectRaw('ddv.*, nd.ho_ten as khach_hang, nd.email, p.tieu_de as phim, r.ten as rap')
            ->first();

        if (!$order) {
            abort(404);
        }

        $tickets = DB::table('chi_tiet_ve as ctv')
            ->leftJoin('ghe as g', 'g.id', '=', 'ctv.ghe_id')
            ->where('ctv.don_dat_ve_id', $id)
            ->selectRaw('ctv.*, g.hang, g.cot')
            ->get();

        $combos = DB::table('don_dat_ve_combo as dvc')
            ->join('combo as c', 'c.id', '=', 'dvc.combo_id')
            ->where('dvc.don_dat_ve_id', $id)
            ->selectRaw('c.ten, dvc.so_luong, dvc.gia, (dvc.so_luong * dvc.gia) as thanh_tien')
            ->get();

        $products = DB::table('don_hang_san_pham as dhsp')
            ->join('san_pham as sp', 'sp.id', '=', 'dhsp.san_pham_id')
            ->where('dhsp.don_dat_ve_id', $id)
            ->selectRaw('sp.ten, dhsp.so_luong, dhsp.gia, (dhsp.so_luong * dhsp.gia) as thanh_tien')
            ->get();

        $payments = DB::table('thanh_toan as tt')
            ->leftJoin('phuong_thuc_thanh_toan as pttt', 'pttt.id', '=', 'tt.phuong_thuc_id')
            ->where('tt.don_dat_ve_id', $id)
            ->selectRaw('tt.*, pttt.ten as phuong_thuc')
            ->orderBy('tt.id', 'desc')
            ->get();

        $summary = [
            'tong_ve'     => (int) $tickets->count(),
            'tien_ve'     => (float) $tickets->sum('gia'),
            'tien_combo'  => (float) $combos->sum('thanh_tien'),
            'tien_sp'     => (float) $products->sum('thanh_tien'),
            'da_thanh_toan' => (float) $payments->where('trang_thai', 'thanh_cong')->sum('so_tien'),
        ];

        return $this->viewOrJson($request, 'admin.reports.order_detail', compact(
            'order',
            'tickets',
            'combos',
            'products',
            'payments',
            'summary'
        ));
    }
}
