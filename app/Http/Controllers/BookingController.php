<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Mail\BookingConfirmation;
use App\Models\SuatChieu;
use App\Models\Ghe;
use App\Models\GheSuatChieu;
use App\Models\DonDatVe;
use App\Models\ChiTietVe;
use App\Models\MaGiamGia;
use App\Models\Combo;
use App\Models\SanPham;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


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

        // Dọn dẹp ghế giữ tạm đã hết hạn và đơn hàng chưa thanh toán quá 10 phút
        $this->cleanupExpiredBookings();

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

        // ✅ YÊU CẦU: Reload/F5 trang -> Xóa ghế giữ tạm (bắt chọn lại)
        // Chỉ xóa ghế giữ tạm (chưa tạo đơn hàng), không ảnh hưởng đơn chờ thanh toán
        if (auth()->check()) {
            DB::table('ghe_giu_tam')
                ->where('suat_chieu_id', $suatChieuId)
                ->where('nguoi_dung_id', auth()->id())
                ->delete();
        }

        // Lấy sơ đồ ghế
        $ghes = $suatChieu->phong->ghes()
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

        // Lấy ghế đang chờ thanh toán
        $gheChoThanhToan = ChiTietVe::where('suat_chieu_id', $suatChieuId)
            ->where('trang_thai', 'cho_thanh_toan')
            ->pluck('ghe_id')
            ->toArray();

        // Lấy ghế giữ tạm (trong 10 phút) - tất cả ghế
        // Bao gồm cả ghế trong đơn chờ thanh toán (dù có thể đã hết hạn trong ghe_giu_tam)
        $giuTamIds = DB::table('ghe_giu_tam')
            ->where('suat_chieu_id', $suatChieuId)
            ->where('het_han', '>', Carbon::now())
            ->pluck('ghe_id')
            ->toArray();
        
        // Thêm ghế đang chờ thanh toán vào danh sách giữ tạm (để đảm bảo hiển thị đúng)
        // Ghế đang chờ thanh toán cũng cần được hiển thị là giữ tạm
        $giuTamIds = array_unique(array_merge($giuTamIds, $gheChoThanhToan));

        // Lấy ghế giữ tạm của user hiện tại (để restore state khi load trang)
        $myHeldSeats = [];
        if (auth()->check()) {
            // Ghế đang giữ tạm
            $heldSeats = DB::table('ghe_giu_tam')
                ->where('suat_chieu_id', $suatChieuId)
                ->where('nguoi_dung_id', auth()->id())
                ->where('het_han', '>', Carbon::now())
                ->pluck('ghe_id')
                ->toArray();

            // Ghế trong đơn chờ thanh toán của user (để đảm bảo hiển thị đúng khi load lại trang)
            $pendingOrderSeats = ChiTietVe::where('suat_chieu_id', $suatChieuId)
                ->whereHas('donDatVe', function($query) {
                    $query->where('nguoi_dung_id', auth()->id())
                          ->where('trang_thai', 'cho_thanh_toan');
                })
                ->pluck('ghe_id')
                ->toArray();

            $myHeldSeats = array_unique(array_merge($heldSeats, $pendingOrderSeats));
            
            // ✅ Fix: Loại bỏ các ghế đã đặt thành công khỏi danh sách giữ tạm
            // Điều này đảm bảo ghế hiển thị màu đỏ (đã đặt) thay vì màu xanh (đang chọn)
            // ngay cả khi bản ghi ghe_giu_tam chưa kịp xóa
            $myHeldSeats = array_diff($myHeldSeats, $gheDaDat);
        }

        // Lấy combo và sản phẩm
        $combos = Combo::all();
        $sanPhams = SanPham::all();

        $availableVouchers = MaGiamGia::where('kich_hoat', true)
    ->where(function($query) {
        $query->whereNull('ngay_bat_dau')
              ->orWhere('ngay_bat_dau', '<=', now());
    })
    ->where(function($query) {
        $query->whereNull('ngay_ket_thuc')
              ->orWhere('ngay_ket_thuc', '>=', now());
    })
    ->where('so_luong', '>', 0)
    ->whereIn('ap_dung_cho', ['ve', 'tat_ca'])
    ->get();

        return view('client.booking.index', compact(
            'suatChieu',
            'ghes',
            'gheStatuses',
            'gheDaDat',
            'gheChoThanhToan',
            'giuTamIds',
            'myHeldSeats',
            'combos',
            'sanPhams',
            'availableVouchers'
        ));
    }

    /**
     * Thả ghế giữ tạm
     */
    public function releaseSeats(Request $request)
    {
        $request->validate([
            'suat_chieu_id' => 'required|exists:suat_chieu,id',
            'ghe_ids' => 'nullable|array|max:8', // Cho phép mảng rỗng khi không còn ghế nào
            'ghe_ids.*' => 'exists:ghe,id',
        ]);

        $suatChieuId = $request->suat_chieu_id;
        $gheIds = $request->ghe_ids ?? [];

        DB::beginTransaction();
        try {
            // Xóa ghế giữ tạm của user này
            $query = DB::table('ghe_giu_tam')
                ->where('nguoi_dung_id', auth()->id())
                ->where('suat_chieu_id', $suatChieuId);
            
            // Nếu có danh sách ghế cụ thể, chỉ xóa những ghế đó
            if (!empty($gheIds)) {
                $query->whereIn('ghe_id', $gheIds);
            }
            // Nếu không có danh sách ghế, xóa tất cả ghế giữ tạm của user cho suất chiếu này
            
            $query->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã thả ghế giữ tạm thành công.',
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
     * Giữ tạm ghế
     */
    public function holdSeats(Request $request)
    {
        $request->validate([
            'suat_chieu_id' => 'required|exists:suat_chieu,id',
            'ghe_ids' => 'required|array|min:1|max:8',
            'ghe_ids.*' => 'exists:ghe,id',
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để giữ ghế.',
            ]);
        }

        $suatChieuId = $request->suat_chieu_id;
        $gheIds = $request->ghe_ids;

        DB::beginTransaction();
        try {
            // Kiểm tra ghế có khả dụng không
            foreach ($gheIds as $gheId) {
                // Khóa hàng ghế để tuần tự hóa thao tác
                DB::table('ghe')->where('id', $gheId)->lockForUpdate()->first();
                // Kiểm tra ghế đã đặt
                $daDat = ChiTietVe::where('suat_chieu_id', $suatChieuId)
                    ->where('ghe_id', $gheId)
                    ->whereIn('trang_thai', ['da_dat', 'da_thanh_toan', 'da_checkin', 'cho_thanh_toan'])
                    ->exists();

                if ($daDat) {
                    throw new \Exception('Ghế ' . Ghe::find($gheId)->so_ghe_ngoi . ' đã được đặt.');
                }

                // Kiểm tra ghế giữ tạm (bỏ qua nếu đang được chính user này giữ)
                $giuTam = DB::table('ghe_giu_tam')
                    ->where('suat_chieu_id', $suatChieuId)
                    ->where('ghe_id', $gheId)
                    ->where('het_han', '>', Carbon::now())
                    ->where('nguoi_dung_id', '!=', auth()->id()) // Bỏ qua ghế đang được chính user này giữ
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

            // Xóa ghế giữ tạm cũ của user này cho suất chiếu này (nếu có)
            DB::table('ghe_giu_tam')
                ->where('nguoi_dung_id', auth()->id())
                ->where('suat_chieu_id', $suatChieuId)
                ->delete();

            // Thêm ghế giữ tạm mới (bắt duplicate nếu có race)
            foreach ($gheIds as $gheId) {
                try {
                    DB::table('ghe_giu_tam')->insert([
                        'suat_chieu_id' => $suatChieuId,
                        'ghe_id' => $gheId,
                        'nguoi_dung_id' => auth()->id(),
                        'het_han' => Carbon::now()->addMinutes(11), // Tăng lên 11 phút để tránh hết hạn sớm do delay load trang
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // 23000: integrity constraint violation (duplicate key), MySQL 1062
                    if ($e->getCode() == 23000) {
                        $seat = Ghe::find($gheId);
                        throw new \Exception('Ghế ' . ($seat?->so_ghe_ngoi ?? ($seat?->hang . $seat?->cot)) . ' vừa được người khác chọn.');
                    }
                    throw $e;
                }
            }

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
        // Validate input data
        $request->validate([
            'suat_chieu_id' => 'required|exists:suat_chieu,id',
            'ghe_ids' => 'required|array|min:1|max:8',
            'ghe_ids.*' => 'exists:ghe,id',
            'combo_items' => 'nullable|array',
            'combo_items.*.combo_id' => 'exists:combo,id',
            'combo_items.*.so_luong' => 'integer|min:1',
            'ma_giam_gia' => 'nullable|string',
            'voucher_nd_id' => 'nullable|integer|exists:voucher_nguoi_dung,id',
        ]);

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

        // Kiểm tra số ghế tối đa cho tài khoản này
        $existingSeats = ChiTietVe::where('suat_chieu_id', $suatChieuId)
            ->whereHas('donDatVe', function($query) {
                $query->where('nguoi_dung_id', auth()->id())
                      ->whereIn('trang_thai', ['cho_thanh_toan', 'da_thanh_toan', 'da_checkin']);
            })
            ->count();

        $totalSeats = $existingSeats + count($gheIds);
        if ($totalSeats > 8) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chỉ được đặt tối đa 8 ghế cho suất chiếu này.',
            ]);
        }


        $comboItems = $request->combo_items ?? [];
        $maGiamGia = $request->ma_giam_gia; // có thể là mã hệ thống hoặc mã VCxxxxxx
        $voucherNguoiDungId = $request->voucher_nd_id; // id voucher_nguoi_dung nếu có

        DB::beginTransaction();
        try {
            $suatChieu = SuatChieu::findOrFail($suatChieuId);

            // Kiểm tra thời gian
            $now = Carbon::now();
            $thoiGianBatDau = Carbon::parse($suatChieu->gio_bat_dau);
            $thoiGianToiThieu = $thoiGianBatDau->copy()->subMinutes(10);
            
            if ($now->gte($thoiGianBatDau)) {
                throw new \Exception('Suất chiếu đã bắt đầu.');
            }
            
            if ($now->gte($thoiGianToiThieu)) {
                throw new \Exception('Không thể đặt vé trong vòng 10 phút trước khi chiếu. Vui lòng chọn suất chiếu khác.');
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

            // Tính tổng tiền vé theo loại ghế, ngày và khung giờ
            $tongTienVe = 0;
            $ghePrices = [];
            foreach ($gheIds as $gheId) {
                $ghe = Ghe::find($gheId);
                $price = $this->calculateSeatPrice($suatChieu, $ghe);
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
                    ->where('kich_hoat', true)
                    ->where(function($query) {
                        $query->whereNull('ngay_bat_dau')
                              ->orWhere('ngay_bat_dau', '<=', Carbon::now());
                    })
                    ->where(function($query) {
                        $query->whereNull('ngay_ket_thuc')
                              ->orWhere('ngay_ket_thuc', '>=', Carbon::now());
                    })
                    ->where('so_luong', '>', 0)
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
                    // Decrement quantity after successful application
                    $maGiamGiaObj->decrement('so_luong');
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

            // Tạo chi tiết vé với trạng thái chờ thanh toán (không phải đã đặt)
            foreach ($gheIds as $gheId) {
                $ghe = Ghe::find($gheId);
                if (!$ghe) {
                    throw new \Exception('Ghế không tồn tại.');
                }
                try {
                    ChiTietVe::create([
                        'don_dat_ve_id' => $donDatVe->id,
                        'suat_chieu_id' => $suatChieuId,
                        'ghe_id' => $gheId,
                        'gia' => $ghePrices[$gheId],
                        'loai_ghe' => $ghe->loai,
                        'trang_thai' => 'cho_thanh_toan', // Chờ thanh toán, không phải đã đặt
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->getCode() == 23000) {
                        throw new \Exception('Ghế ' . ($ghe->so_ghe_ngoi ?? ($ghe->hang . $ghe->cot)) . ' vừa được người khác đặt.');
                    }
                    throw $e;
                }
            }

            // Tạo chi tiết combo nếu có
            if (!empty($donDatVeCombos)) {
                foreach ($donDatVeCombos as $comboData) {
                    $combo = Combo::find($comboData['combo_id']);
                    if (!$combo) {
                        throw new \Exception('Combo không tồn tại.');
                    }
                    if ($combo->so_luong < $comboData['so_luong']) {
                        throw new \Exception("Combo '{$combo->ten}' không đủ số lượng (cần {$comboData['so_luong']}, còn {$combo->so_luong}).");
                    }
                    DB::table('don_dat_ve_combo')->insert([
                        'don_dat_ve_id' => $donDatVe->id,
                        'combo_id' => $comboData['combo_id'],
                        'so_luong' => $comboData['so_luong'],
                        'gia' => $comboData['gia'],
                    ]);
                    // Decrement combo quantity
                    $combo->decrement('so_luong', $comboData['so_luong']);
                }
            }

            // Tích điểm cho người dùng: 1 điểm cho mỗi 1000 VND
            $diemTichLuy = floor($tongTien / 1000);
            if ($diemTichLuy > 0) {
                $user->themDiem($diemTichLuy, 'Tích điểm từ đơn đặt vé ' . $donDatVe->ma_don);
            }

            // Tạo lại ghế giữ tạm với thời gian 10 phút từ lúc tạo đơn (để user có 10 phút thanh toán)
            // Xóa ghế giữ tạm cũ của user này cho suất chiếu này
            DB::table('ghe_giu_tam')
                ->where('nguoi_dung_id', auth()->id())
                ->where('suat_chieu_id', $suatChieuId)
                ->delete();
            
            // Tạo lại ghế giữ tạm với thời gian hết hạn là 11 phút từ bây giờ
            foreach ($gheIds as $gheId) {
                DB::table('ghe_giu_tam')->insert([
                    'suat_chieu_id' => $suatChieuId,
                    'ghe_id' => $gheId,
                    'nguoi_dung_id' => auth()->id(),
                    'het_han' => Carbon::now()->addMinutes(11),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt vé thành công! Vui lòng kiểm tra email để xác nhận đơn hàng.',
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
        // Dọn dẹp ghế giữ tạm đã hết hạn và đơn hàng chưa thanh toán quá 10 phút
        $this->cleanupExpiredBookings();

        $donDatVe = DonDatVe::with([
            'suatChieu.phim',
            'suatChieu.phong.rap',
            'chiTietVes.ghe',
            'maGiamGia'
        ])->where('id', $id)
          ->where('nguoi_dung_id', auth()->id())
          ->firstOrFail();

        // Kiểm tra nếu đơn hàng đã bị hủy tự động do hết thời gian thanh toán
        if ($donDatVe->trang_thai === 'da_huy') {
            return redirect()->route('booking.index', ['suat_chieu_id' => $donDatVe->suat_chieu_id])
                ->with('error', 'Đơn hàng của bạn đã hết thời gian thanh toán và đã được hủy tự động. Vui lòng đặt vé lại.');
        }

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
        Log::info('Process payment request', $request->all());
        $request->validate([
            'payment_method' => 'required|in:momo,vnpay',
        ]);

        $donDatVe = DonDatVe::where('id', $id)
            ->where('nguoi_dung_id', auth()->id())
            ->where('trang_thai', 'cho_thanh_toan')
            ->first();

        if (!$donDatVe) {
            Log::warning('Booking not found or not accessible', [
                'booking_id' => $id,
                'user_id' => auth()->id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Đơn đặt vé không tồn tại hoặc đã được xử lý.',
                'redirect' => route('home')
            ], 404);
        }

        $paymentMethod = $request->payment_method;

        //===== Xử lý thanh toán MoMo ======//
        if ($paymentMethod === 'momo') {
            $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

            $partnerCode = env('MOMO_PARTNER_CODE', 'MOMOBKUN20180529');
            $accessKey = env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j');
            $secretKey = env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');

            $orderId = 'BOOKING_' . $donDatVe->id . '_' . time();
            $amount = (int) round($donDatVe->tong_tien); // MoMo yêu cầu integer
            $orderInfo = 'Thanh toán đơn ' . $donDatVe->ma_don;
            $redirectUrl = route('booking.momo-return'); // ✅ FIX: sử dụng momo-return route
            $ipnUrl = route('booking.momo-callback'); // ✅ callback URL
            $requestId = (string) time();
            $requestType = 'payWithATM';
            $extraData = '';

            $rawHash = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$ipnUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$partnerCode}&redirectUrl={$redirectUrl}&requestId={$requestId}&requestType={$requestType}";
            $signature = hash_hmac('sha256', $rawHash, $secretKey);

            $data = [
                'partnerCode' => $partnerCode,
                'accessKey'   => $accessKey,
                'requestId'   => $requestId,
                'amount'      => (string)$amount,
                'orderId'     => $orderId,
                'orderInfo'   => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl'      => $ipnUrl,
                'lang'        => 'vi',
                'extraData'   => $extraData,
                'requestType' => $requestType,
                'signature'   => $signature,
            ];

            Log::info('MoMo request', ['orderId' => $orderId, 'amount' => $amount, 'ipnUrl' => $ipnUrl]);

            try {
                $response = Http::timeout(10)->post($endpoint, $data)->json();
                Log::info('MoMo response', $response);
            } catch (\Exception $e) {
                Log::error('MoMo request error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi kết nối MoMo: ' . $e->getMessage(),
                ], 500);
            }

            if (!empty($response) && isset($response['resultCode']) && $response['resultCode'] == 0 && !empty($response['payUrl'])) {
                // ✅ Lưu phương thức thanh toán TRƯỚC khi redirect
                DB::beginTransaction();
                try {
                    $donDatVe->update([
                        'phuong_thuc_thanh_toan' => 'momo',
                    ]);
                    DB::commit();
                    
                    Log::info('MoMo payment initiated', ['order_id' => $donDatVe->id, 'orderId' => $orderId]);
                    
                    // Trả JSON để JS redirect hoặc form submit bình thường
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => true, 'payUrl' => $response['payUrl']]);
                    }
                    return redirect()->away($response['payUrl']);
                    
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error('Error updating MoMo phuong_thuc: ' . $e->getMessage());
                    return response()->json(['success' => false, 'message' => 'Lỗi cập nhật phương thức thanh toán'], 500);
                }
            }

            Log::warning('MoMo create failed', $response ?? []);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tạo thanh toán MoMo: ' . ($response['message'] ?? 'Không xác định'),
                'response' => $response,
            ], 500);
        }
     
        //===== Xử lý thanh toán VNPay ======//
if ($paymentMethod === 'vnpay') {

    $vnp_TmnCode = config('services.vnpay.tmn_code');
    $vnp_HashSecret = config('services.vnpay.hash_secret');
    $vnp_Url = config('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
    $vnp_Returnurl = route('booking.vnpay-return');

    // Validate VnPay configuration
    if (!$vnp_TmnCode || !$vnp_HashSecret) {
        Log::error('VnPay configuration missing', [
            'tmn_code' => $vnp_TmnCode ? 'set' : 'missing',
            'hash_secret' => $vnp_HashSecret ? 'set' : 'missing'
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Cấu hình VnPay chưa đầy đủ. Vui lòng liên hệ quản trị viên.'
        ], 500);
    }

    $vnp_TxnRef = 'BOOKING_' . $donDatVe->id . '_' . time();
    $vnp_OrderInfo = 'Thanh toan don ' . preg_replace('/[^A-Za-z0-9 ]/', '', $donDatVe->ma_don);
    $vnp_Amount = $donDatVe->tong_tien * 100; // VNPay nhân 100
    $vnp_Locale = 'vn';

    $inputData = [
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => $vnp_TmnCode,
        "vnp_Amount" => $vnp_Amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => request()->ip(),
        "vnp_Locale" => $vnp_Locale,
        "vnp_OrderInfo" => $vnp_OrderInfo,
        "vnp_OrderType" => "billpayment",
        "vnp_ReturnUrl" => $vnp_Returnurl,
        "vnp_TxnRef" => $vnp_TxnRef,
        "vnp_BankCode" => "NCB"
    ];

    // Sort & hash giống MoMo
    ksort($inputData);
    $query = http_build_query($inputData);
    $vnp_SecureHash = hash_hmac('sha512', urldecode($query), $vnp_HashSecret);

    // URL thanh toán
    $paymentUrl = $vnp_Url . '?' . $query . '&vnp_SecureHash=' . $vnp_SecureHash;

    Log::info('[VNPAY] Request tạo thanh toán', [
        'txnRef' => $vnp_TxnRef,
        'amount' => $vnp_Amount,
        'url' => $paymentUrl
    ]);

    try {
        DB::beginTransaction();

        // Lưu phương thức trước khi redirect
        $donDatVe->update([
            'phuong_thuc_thanh_toan' => 'vnpay',
            'ma_giao_dich' => $vnp_TxnRef
        ]);

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Lỗi lưu thông tin VNPay: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Lỗi tạo thanh toán VNPay']);
    }

    // Nếu là AJAX → trả JSON để FE redirect
    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'success' => true,
            'payUrl' => $paymentUrl
        ]);
    }

    // Không AJAX → redirect ngay
    return redirect()->away($paymentUrl);
}


        try {
            DB::beginTransaction();

            // Thanh toán online
            // Giả lập xử lý thanh toán (thực tế sẽ tích hợp với cổng thanh toán)

            // Cập nhật trạng thái đơn hàng
            $donDatVe->update([
                'trang_thai' => 'da_thanh_toan',
                'thoi_gian_thanh_toan' => Carbon::now(),
                'phuong_thuc_thanh_toan' => $paymentMethod,
            ]);

            // Cập nhật trạng thái chi tiết vé thành đã thanh toán
            $donDatVe->chiTietVes()->update(['trang_thai' => 'da_thanh_toan']);

                // Gửi email xác nhận thanh toán thành công
                try {
                    \Log::info('Bắt đầu gửi email xác nhận thanh toán cho đơn hàng: ' . $donDatVe->ma_don);
                    
                    // Gửi email đồng bộ
                    \Mail::to($donDatVe->nguoiDung->email)->sendNow(new \App\Mail\BookingConfirmation($donDatVe));
                    
                    \Log::info('✅ Đã gửi thành công email xác nhận thanh toán cho đơn hàng: ' . $donDatVe->ma_don);
                    \Log::info('Người nhận: ' . $donDatVe->nguoiDung->email);
                } catch (\Exception $e) {
                    \Log::error('❌ Lỗi khi gửi email xác nhận thanh toán đơn hàng ' . $donDatVe->ma_don . ': ' . $e->getMessage());
                    \Log::error('Chi tiết lỗi: ' . $e->getTraceAsString());
                    
                    // Thử gửi lại lần nữa nếu lỗi
                    try {
                        \Mail::to($donDatVe->nguoiDung->email)->sendNow(new \App\Mail\BookingConfirmation($donDatVe));
                        \Log::info('✅ Đã gửi lại thành công email xác nhận thanh toán sau lỗi: ' . $donDatVe->ma_don);
                    } catch (\Exception $retryException) {
                        \Log::error('❌ Lỗi khi gửi lại email xác nhận thanh toán: ' . $retryException->getMessage());
                    }
                }

                DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thanh toán thành công!',
                'redirect' => route('booking.confirm', $donDatVe->id),
            ]);

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
            'ghe_ids' => 'nullable|array|max:8',
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
                if (!$ghe) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ghế không hợp lệ.',
                    ]);
                }
                $price = $this->calculateSeatPrice($suatChieu, $ghe);
                $tongTienVe += $price;
            }

            $tongTienCombo = 0;
            foreach ($comboItems as $item) {
                $combo = Combo::find($item['combo_id']);
                if (!$combo) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Combo không hợp lệ.',
                    ]);
                }
                $soLuong = $item['so_luong'];
                if ($soLuong > 0) {
                    $tongTienCombo += $combo->gia * $soLuong;
                }
            }

            $tongTien = $tongTienVe + $tongTienCombo;

            // Hỗ trợ 2 loại: mã giảm giá hệ thống hoặc voucher người dùng (VCxxxxxx)
            // Nếu mã bắt đầu bằng VC => hiểu là voucher người dùng theo id
            if (strtoupper(substr($code, 0, 2)) === 'VC') {
                $idPart = preg_replace('/[^0-9]/', '', $code);
                if (!is_numeric($idPart)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mã voucher không hợp lệ.',
                    ]);
                }
                $voucherNguoiDung = \App\Models\VoucherNguoiDung::with('voucher')
                    ->where('id', $idPart)
                    ->where('nguoi_dung_id', auth()->id())
                    ->first();

                if (!$voucherNguoiDung) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Voucher không tồn tại.',
                    ]);
                }

                if (!$voucherNguoiDung->conSuDungDuoc()) {
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
                    'discount_type' => $voucher->loai,
                    'discount_value' => $voucher->gia_tri,
                    'max_discount' => $voucher->giam_toi_da,
                    'min_order_value' => $voucher->gia_tri_don_hang_toi_thieu,
                    'message' => 'Áp dụng voucher thành công.',
                ]);
            }

            // Ngược lại: mã giảm giá hệ thống
            try {
                $maGiamGia = MaGiamGia::where('ma', $code)
                    ->where('kich_hoat', 1)  // Sử dụng 1 thay vì true để đảm bảo tương thích
                    ->where(function($query) {
                        $query->whereNull('ngay_bat_dau')
                              ->orWhere('ngay_bat_dau', '<=', now());
                    })
                    ->where(function($query) {
                        $query->whereNull('ngay_ket_thuc')
                              ->orWhere('ngay_ket_thuc', '>=', now()->startOfDay());
                    })
                    ->where('so_luong', '>', 0)
                    ->first();
                
                // \Log::info('MaGiamGia query result:', [
                //     'code' => $code,
                //     'exists' => $maGiamGia ? 'yes' : 'no',
                //     'query' => DB::getQueryLog()
                // ]);
                    
            } catch (\Exception $e) {
                \Log::error('Lỗi khi kiểm tra mã giảm giá: ' . $e->getMessage(), [
                    'code' => $code,
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => config('app.debug') 
                        ? 'Lỗi hệ thống: ' . $e->getMessage() 
                        : 'Có lỗi xảy ra khi kiểm tra mã giảm giá. Vui lòng thử lại sau.'
                ]);
            }

            if (!$maGiamGia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.',
                ]);
            }

            // Kiểm tra mã giảm giá có áp dụng cho vé không
            if (!in_array($maGiamGia->ap_dung_cho, ['ve', 'tat_ca'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá này không áp dụng cho đặt vé.',
                ]);
            }

            // Kiểm tra giá trị đơn hàng tối thiểu
            if ($maGiamGia->gia_tri_don_hang_toi_thieu && $tongTien < $maGiamGia->gia_tri_don_hang_toi_thieu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng chưa đạt giá trị tối thiểu để áp dụng mã giảm giá này.',
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
                'discount_type' => $maGiamGia->loai,
                'discount_value' => $maGiamGia->gia_tri,
                'max_discount' => $maGiamGia->giam_toi_da,
                'min_order_value' => $maGiamGia->gia_tri_don_hang_toi_thieu,
                'message' => 'Áp dụng mã giảm giá thành công.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Check voucher error: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            
            // Return more detailed error in development, generic message in production
            $errorMessage = config('app.debug') 
                ? 'Lỗi: ' . $e->getMessage() . ' (Dòng ' . $e->getLine() . ')'
                : 'Có lỗi xảy ra khi kiểm tra mã giảm giá. Vui lòng thử lại sau.';
                
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'debug' => config('app.debug') ? [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    /**
     * Tính giá ghế dựa trên loại ghế, ngày và khung giờ
     */
    private function calculateSeatPrice($suatChieu, $ghe)
    {
        $basePrice = $suatChieu->gia_ve;

        // Tăng giá cuối tuần (thứ 7, Chủ nhật): +20%
        if ($suatChieu->gio_bat_dau->isWeekend()) {
            $basePrice *= 1.2;
        }

        // Tăng giá buổi tối từ 18h trở đi: +15%
        if ($suatChieu->gio_bat_dau->hour >= 18) {
            $basePrice *= 1.15;
        }

        // Áp dụng loại ghế
        if ($ghe->loai === 'vip') {
            $basePrice *= 1.5;
        } elseif ($ghe->loai === 'doi') {
            $basePrice *= 2;
        }

        return $basePrice;
    }

    /**
     * Kiểm tra ghế có liên tiếp không
     */
    private function areSeatsConsecutive($gheIds)
    {
        if (count($gheIds) <= 1) {
            return true;
        }

        $ghes = Ghe::whereIn('id', $gheIds)->get()->keyBy('id');

        // Nhóm ghế theo hàng
        $seatsByRow = [];
        foreach ($ghes as $ghe) {
            $seatsByRow[$ghe->hang][] = $ghe->cot;
        }

        // Nếu có nhiều hơn 1 hàng thì không liên tiếp
        if (count($seatsByRow) > 1) {
            return false;
        }

        // Lấy danh sách cột trong hàng duy nhất
        $columns = array_values($seatsByRow)[0];
        sort($columns);

        // Kiểm tra cột có liên tiếp không
        for ($i = 0; $i < count($columns) - 1; $i++) {
            if ($columns[$i + 1] - $columns[$i] !== 1) {
                return false;
            }
        }

        return true;
    }

    /**
     * Dọn dẹp ghế giữ tạm đã hết hạn và đơn hàng chưa thanh toán quá 10 phút
     */
    private function cleanupExpiredBookings()
    {
        $now = Carbon::now();
        $tenMinutesAgo = $now->copy()->subMinutes(10);

        DB::beginTransaction();
        try {
            // 1. Xóa ghế giữ tạm đã hết hạn (nhưng KHÔNG xóa ghế của đơn đang chờ thanh toán)
            // Lấy danh sách ghế đang trong đơn chờ thanh toán (kèm suat_chieu_id để đảm bảo đúng)
            $gheIdsInPendingOrders = DB::table('chi_tiet_ve')
                ->join('don_dat_ve', 'chi_tiet_ve.don_dat_ve_id', '=', 'don_dat_ve.id')
                ->where('don_dat_ve.trang_thai', 'cho_thanh_toan')
                ->where('chi_tiet_ve.trang_thai', 'cho_thanh_toan')
                ->select('chi_tiet_ve.ghe_id', 'chi_tiet_ve.suat_chieu_id')
                ->get()
                ->map(function($item) {
                    return $item->suat_chieu_id . '_' . $item->ghe_id; // Tạo key duy nhất
                })
                ->toArray();

            // Xóa ghế giữ tạm đã hết hạn, nhưng bỏ qua ghế đang trong đơn chờ thanh toán hoặc có nguoi_dung_id
            $expiredHoldsQuery = DB::table('ghe_giu_tam')
                ->where('het_han', '<', $now)
                ->whereNull('nguoi_dung_id'); // Chỉ xóa holds không có user (anonymous holds)

            $expiredHoldsData = $expiredHoldsQuery->get();

            if ($expiredHoldsData->isNotEmpty()) {
                // Lọc ra các ghế giữ tạm KHÔNG thuộc đơn chờ thanh toán
                $holdsToDelete = $expiredHoldsData->filter(function($hold) use ($gheIdsInPendingOrders) {
                    $key = $hold->suat_chieu_id . '_' . $hold->ghe_id;
                    return !in_array($key, $gheIdsInPendingOrders);
                });

                if ($holdsToDelete->isNotEmpty()) {
                    // Xóa các ghế giữ tạm đã hết hạn và không thuộc đơn chờ thanh toán
                    $gheIdsToDelete = $holdsToDelete->pluck('ghe_id')->toArray();
                    $suatChieuIdsToDelete = $holdsToDelete->pluck('suat_chieu_id')->toArray();

                    DB::table('ghe_giu_tam')
                        ->where('het_han', '<', $now)
                        ->whereNull('nguoi_dung_id')
                        ->whereIn('ghe_id', $gheIdsToDelete)
                        ->whereIn('suat_chieu_id', $suatChieuIdsToDelete)
                        ->delete();

                    Log::info('Cleaned up expired anonymous temporary holds', [
                        'count' => $holdsToDelete->count(),
                        'expired_holds' => $holdsToDelete->toArray()
                    ]);
                }
            }

            // 2. Hủy đơn hàng chưa thanh toán quá 10 phút
            $expiredOrders = DonDatVe::where('trang_thai', 'cho_thanh_toan')
                ->where('created_at', '<', $tenMinutesAgo)
                ->with(['chiTietVes', 'suatChieu'])
                ->get();

            foreach ($expiredOrders as $order) {
                // Lấy danh sách ghế để trả về trạng thái trống (trước khi xóa)
                $gheIds = $order->chiTietVes()->pluck('ghe_id')->toArray();
                $suatChieuId = $order->suat_chieu_id;
                $orderId = $order->id;
                $maDon = $order->ma_don;
                $userId = $order->nguoi_dung_id;
                $createdAt = $order->created_at;

                // Xóa hoàn toàn đơn hàng, không lưu lịch sử
                // Xóa chi tiết vé trước
                $order->chiTietVes()->delete();

                // Xóa combo đã đặt (nếu có)
                DB::table('don_dat_ve_combo')->where('don_dat_ve_id', $orderId)->delete();

                // Xóa các bản ghi giữ tạm ghế của người dùng cho suất chiếu này
                DB::table('ghe_giu_tam')
                    ->where('nguoi_dung_id', $userId)
                    ->where('suat_chieu_id', $suatChieuId)
                    ->delete();

                // Xóa đơn đặt vé
                $order->delete();

                // Trả ghế về trạng thái trống trong GheSuatChieu
                foreach ($gheIds as $gheId) {
                    $gheSuatChieu = GheSuatChieu::where('suat_chieu_id', $suatChieuId)
                        ->where('ghe_id', $gheId)
                        ->first();
                    if ($gheSuatChieu) {
                        $gheSuatChieu->update(['trang_thai' => 'hoat_dong']);
                    }
                }

                Log::info('Auto-deleted expired unpaid order (completely removed)', [
                    'order_id' => $orderId,
                    'ma_don' => $maDon,
                    'created_at' => $createdAt,
                    'seats_returned' => $gheIds
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error during cleanupExpiredBookings: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
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
            ->whereIn('trang_thai', ['cho_thanh_toan', 'da_thanh_toan'])
            ->firstOrFail();

        // Kiểm tra thời gian hủy (trước 2 giờ chiếu)
        $suatChieu = $donDatVe->suatChieu;
        if (Carbon::now()->addHours(2)->gte($suatChieu->gio_bat_dau)) {
            return back()->with('error', 'Không thể hủy vé trong vòng 2 giờ trước khi chiếu.');
        }

        DB::beginTransaction();
        try {
            // Đánh dấu chi tiết vé đã hủy để giữ lịch sử và trả ghế
            $donDatVe->chiTietVes()->update(['trang_thai' => 'da_huy']);

            // Cập nhật trạng thái đơn
            $donDatVe->trang_thai = 'da_huy';
            $donDatVe->save();

            // KHÔNG xóa bản ghi combo để lưu lịch sử; nếu muốn, có thể thêm cờ trạng thái riêng

            DB::commit();

            return redirect()->route('account.bookings')->with('success', 'Đã hủy đơn và lưu lịch sử thành công.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra khi hủy đơn.');
        }
    }

    /**
     * API hủy đơn đặt vé qua AJAX (dùng khi huỷ do hết thời gian thanh toán hoặc thoát trang)
     */
    public function ajaxCancel(Request $request, $id)
    {
        // Accept both AJAX requests and FormData (from sendBeacon)
        // sendBeacon doesn't send X-Requested-With header, so we check for either
        $isAjax = $request->ajax() || $request->has('page_exit') || $request->has('time_expired');
        
        if (!$isAjax) {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu không hợp lệ.',
            ], 400);
        }

        $donDatVe = DonDatVe::where('id', $id)
            ->where('nguoi_dung_id', auth()->id())
            ->whereIn('trang_thai', ['cho_thanh_toan', 'da_thanh_toan'])
            ->first();

        if (!$donDatVe) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn đặt vé không tồn tại hoặc đã được xử lý.',
            ], 404);
        }

        // Kiểm tra thời gian hủy (trước 2 giờ chiếu) - chỉ áp dụng cho hủy thủ công
        // Handle both JSON (from XHR) and FormData (from sendBeacon)
        $isPageExit = $request->input('page_exit', false);
        $isPageExit = $isPageExit === true || $isPageExit === '1' || $isPageExit === 1;
        
        $isTimeExpired = $request->input('time_expired', false);
        $isTimeExpired = $isTimeExpired === true || $isTimeExpired === '1' || $isTimeExpired === 1;
        
        $shouldDeleteCompletely = $isPageExit || $isTimeExpired; // Xóa hoàn toàn khi thoát trang hoặc hết thời gian
        
        if (!$shouldDeleteCompletely) {
            // Chỉ kiểm tra thời gian hủy cho hủy thủ công
            $suatChieu = $donDatVe->suatChieu;
            if (Carbon::now()->addHours(2)->gte($suatChieu->gio_bat_dau)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể hủy vé trong vòng 2 giờ trước khi chiếu.',
                ], 403);
            }
        }

        DB::beginTransaction();
        try {
            // Lấy danh sách ghế để trả về trạng thái trống
            $gheIds = $donDatVe->chiTietVes()->pluck('ghe_id')->toArray();

            if ($shouldDeleteCompletely) {
                // Khi thoát trang hoặc hết thời gian: Xóa hoàn toàn đơn hàng, không lưu lịch sử
                // Xóa chi tiết vé trước
                $donDatVe->chiTietVes()->delete();

                // Xóa combo đã đặt
                DB::table('don_dat_ve_combo')->where('don_dat_ve_id', $id)->delete();

                // Xóa đơn đặt vé
                $donDatVe->delete();

                Log::info('Booking completely deleted', [
                    'booking_id' => $id,
                    'user_id' => auth()->id(),
                    'seats_returned' => $gheIds,
                    'reason' => $isPageExit ? 'page_exit' : 'time_expired'
                ]);
            } else {
                // Hủy thủ công: Giữ lịch sử
                // Đánh dấu chi tiết vé đã hủy để giữ lịch sử
                $donDatVe->chiTietVes()->update(['trang_thai' => 'da_huy']);

                // Cập nhật trạng thái đơn
                $donDatVe->trang_thai = 'da_huy';
                $donDatVe->save();

                // Xóa combo đã đặt (nếu có)
                DB::table('don_dat_ve_combo')->where('don_dat_ve_id', $id)->delete();
            }

            // Xóa các bản ghi giữ tạm ghế của người dùng cho suất chiếu này
            DB::table('ghe_giu_tam')
                ->where('nguoi_dung_id', auth()->id())
                ->where('suat_chieu_id', $donDatVe->suat_chieu_id)
                ->delete();

            // Trả ghế về trạng thái trống (hoat_dong) trong GheSuatChieu
            foreach ($gheIds as $gheId) {
                $gheSuatChieu = GheSuatChieu::where('suat_chieu_id', $donDatVe->suat_chieu_id)
                    ->where('ghe_id', $gheId)
                    ->first();
                if ($gheSuatChieu) {
                    $gheSuatChieu->trang_thai = 'hoat_dong';
                    $gheSuatChieu->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $shouldDeleteCompletely 
                    ? ($isPageExit ? 'Đã hủy đơn do thoát trang.' : 'Đã hủy đơn do hết thời gian thanh toán.')
                    : 'Đã hủy đơn và trả ghế về trạng thái trống.',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi hủy đơn.',
            ], 500);
        }
    }

     public function momoCallback(Request $request)
    {
        Log::info('MoMo IPN received', $request->all());

        $orderId = $request->input('orderId');
        $resultCode = $request->input('resultCode');
        $amount = $request->input('amount');

        if (!$orderId) {
            Log::warning('MoMo IPN missing orderId', $request->all());
            return response('Missing orderId', 400);
        }

        // Extract ID từ orderId (BOOKING_23_timestamp)
        $parts = explode('_', $orderId);
        if (count($parts) < 2 || $parts[0] !== 'BOOKING') {
            Log::warning('MoMo IPN invalid orderId format: ' . $orderId);
            return response('Invalid orderId', 400);
        }
        
        $id = (int) $parts[1];
        $donDatVe = DonDatVe::find($id);
        
        if (!$donDatVe) {
            Log::warning('MoMo IPN order not found: ' . $orderId);
            return response('Order not found', 404);
        }

        DB::beginTransaction();
        try {
            // resultCode = 0: thành công
            if ((string)$resultCode === '0') {
            // Chỉ cập nhật nếu chưa thanh toán
                if ($donDatVe->trang_thai !== 'da_thanh_toan') {
                    $donDatVe->update([
                        'trang_thai' => 'da_thanh_toan',
                        'thoi_gian_thanh_toan' => Carbon::now(),
                        'phuong_thuc_thanh_toan' => 'momo',
                    ]);
                    $donDatVe->chiTietVes()->update(['trang_thai' => 'da_thanh_toan']);

                    // Tích điểm cho người dùng: 1 điểm cho mỗi 1000 VND
                    $diemTichLuy = floor($donDatVe->tong_tien / 1000);
                    if ($diemTichLuy > 0) {
                        $user = $donDatVe->nguoiDung;
                        $user->themDiem($diemTichLuy, 'Tích điểm từ đơn đặt vé ' . $donDatVe->ma_don);
                    }

                    // Clear dashboard cache to update statistics
                    Cache::increment('dashboard_version', 1);

                    Log::info('✅ Order marked paid via MoMo callback', ['orderId' => $orderId, 'don_dat_ve_id' => $id]);

                    // Gửi email xác nhận
                    try {
                        Mail::to($donDatVe->nguoiDung->email)->sendNow(new BookingConfirmation($donDatVe));
                        Log::info('✅ Email confirmation sent for MoMo payment: ' . $orderId);
                    } catch (\Exception $e) {
                        Log::error('❌ Error sending email for MoMo callback: ' . $e->getMessage());
                    }
                } else {
                    Log::info('Order already marked paid, skipping update', ['orderId' => $orderId]);
                }
                
                DB::commit();
                return response('OK', 200);
            }

            // resultCode !== 0: thanh toán thất bại
            Log::warning('MoMo IPN returned failure', ['orderId' => $orderId, 'resultCode' => $resultCode]);
            // Vẫn trả OK để MoMo server không retry
            DB::commit();
            return response('Ignored', 200);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error processing MoMo callback: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            return response('Internal error', 500);
        }
    }

    public function momoReturn(Request $request)
    {
        Log::info('MoMo return (GET redirect)', $request->all());
        
        $orderId = $request->query('orderId');
        $resultCode = $request->query('resultCode');

        if (!$orderId) {
            return redirect()->route('booking.index')->with('error', 'Thiếu dữ liệu trả về từ MoMo.');
        }

        // Extract ID từ orderId
        $parts = explode('_', $orderId);
        if (count($parts) < 2 || $parts[0] !== 'BOOKING') {
            return redirect()->route('booking.index')->with('error', 'Dữ liệu orderId không hợp lệ.');
        }

        $id = (int) $parts[1];
        $donDatVe = DonDatVe::find($id);

        if (!$donDatVe) {
            return redirect()->route('booking.index')->with('error', 'Đơn đặt vé không tồn tại.');
        }

        // ✅ FIX: resultCode = 0 => thanh toán thành công => CẬP NHẬT TRẠNG THÁI NGAY
        if ((string)$resultCode === '0') {
            Log::info('MoMo payment successful, updating status', ['id' => $id, 'orderId' => $orderId]);
            
            DB::beginTransaction();
            try {
                // ✅ Cập nhật trạng thái ngay
                $donDatVe->update([
                    'trang_thai' => 'da_thanh_toan',
                    'thoi_gian_thanh_toan' => Carbon::now(),
                    'phuong_thuc_thanh_toan' => 'momo',
                ]);
                
                // Cập nhật chi tiết vé
                $donDatVe->chiTietVes()->update(['trang_thai' => 'da_thanh_toan']);
                
                DB::commit();
                
                Log::info('✅ Order status updated to da_thanh_toan', ['id' => $id]);
                
                // ✅ Gửi email xác nhận
                try {
                    Mail::to($donDatVe->nguoiDung->email)->sendNow(new BookingConfirmation($donDatVe));
                    Log::info('✅ Confirmation email sent', ['id' => $id]);
                } catch (\Exception $e) {
                    Log::error('Error sending confirmation email: ' . $e->getMessage());
                }

                // ✅ Xóa ghế giữ tạm của người dùng cho suất chiếu này
                DB::table('ghe_giu_tam')
                    ->where('nguoi_dung_id', $donDatVe->nguoi_dung_id)
                    ->where('suat_chieu_id', $donDatVe->suat_chieu_id)
                    ->delete();
                
                return redirect()->route('booking.confirm', $id)->with('success', 'Thanh toán MoMo thành công!');
                
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error updating status in momoReturn: ' . $e->getMessage());
                return redirect()->route('booking.payment', $id)->with('error', 'Lỗi cập nhật trạng thái: ' . $e->getMessage());
            }
        }

        // resultCode !== 0: thanh toán thất bại
        Log::warning('MoMo payment failed', ['orderId' => $orderId, 'resultCode' => $resultCode]);

        // ✅ Nếu thanh toán thất bại, xóa hoàn toàn đơn hàng (không lưu lịch sử)
        DB::beginTransaction();
        try {
            // Lấy danh sách ghế để trả về trạng thái trống
            $gheIds = $donDatVe->chiTietVes()->pluck('ghe_id')->toArray();

            // Xóa chi tiết vé trước
            $donDatVe->chiTietVes()->delete();

            // Xóa combo đã đặt
            DB::table('don_dat_ve_combo')->where('don_dat_ve_id', $id)->delete();

            // Xóa đơn đặt vé
            $donDatVe->delete();

            // Trả ghế về trạng thái trống (hoat_dong) trong GheSuatChieu
            foreach ($gheIds as $gheId) {
                $gheSuatChieu = GheSuatChieu::where('suat_chieu_id', $donDatVe->suat_chieu_id)
                    ->where('ghe_id', $gheId)
                    ->first();
                if ($gheSuatChieu) {
                    $gheSuatChieu->trang_thai = 'hoat_dong';
                    $gheSuatChieu->save();
                }
            }

            DB::commit();
            Log::info('Booking completely deleted due to MoMo payment failure', [
                'booking_id' => $id,
                'user_id' => auth()->id(),
                'seats_returned' => $gheIds
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting booking after MoMo payment failure: ' . $e->getMessage());
        }

        return redirect()->route('booking.index', ['suat_chieu_id' => $donDatVe->suat_chieu_id ?? null])
            ->with('error', 'Thanh toán MoMo không thành công. Đơn hàng đã được hủy. Vui lòng đặt vé lại.');
    }

 /**

* Handle VNPay return URL
  */
  public function vnpayReturn(Request $request)
{
    Log::info('VNPay Return received', $request->all());

    $vnp_ResponseCode = $request->input('vnp_ResponseCode');
    $vnp_TxnRef = $request->input('vnp_TxnRef');
    $vnp_SecureHash = $request->input('vnp_SecureHash');

    if (!$vnp_TxnRef) {
        return redirect()->route('booking.index')
            ->with('error', 'Thiếu mã giao dịch từ VNPay.');
    }

    // Tách ID đơn hàng từ format BOOKING_{id}_{timestamp}
    $parts = explode('_', $vnp_TxnRef);
    $id = isset($parts[1]) ? (int)$parts[1] : 0;

    $donDatVe = DonDatVe::find($id);
    if (!$donDatVe) {
        return redirect()->route('booking.index')
            ->with('error', 'Đơn đặt vé không tồn tại.');
    }

    // ===== Xác thực hash =====
    $vnp_HashSecret = config('services.vnpay.hash_secret');
    $inputData = $request->except(['vnp_SecureHash']);
    ksort($inputData);

    $hashString = urldecode(http_build_query($inputData));
    $verifyHash = hash_hmac('sha512', $hashString, $vnp_HashSecret);

    if ($verifyHash !== $vnp_SecureHash) {
        return redirect()->route('booking.payment', $id)
            ->with('error', 'Dữ liệu trả về không hợp lệ.');
    }

    // ===== Thanh toán thành công =====
    if ($vnp_ResponseCode === "00") {
        Log::info("VNPay Return indicates success for order $id");

        DB::beginTransaction();
        try {
            $donDatVe->update([
                'trang_thai' => 'da_thanh_toan',
                'thoi_gian_thanh_toan' => now(),
                'phuong_thuc_thanh_toan' => 'vnpay',
                'ma_giao_dich' => $request->input('vnp_TransactionNo')
            ]);

            $donDatVe->chiTietVes()->update(['trang_thai' => 'da_thanh_toan']);

            // Tích điểm cho người dùng: 1 điểm cho mỗi 1000 VND
            $diemTichLuy = floor($donDatVe->tong_tien / 1000);
            if ($diemTichLuy > 0) {
                $user = $donDatVe->nguoiDung;
                $user->themDiem($diemTichLuy, 'Tích điểm từ đơn đặt vé ' . $donDatVe->ma_don);
            }

            // Clear dashboard cache to update statistics
            Cache::increment('dashboard_version', 1);

            DB::commit();

            // Gửi email
            try {
                Mail::to($donDatVe->nguoiDung->email)
                    ->sendNow(new BookingConfirmation($donDatVe));
            } catch (\Exception $e) {
                Log::error("Email send error in VNPay Return: " . $e->getMessage());
            }

            // ✅ Xóa ghế giữ tạm của người dùng cho suất chiếu này
            DB::table('ghe_giu_tam')
                ->where('nguoi_dung_id', $donDatVe->nguoi_dung_id)
                ->where('suat_chieu_id', $donDatVe->suat_chieu_id)
                ->delete();

            return redirect()->route('booking.confirm', $id)
                ->with('success', 'Thanh toán VNPay thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("VNPay Return DB error: " . $e->getMessage());

            return redirect()->route('booking.payment', $id)
                ->with('error', 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage());
        }
    }

    // ===== Thanh toán thất bại =====
    Log::warning("VNPay return failed for order $id");

    // ✅ Nếu thanh toán thất bại, xóa hoàn toàn đơn hàng (không lưu lịch sử)
    DB::beginTransaction();
    try {
        // Lấy danh sách ghế để trả về trạng thái trống
        $gheIds = $donDatVe->chiTietVes()->pluck('ghe_id')->toArray();

        // Xóa chi tiết vé trước
        $donDatVe->chiTietVes()->delete();

        // Xóa combo đã đặt
        DB::table('don_dat_ve_combo')->where('don_dat_ve_id', $id)->delete();

        // Xóa đơn đặt vé
        $donDatVe->delete();

        // Trả ghế về trạng thái trống (hoat_dong) trong GheSuatChieu
        foreach ($gheIds as $gheId) {
            $gheSuatChieu = GheSuatChieu::where('suat_chieu_id', $donDatVe->suat_chieu_id)
                ->where('ghe_id', $gheId)
                ->first();
            if ($gheSuatChieu) {
                $gheSuatChieu->trang_thai = 'hoat_dong';
                $gheSuatChieu->save();
            }
        }

        DB::commit();
        Log::info('Booking completely deleted due to VNPay payment failure', [
            'booking_id' => $id,
            'user_id' => auth()->id(),
            'seats_returned' => $gheIds
        ]);
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Error deleting booking after VNPay payment failure: ' . $e->getMessage());
    }

    return redirect()->route('booking.index', ['suat_chieu_id' => $donDatVe->suat_chieu_id ?? null])
        ->with('error', 'Thanh toán VNPay thất bại. Đơn hàng đã được hủy. Vui lòng đặt vé lại.');
}

/**

* VNPay IPN / Callback (nếu cần)
  */
  public function vnpayCallback(Request $request)
{
    Log::info('VNPay IPN received', $request->all());

    // Lấy các tham số từ VNPay
    $vnp_ResponseCode = $request->input('vnp_ResponseCode');
    $vnp_TxnRef = $request->input('vnp_TxnRef');
    $vnp_TransactionNo = $request->input('vnp_TransactionNo');
    $vnp_SecureHash = $request->input('vnp_SecureHash');

    if (!$vnp_TxnRef) {
        Log::warning('VNPay missing vnp_TxnRef');
        return response('Missing TxnRef', 400);
    }

    // Tách ID đơn hàng từ format BOOKING_{id}_{timestamp}
    $parts = explode('_', $vnp_TxnRef);
    $id = isset($parts[1]) ? (int)$parts[1] : 0;

    $donDatVe = DonDatVe::find($id);
    if (!$donDatVe) {
        Log::warning("VNPay IPN order not found: $vnp_TxnRef");
        return response('Order not found', 404);
    }

    // ===== Xác thực Secure Hash =====
    $vnp_HashSecret = config('services.vnpay.hash_secret');
    $inputData = $request->except(['vnp_SecureHash']);
    ksort($inputData);

    $hashString = urldecode(http_build_query($inputData));
    $verifyHash = hash_hmac('sha512', $hashString, $vnp_HashSecret);

    if ($verifyHash !== $vnp_SecureHash) {
        Log::warning("VNPay IPN invalid hash for order $id");
        return response("Invalid hash", 400);
    }

    DB::beginTransaction();
    try {
        if ($vnp_ResponseCode === "00") {
            // Chỉ update nếu chưa thanh toán
            if ($donDatVe->trang_thai !== 'da_thanh_toan') {
                $donDatVe->update([
                    'trang_thai' => 'da_thanh_toan',
                    'thoi_gian_thanh_toan' => now(),
                    'phuong_thuc_thanh_toan' => 'vnpay',
                    'ma_giao_dich' => $vnp_TransactionNo
                ]);

                $donDatVe->chiTietVes()->update(['trang_thai' => 'da_thanh_toan']);

                Log::info("✅ VNPay IPN - Order $id marked as PAID");

                // Gửi email
                try {
                    Mail::to($donDatVe->nguoiDung->email)
                        ->sendNow(new BookingConfirmation($donDatVe));
                } catch (\Exception $e) {
                    Log::error("VNPay IPN email error: " . $e->getMessage());
                }

                // ✅ Xóa ghế giữ tạm
                DB::table('ghe_giu_tam')
                    ->where('nguoi_dung_id', $donDatVe->nguoi_dung_id)
                    ->where('suat_chieu_id', $donDatVe->suat_chieu_id)
                    ->delete();


            }
        } else {
            Log::warning("VNPay IPN payment failed: $vnp_ResponseCode");
        }

        DB::commit();
        return response("OK", 200);

    } catch (\Exception $e) {
        DB::rollback();
        Log::error("VNPay callback error: " . $e->getMessage());
        return response("Internal error", 500);
    }
}

}
