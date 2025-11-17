<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuatChieu;
use App\Models\Combo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\MaGiamGia; // <--- import model
use Carbon\Carbon; 
class BookingController extends Controller
{
    public function show(Request $request)
    {
        $suatChieu = SuatChieu::with('phong.soDoGhe')->findOrFail($request->suat_chieu_id);
        $phong = $suatChieu->phong;

        if (!$phong || !$phong->soDoGhe) {
            return back()->with('error', 'Phòng này chưa có sơ đồ ghế!');
        }

        // Lấy ma trận ghế từ JSON
        $matrix = json_decode($phong->soDoGhe->ma_tran, true) ?: [];

        // Tính giá ghế trực tiếp trên matrix
        foreach ($matrix as &$seat) {
            $seat['gia'] = match($seat['loai'] ?? 'thuong') {
                'vip' => $suatChieu->gia_ve * 1.5,
                'doi' => $suatChieu->gia_ve * 2,
                default => $suatChieu->gia_ve,
            };
        }

        // Ghế nào đã đặt thì lấy từ bảng ghe_suat_chieu
        $trangThaiGhe = DB::table('ghe_suat_chieu')
            ->where('suat_chieu_id', $suatChieu->id)
            ->pluck('trang_thai', 'ghe_id');

        $combos = Combo::all();

        return view('client.movies.booking', compact(
            'suatChieu','phong','matrix','trangThaiGhe','combos'
        ));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để đặt vé!');
        }

        $request->validate([
            'suat_chieu_id' => 'required|exists:suat_chieu,id',
            'ghe_ids' => 'nullable|array',
            'combo_quantities' => 'nullable|array',
        ]);

        if (empty($request->ghe_ids)) {
            return back()->with('error', 'Bạn phải chọn ít nhất 1 ghế!');
        }

        $suatChieu = SuatChieu::findOrFail($request->suat_chieu_id);
        $nguoiDungId = Auth::id();
        $gheIds = $request->ghe_ids;
        $comboQuantities = $request->combo_quantities ?? [];

        DB::beginTransaction();
        try {
            // Lấy ma trận để tính giá ghế
            $matrix = json_decode($suatChieu->phong->soDoGhe->ma_tran, true) ?: [];
            $tongTienGhe = 0;

            foreach ($matrix as $item) {
                if (in_array($item['id'] ?? null, $gheIds)) {
                    $gia = match($item['loai'] ?? 'thuong') {
                        'vip' => $suatChieu->gia_ve * 1.5,
                        'doi' => $suatChieu->gia_ve * 2,
                        default => $suatChieu->gia_ve,
                    };
                    $tongTienGhe += $gia;
                }
            }

            // Tính tiền combo
            $tongTienCombo = 0;
            foreach ($comboQuantities as $comboId => $qty) {
                if ($qty > 0) {
                    $combo = Combo::find($comboId);
                    if ($combo) $tongTienCombo += $combo->gia * $qty;
                }
            }

            $tongTien = $tongTienGhe + $tongTienCombo;

            // Tạo đơn đặt vé
            $donId = DB::table('don_dat_ve')->insertGetId([
                'ma_don' => Str::upper(Str::random(8)),
                'nguoi_dung_id' => $nguoiDungId,
                'suat_chieu_id' => $suatChieu->id,
                'tong_tien' => $tongTien,
                'trang_thai' => 'cho_thanh_toan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Lưu trạng thái ghế
            foreach ($gheIds as $gheId) {
                DB::table('ghe_suat_chieu')->updateOrInsert(
                    [
                        'suat_chieu_id' => $suatChieu->id,
                        'ghe_id' => $gheId
                    ],
                    [
                        'trang_thai' => 'da_dat',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }

            // Lưu combo
            foreach ($comboQuantities as $comboId => $qty) {
                if ($qty > 0) {
                    $combo = Combo::find($comboId);
                    if ($combo) {
                        DB::table('don_dat_ve_combo')->insert([
                            'don_dat_ve_id' => $donId,
                            'combo_id' => $combo->id,
                            'so_luong' => $qty,
                            'gia' => $combo->gia,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('booking.show', ['suat_chieu_id' => $suatChieu->id])
                ->with('success', 'Đặt vé thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi đặt vé: '.$e->getMessage());
        }
    }
public function check(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $coupon = MaGiamGia::where('ma', $request->code)
            ->where('kich_hoat', 1)
            ->where(function ($q) {
                $today = Carbon::today();
                $q->whereNull('ngay_bat_dau')->orWhere('ngay_bat_dau', '<=', $today);
                $q->whereNull('ngay_ket_thuc')->orWhere('ngay_ket_thuc', '>=', $today);
            })
            ->first();

        if (!$coupon) {
            return response()->json([
                'status' => false,
                'message' => 'Mã giảm giá không hợp lệ!'
            ]);
        }

        // Kiểm tra số lượng
        if ($coupon->so_luong <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Mã giảm giá đã hết lượt sử dụng!'
            ]);
        }

        // Kiểm tra giá trị đơn hàng tối thiểu
        if ($coupon->gia_tri_don_hang_toi_thieu && $request->subtotal < $coupon->gia_tri_don_hang_toi_thieu) {
            return response()->json([
                'status' => false,
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu để áp dụng mã!'
            ]);
        }

        // Tính giảm giá
        if ($coupon->loai === 'phan_tram') {
            $discount = $request->subtotal * ($coupon->gia_tri / 100);

            if ($coupon->giam_toi_da) {
                $discount = min($discount, $coupon->giam_toi_da);
            }
        } else {
            $discount = $coupon->gia_tri;
        }

        return response()->json([
            'status' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'discount' => $discount,
            'coupon_id' => $coupon->id
        ]);
    }

}
