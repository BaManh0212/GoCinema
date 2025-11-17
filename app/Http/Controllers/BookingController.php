<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SuatChieu;
use App\Models\Ghe;
use App\Models\GheSuatChieu;
use App\Models\DonDatVe;
use App\Models\ChiTietVe;
use App\Models\MaGiamGia;
use App\Models\Combo;
use App\Models\SanPham;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Hiển thị trang đặt vé cho suất chiếu
     */
    public function index(Request $request)
    {
        $suatChieuId = $request->query('suat_chieu_id');
        if (!$suatChieuId) {
            return redirect('/')->with('error', 'Vui lòng chọn suất chiếu.');
        }

        $suatChieu = SuatChieu::with(['phim', 'phong.rap'])->findOrFail($suatChieuId);

        // Kiểm tra suất chiếu có hoạt động không
        if ($suatChieu->trang_thai !== 'hoat_dong') {
            return redirect('/')->with('error', 'Suất chiếu này không khả dụng.');
        }

        // Kiểm tra thời gian chiếu
        $now = Carbon::now();
        if ($now->gte($suatChieu->gio_bat_dau)) {
            return redirect('/')->with('error', 'Suất chiếu đã bắt đầu hoặc đã kết thúc.');
        }

        // Lấy sơ đồ ghế
        $ghes = $suatChieu->phong->ghe()
            ->orderBy('hang')
            ->orderBy('cot')
            ->get()
            ->groupBy('hang');

        // Lấy trạng thái ghế theo suất chiếu
        $gheStatuses = GheSuatChieu::where('suat_chieu_id', $suatChieuId)
            ->pluck('trang_thai', 'ghe_id')
            ->toArray();

        // Lấy ghế đã đặt hoặc đã thanh toán hoặc đã check-in
        $gheDaDat = ChiTietVe::where('suat_chieu_id', $suatChieuId)
            ->whereIn('trang_thai', ['da_dat', 'da_thanh_toan', 'da_checkin'])
            ->pluck('ghe_id')
            ->toArray();

        // Lấy ghế giữ tạm (trong 10 phút)
        $giuTamIds = DB::table('ghe_giu_tam')
            ->where('suat_chieu_id', $suatChieuId)
            ->where('het_han', '>', Carbon::now())
            ->pluck('ghe_id')
            ->toArray();

        // Lấy combo và sản phẩm
        $combos = Combo::all();
        $sanPhams = SanPham::all();

        return view('client.booking.index', compact(
            'suatChieu',
            'ghes',
            'gheStatuses',
            'gheDaDat',
            'giuTamIds',
            'combos',
            'sanPhams'
        ));
    }

    /**
     * Giữ tạm ghế
     */
    public function holdSeats(Request $request)
    {
        $request->validate([
            'suat_chieu_id' => 'required|exists:suat_chieu,id',
            'ghe_ids' => 'required|array|min:1|max:2',
            'ghe_ids.*' => 'exists:ghe,id',
        ]);

        $suatChieuId = $request->suat_chieu_id;
        $gheIds = $request->ghe_ids;

        DB::beginTransaction();
        try {
            // Kiểm tra ghế có khả dụng không
            foreach ($gheIds as $gheId) {
                // Kiểm tra ghế đã đặt
                $daDat = ChiTietVe::where('suat_chieu_id', $suatChieuId)
                    ->where('ghe_id', $gheId)
                    ->whereIn('trang_thai', ['da_dat', 'da_thanh_toan', 'da_checkin'])
                    ->exists();

                if ($daDat) {
                    throw new \Exception('Ghế ' . Ghe::find($gheId)->so_ghe_ngoi . ' đã được đặt.');
                }

                // Kiểm tra ghế giữ tạm
                $giuTam = DB::table('ghe_giu_tam')
                    ->where('suat_chieu_id', $suatChieuId)
                    ->where('ghe_id', $gheId)
                    ->where('het_han', '>', Carbon::now())
                    ->exists();

                if ($giuTam) {
                    throw new \Exception('Ghế ' . Ghe::find($gheId)->so_ghe_ngoi . ' đang được giữ tạm.');
                }

                // Kiểm tra trạng thái ghế theo suất chiếu
                $gheStatus = GheSuatChieu::where('suat_chieu_id', $suatChieuId)
                    ->where('ghe_id', $gheId)
                    ->value('trang_thai');

                if ($gheStatus === 'bao_tri' || $gheStatus === 'vo_hieu_hoa') {
                    throw new \Exception('Ghế ' . Ghe::find($gheId)->so_ghe_ngoi . ' không khả dụng.');
                }
            }

            // Xóa ghế giữ tạm cũ của user này (nếu có)
            DB::table('ghe_giu_tam')
                ->where('nguoi_dung_id', auth()->id())
                ->delete();

            // Thêm ghế giữ tạm mới
            $holdData = [];
            foreach ($gheIds as $gheId) {
                $holdData[] = [
                    'suat_chieu_id' => $suatChieuId,
                    'ghe_id' => $gheId,
                    'nguoi_dung_id' => auth()->id(),
                    'het_han' => Carbon::now()->addMinutes(10),
                ];
            }

            DB::table('ghe_giu_tam')->insert($holdData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã giữ tạm ghế thành công.',
                'hold_expires' => Carbon::now()->addMinutes(10)->format('H:i:s'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Xử lý đặt vé
     */
    public function store(Request $request)
    {
        $request->validate([
            'suat_chieu_id' => 'required|exists:suat_chieu,id',
            'ghe_ids' => 'required|array|min:1|max:2',
            'ghe_ids.*' => 'exists:ghe,id',
            'combo_items' => 'nullable|array',
            'combo_items.*.combo_id' => 'exists:combo,id',
            'combo_items.*.so_luong' => 'integer|min:1',
            // Cho phép truyền mã giảm giá tự do (mã hệ thống hoặc mã voucher người dùng)
            'ma_giam_gia' => 'nullable|string',
            // Nếu là voucher người dùng (đổi điểm) thì truyền id để đánh dấu đã sử dụng
            'voucher_nd_id' => 'nullable|integer|exists:voucher_nguoi_dung,id',
        ]);

        // Kiểm tra đăng nhập
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để đặt vé.',
                'redirect' => route('login'),
            ]);
        }

        $user = auth()->user();
        $suatChieuId = $request->suat_chieu_id;
        $gheIds = $request->ghe_ids;
        $comboItems = $request->combo_items ?? [];
        $maGiamGia = $request->ma_giam_gia; // có thể là mã hệ thống hoặc mã VCxxxxxx
        $voucherNguoiDungId = $request->voucher_nd_id; // id voucher_nguoi_dung nếu có

        DB::beginTransaction();
        try {
            $suatChieu = SuatChieu::findOrFail($suatChieuId);

            // Kiểm tra thời gian
            if (Carbon::now()->gte($suatChieu->gio_bat_dau)) {
                throw new \Exception('Suất chiếu đã bắt đầu.');
            }

            // Kiểm tra ghế
            foreach ($gheIds as $gheId) {
                // Kiểm tra ghế đã đặt
                $daDat = ChiTietVe::where('suat_chieu_id', $suatChieuId)
                    ->where('ghe_id', $gheId)
                    ->whereIn('trang_thai', ['da_dat', 'da_thanh_toan', 'da_checkin'])
                    ->exists();

                if ($daDat) {
                    $ghe = Ghe::find($gheId);
                    throw new \Exception('Ghế ' . $ghe->so_ghe_ngoi . ' đã được đặt.');
                }

                // Kiểm tra ghế giữ tạm của user khác
                $giuTamKhac = DB::table('ghe_giu_tam')
                    ->where('suat_chieu_id', $suatChieuId)
                    ->where('ghe_id', $gheId)
                    ->where('nguoi_dung_id', '!=', auth()->id())
                    ->where('het_han', '>', Carbon::now())
                    ->exists();

                if ($giuTamKhac) {
                    $ghe = Ghe::find($gheId);
                    throw new \Exception('Ghế ' . $ghe->so_ghe_ngoi . ' đang được giữ tạm bởi người khác.');
                }
            }

            // Tính tổng tiền vé theo loại ghế
            $tongTienVe = 0;
            $ghePrices = [];
            foreach ($gheIds as $gheId) {
                $ghe = Ghe::find($gheId);
                $price = $suatChieu->gia_ve;
                if ($ghe->loai === 'vip') {
                    $price *= 1.5;
                } elseif ($ghe->loai === 'doi') {
                    $price *= 2;
                }
                $ghePrices[$gheId] = $price;
                $tongTienVe += $price;
            }

            $tongTienCombo = 0;

            // Tính tiền combo
            $donDatVeCombos = [];
            foreach ($comboItems as $item) {
                $combo = Combo::find($item['combo_id']);
                $soLuong = $item['so_luong'];
                $tongTienCombo += $combo->gia * $soLuong;

                $donDatVeCombos[] = [
                    'combo_id' => $combo->id,
                    'so_luong' => $soLuong,
                    'gia' => $combo->gia,
                ];
            }

            $tongTien = $tongTienVe + $tongTienCombo;

            // Áp dụng ưu đãi: ưu tiên voucher người dùng nếu có, nếu không thì dùng mã giảm giá hệ thống
            $maGiamGiaObj = null;
            if ($voucherNguoiDungId) {
                $voucherNguoiDung = \App\Models\VoucherNguoiDung::with('voucher')
                    ->where('id', $voucherNguoiDungId)
                    ->where('nguoi_dung_id', $user->id)
                    ->first();

                if ($voucherNguoiDung && $voucherNguoiDung->conSuDungDuoc()) {
                    $voucher = $voucherNguoiDung->voucher;
                    // Chỉ áp dụng cho vé
                    if ($voucher->ap_dung_cho === 've' || $voucher->ap_dung_cho === 'tat_ca') {
                        $giamGia = $voucher->loai === 'phan_tram'
                            ? $tongTien * (floatval($voucher->gia_tri) / 100)
                            : floatval($voucher->gia_tri);
                        if (isset($voucher->giam_toi_da) && $voucher->giam_toi_da > 0 && $giamGia > $voucher->giam_toi_da) {
                            $giamGia = floatval($voucher->giam_toi_da);
                        }
                        $tongTien -= $giamGia;
                        // Đánh dấu voucher người dùng đã sử dụng
                        $voucherNguoiDung->increment('so_lan_da_dung');
                        $voucherNguoiDung->trang_thai = 'da_su_dung';
                        $voucherNguoiDung->save();
                    }
                }
            } elseif ($maGiamGia) {
                $maGiamGiaObj = MaGiamGia::where('ma', $maGiamGia)
                    ->where('trang_thai', 'hoat_dong')
                    ->where('ngay_bat_dau', '<=', Carbon::now())
                    ->where('ngay_ket_thuc', '>=', Carbon::now())
                    ->first();

                if ($maGiamGiaObj) {
                    if ($maGiamGiaObj->loai === 'phan_tram') {
                        $giamGia = $tongTien * ($maGiamGiaObj->gia_tri / 100);
                        if ($maGiamGiaObj->giam_toi_da && $giamGia > $maGiamGiaObj->giam_toi_da) {
                            $giamGia = $maGiamGiaObj->giam_toi_da;
                        }
                    } else {
                        $giamGia = $maGiamGiaObj->gia_tri;
                    }
                    $tongTien -= $giamGia;
                }
            }

            // Tạo đơn đặt vé
            $donDatVe = DonDatVe::create([
                'ma_don' => 'DV' . time() . rand(100, 999),
                'nguoi_dung_id' => $user->id,
                'suat_chieu_id' => $suatChieuId,
                'ma_giam_gia_id' => $maGiamGiaObj ? $maGiamGiaObj->id : null,
                'tong_tien' => $tongTien,
                'trang_thai' => 'cho_thanh_toan',
            ]);

            // Tạo chi tiết vé
            foreach ($gheIds as $gheId) {
                $ghe = Ghe::find($gheId);
                ChiTietVe::create([
                    'don_dat_ve_id' => $donDatVe->id,
                    'suat_chieu_id' => $suatChieuId,
                    'ghe_id' => $gheId,
                    'gia' => $ghePrices[$gheId],
                    'loai_ghe' => $ghe->loai,
                    'trang_thai' => 'da_dat',
                ]);
            }

            // Tạo chi tiết combo nếu có
            if (!empty($donDatVeCombos)) {
                foreach ($donDatVeCombos as $comboData) {
                    DB::table('don_dat_ve_combo')->insert([
                        'don_dat_ve_id' => $donDatVe->id,
                        'combo_id' => $comboData['combo_id'],
                        'so_luong' => $comboData['so_luong'],
                        'gia' => $comboData['gia'],
                    ]);
                }
            }

            // Xóa ghế giữ tạm
            DB::table('ghe_giu_tam')
                ->where('nguoi_dung_id', auth()->id())
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt vé thành công!',
                'don_dat_ve_id' => $donDatVe->id,
                'redirect' => route('booking.payment', $donDatVe->id),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Trang thanh toán
     */
    public function payment($id)
    {
        $donDatVe = DonDatVe::with([
            'suatChieu.phim',
            'suatChieu.phong.rap',
            'chiTietVes.ghe',
            'maGiamGia'
        ])->where('id', $id)
          ->where('nguoi_dung_id', auth()->id())
          ->firstOrFail();

        // Lấy combo đã đặt
        $combos = DB::table('don_dat_ve_combo as ddvc')
            ->join('combo', 'combo.id', '=', 'ddvc.combo_id')
            ->where('ddvc.don_dat_ve_id', $id)
            ->select('combo.ten', 'ddvc.so_luong', 'ddvc.gia')
            ->get();

        return view('client.booking.payment', compact('donDatVe', 'combos'));
    }

    /**
     * Xử lý thanh toán
     */
    public function processPayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:momo,zalopay,bank,counter',
        ]);

        $donDatVe = DonDatVe::where('id', $id)
            ->where('nguoi_dung_id', auth()->id())
            ->where('trang_thai', 'cho_thanh_toan')
            ->firstOrFail();

        $paymentMethod = $request->payment_method;

        try {
            DB::beginTransaction();

            if ($paymentMethod === 'counter') {
                // Thanh toán tại quầy - không cập nhật trạng thái thanh toán
                // Ghế sẽ được giữ trong 10 phút
                $donDatVe->update([
                    'thoi_gian_dat' => Carbon::now(),
                    'phuong_thuc_thanh_toan' => 'counter',
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Đặt vé thành công! Vui lòng thanh toán tại quầy trong vòng 10 phút.',
                    'redirect' => route('booking.confirm', $donDatVe->id),
                ]);
            } else {
                // Thanh toán online
                // Giả lập xử lý thanh toán (thực tế sẽ tích hợp với cổng thanh toán)

                // Cập nhật trạng thái đơn hàng
                $donDatVe->update([
                    'trang_thai' => 'da_thanh_toan',
                    'thoi_gian_thanh_toan' => Carbon::now(),
                    'phuong_thuc_thanh_toan' => $paymentMethod,
                ]);

                // Cập nhật trạng thái chi tiết vé
                $donDatVe->chiTietVes()->update(['trang_thai' => 'da_thanh_toan']);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Thanh toán thành công!',
                    'redirect' => route('booking.confirm', $donDatVe->id),
                ]);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thanh toán: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Trang xác nhận đặt vé
     */
    public function confirm($id)
    {
        $donDatVe = DonDatVe::with([
            'suatChieu.phim',
            'suatChieu.phong.rap',
            'chiTietVes.ghe',
            'maGiamGia'
        ])->where('id', $id)
          ->where('nguoi_dung_id', auth()->id())
          ->firstOrFail();

        // Lấy combo đã đặt
        $combos = DB::table('don_dat_ve_combo as ddvc')
            ->join('combo', 'combo.id', '=', 'ddvc.combo_id')
            ->where('ddvc.don_dat_ve_id', $id)
            ->select('combo.ten', 'ddvc.so_luong', 'ddvc.gia')
            ->get();

        return view('client.booking.confirm', compact('donDatVe', 'combos'));
    }

    /**
     * Kiểm tra voucher
     */
    public function checkVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'suat_chieu_id' => 'required|exists:suat_chieu,id',
            // Cho phép áp dụng trước khi chọn ghế
            'ghe_ids' => 'nullable|array|max:2',
            'ghe_ids.*' => 'exists:ghe,id',
            'combo_items' => 'nullable|array',
            'combo_items.*.combo_id' => 'exists:combo,id',
            'combo_items.*.so_luong' => 'integer|min:1',
        ]);

        $code = $request->code;
        $suatChieuId = $request->suat_chieu_id;
        $gheIds = $request->ghe_ids ?? [];
        $comboItems = $request->combo_items ?? [];

        try {
            // Tính tổng tiền trước giảm giá: tính theo loại ghế giống store()
            $suatChieu = SuatChieu::findOrFail($suatChieuId);
            $tongTienVe = 0;
            foreach ($gheIds as $gheId) {
                $ghe = Ghe::find($gheId);
                $price = $suatChieu->gia_ve;
                if ($ghe && $ghe->loai === 'vip') {
                    $price *= 1.5;
                } elseif ($ghe && $ghe->loai === 'doi') {
                    $price *= 2;
                }
                $tongTienVe += $price;
            }

            $tongTienCombo = 0;
            foreach ($comboItems as $item) {
                $combo = Combo::find($item['combo_id']);
                $soLuong = $item['so_luong'];
                if ($combo && $soLuong > 0) {
                    $tongTienCombo += $combo->gia * $soLuong;
                }
            }

            $tongTien = $tongTienVe + $tongTienCombo;

            // Hỗ trợ 2 loại: mã giảm giá hệ thống hoặc voucher người dùng (VCxxxxxx)
            // Nếu mã bắt đầu bằng VC => hiểu là voucher người dùng theo id
            if (strtoupper(substr($code, 0, 2)) === 'VC') {
                $idPart = preg_replace('/[^0-9]/', '', $code);
                $voucherNguoiDung = \App\Models\VoucherNguoiDung::with('voucher')
                    ->where('id', $idPart)
                    ->where('nguoi_dung_id', auth()->id())
                    ->first();

                if (!$voucherNguoiDung || !$voucherNguoiDung->conSuDungDuoc()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Voucher không hợp lệ hoặc đã hết hạn/sử dụng.',
                    ]);
                }

                $voucher = $voucherNguoiDung->voucher;
                if (!in_array($voucher->ap_dung_cho, ['ve', 'tat_ca'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Voucher này không áp dụng cho đặt vé.',
                    ]);
                }

                $giamGia = $voucher->loai === 'phan_tram'
                    ? $tongTien * (floatval($voucher->gia_tri) / 100)
                    : floatval($voucher->gia_tri);
                if (isset($voucher->giam_toi_da) && $voucher->giam_toi_da > 0 && $giamGia > $voucher->giam_toi_da) {
                    $giamGia = floatval($voucher->giam_toi_da);
                }

                return response()->json([
                    'success' => true,
                    'discount' => $giamGia,
                    'voucher_nd_id' => $voucherNguoiDung->id,
                    'message' => 'Áp dụng voucher thành công.',
                ]);
            }

            // Ngược lại: mã giảm giá hệ thống
            $maGiamGia = MaGiamGia::where('ma', $code)
                ->where('trang_thai', 'hoat_dong')
                ->where('ngay_bat_dau', '<=', Carbon::now())
                ->where('ngay_ket_thuc', '>=', Carbon::now())
                ->first();

            if (!$maGiamGia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.',
                ]);
            }

            if ($maGiamGia->loai === 'phan_tram') {
                $giamGia = $tongTien * ($maGiamGia->gia_tri / 100);
                if ($maGiamGia->giam_toi_da && $giamGia > $maGiamGia->giam_toi_da) {
                    $giamGia = $maGiamGia->giam_toi_da;
                }
            } else {
                $giamGia = $maGiamGia->gia_tri;
            }

            return response()->json([
                'success' => true,
                'discount' => $giamGia,
                'message' => 'Áp dụng mã giảm giá thành công.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra mã giảm giá.',
            ]);
        }
    }

    /**
     * Hủy đặt vé
     */
    public function cancel($id)
    {
        $donDatVe = DonDatVe::where('id', $id)
            ->where('nguoi_dung_id', auth()->id())
            ->where('trang_thai', 'cho_thanh_toan')
            ->firstOrFail();

        // Kiểm tra thời gian hủy (trước 2 giờ chiếu)
        $suatChieu = $donDatVe->suatChieu;
        if (Carbon::now()->addHours(2)->gte($suatChieu->gio_bat_dau)) {
            return back()->with('error', 'Không thể hủy vé trong vòng 2 giờ trước khi chiếu.');
        }

        DB::beginTransaction();
        try {
            // Xóa chi tiết vé
            $donDatVe->chiTietVes()->delete();

            // Xóa combo
            DB::table('don_dat_ve_combo')->where('don_dat_ve_id', $id)->delete();

            // Xóa đơn
            $donDatVe->delete();

            DB::commit();

            return redirect('/')->with('success', 'Đã hủy đặt vé thành công.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra khi hủy vé.');
        }
    }
}
