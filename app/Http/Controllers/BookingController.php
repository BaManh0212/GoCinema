<?php

namespace App\Http\Controllers;

use App\Models\ChiTietVe;
use App\Models\DonDatVe;
use App\Models\Ghe;
use App\Models\GheGiuTam;
use App\Models\Phim;
use App\Models\SuatChieu;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function movies(): View
    {
        $movies = Phim::query()
            ->withCount('suatChieus')
            ->orderByDesc('ngay_cong_chieu')
            ->get();

        return view('movies.index', compact('movies'));
    }

    public function showMovie(Phim $phim): View
    {
        $showtimes = SuatChieu::query()
            ->with(['phim', 'phong.rap'])
            ->where('phim_id', $phim->id)
            ->orderBy('gio_bat_dau')
            ->get();

        return view('movies.show', compact('phim', 'showtimes'));
    }

    public function seats(SuatChieu $suatChieu): View
    {
        $seats = Ghe::query()
            ->where('phong_id', $suatChieu->phong_id)
            ->orderBy('hang')
            ->orderBy('cot')
            ->get();

        $heldSeatIds = GheGiuTam::query()
            ->where('suat_chieu_id', $suatChieu->id)
            ->where('het_han', '>', now())
            ->pluck('ghe_id');

        $soldOrPendingSeatIds = ChiTietVe::query()
            ->whereHas('donDatVe', function ($q) use ($suatChieu) {
                $q->where('suat_chieu_id', $suatChieu->id)
                    ->whereIn('trang_thai', ['cho_thanh_toan', 'da_thanh_toan']);
            })
            ->pluck('ghe_id');

        $unavailableSeatIds = $heldSeatIds->concat($soldOrPendingSeatIds)->unique()->values();

        return view('seats.select', [
            'suatChieu' => $suatChieu,
            'seats' => $seats,
            'unavailableSeatIds' => $unavailableSeatIds,
        ]);
    }

    public function hold(Request $request, SuatChieu $suatChieu): RedirectResponse
    {
        $validated = $request->validate([
            'ghe_ids' => ['required', 'array', 'min:1'],
            'ghe_ids.*' => ['integer'],
        ]);

        $currentUser = $request->user();
        if (!$currentUser) {
            return back()->withErrors('Vui lòng cung cấp người dùng (nguoi_dung_id).');
        }

        $seatIds = collect($validated['ghe_ids'])->unique()->values();

        // Ensure seats belong to the showtime's room
        $validSeatIds = Ghe::query()
            ->whereIn('id', $seatIds)
            ->where('phong_id', $suatChieu->phong_id)
            ->pluck('id');

        if ($validSeatIds->count() !== $seatIds->count()) {
            return back()->withErrors('Có ghế không hợp lệ cho suất chiếu này.');
        }

        try {
            DB::transaction(function () use ($suatChieu, $seatIds, $currentUser) {
                // Re-check availability
                $heldSeatIds = GheGiuTam::query()
                    ->where('suat_chieu_id', $suatChieu->id)
                    ->where('het_han', '>', now())
                    ->pluck('ghe_id');

                $soldOrPendingSeatIds = ChiTietVe::query()
                    ->whereHas('donDatVe', function ($q) use ($suatChieu) {
                        $q->where('suat_chieu_id', $suatChieu->id)
                            ->whereIn('trang_thai', ['cho_thanh_toan', 'da_thanh_toan']);
                    })
                    ->pluck('ghe_id');

                $blocked = $heldSeatIds->concat($soldOrPendingSeatIds)->unique();
                $conflicts = $seatIds->intersect($blocked);
                if ($conflicts->isNotEmpty()) {
                    abort(409, 'Một số ghế đã được giữ/chốt bởi người khác.');
                }

                foreach ($seatIds as $seatId) {
                    GheGiuTam::updateOrCreate(
                        [
                            'suat_chieu_id' => $suatChieu->id,
                            'ghe_id' => $seatId,
                        ],
                        [
                            'nguoi_dung_id' => $currentUser->id,
                            'het_han' => now()->addMinutes(10),
                        ]
                    );
                }
            });
        } catch (\Throwable $e) {
            return back()->withErrors('Không thể giữ ghế: ' . $e->getMessage());
        }

        return redirect()->route('checkout.show', ['suatChieu' => $suatChieu->id, 'nguoi_dung_id' => $currentUser->id])
            ->with('status', 'Đã giữ ghế trong 10 phút. Vui lòng thanh toán.');
    }

    public function checkout(Request $request, SuatChieu $suatChieu): View
    {
        $currentUser = $request->user();
        if (!$currentUser) {
            abort(401, 'Thiếu người dùng.');
        }

        $holds = GheGiuTam::query()
            ->where('suat_chieu_id', $suatChieu->id)
            ->where('nguoi_dung_id', $currentUser->id)
            ->where('het_han', '>', now())
            ->pluck('ghe_id');

        $seats = Ghe::query()->whereIn('id', $holds)->orderBy('hang')->orderBy('cot')->get();
        $subtotal = $seats->count() * (float) $suatChieu->gia_ve;

        return view('checkout.show', [
            'suatChieu' => $suatChieu,
            'seats' => $seats,
            'subtotal' => $subtotal,
        ]);
    }

    public function processCheckout(Request $request, SuatChieu $suatChieu): RedirectResponse
    {
        $currentUser = $request->user();
        if (!$currentUser) {
            abort(401, 'Thiếu người dùng.');
        }

        $holds = GheGiuTam::query()
            ->where('suat_chieu_id', $suatChieu->id)
            ->where('nguoi_dung_id', $currentUser->id)
            ->where('het_han', '>', now())
            ->pluck('ghe_id');

        if ($holds->isEmpty()) {
            return back()->withErrors('Chưa có ghế được giữ hoặc giữ ghế đã hết hạn.');
        }

        try {
            $order = DB::transaction(function () use ($suatChieu, $currentUser, $holds) {
                $maDon = 'OD' . strtoupper(Str::random(10));
                $tongTien = $holds->count() * (float) $suatChieu->gia_ve;

                $don = DonDatVe::create([
                    'ma_don' => $maDon,
                    'nguoi_dung_id' => $currentUser->id,
                    'suat_chieu_id' => $suatChieu->id,
                    'ma_giam_gia_id' => null,
                    'tong_tien' => $tongTien,
                    'trang_thai' => 'cho_thanh_toan',
                ]);

                $seatModels = Ghe::query()->whereIn('id', $holds)->get();
                foreach ($seatModels as $seat) {
                    ChiTietVe::create([
                        'don_dat_ve_id' => $don->id,
                        'ghe_id' => $seat->id,
                        'gia' => (float) $suatChieu->gia_ve,
                        'loai_ghe' => $seat->loai,
                        'trang_thai' => 'cho_thanh_toan',
                        'thoi_gian_su_dung' => null,
                    ]);
                }

                // Clear holds for these seats
                GheGiuTam::query()
                    ->where('suat_chieu_id', $suatChieu->id)
                    ->whereIn('ghe_id', $holds)
                    ->where('nguoi_dung_id', $currentUser->id)
                    ->delete();

                return $don;
            });
        } catch (\Throwable $e) {
            return back()->withErrors('Không thể tạo đơn đặt vé: ' . $e->getMessage());
        }

        return redirect()->route('orders.pay', ['donDatVe' => $order->id, 'nguoi_dung_id' => $currentUser->id]);
    }

    public function pay(DonDatVe $donDatVe): RedirectResponse
    {
        try {
            DB::transaction(function () use ($donDatVe) {
                $donDatVe->update(['trang_thai' => 'da_thanh_toan']);
                $donDatVe->chiTietVes()->update(['trang_thai' => 'da_thanh_toan']);
            });
        } catch (\Throwable $e) {
            return back()->withErrors('Thanh toán thất bại: ' . $e->getMessage());
        }

        return redirect()->route('orders.success', ['donDatVe' => $donDatVe->id]);
    }

    public function success(DonDatVe $donDatVe): View
    {
        $donDatVe->load(['suatChieu.phim', 'chiTietVes.ghe.phong.rap']);
        return view('orders.success', compact('donDatVe'));
    }
}
