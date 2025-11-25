<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
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

        // Lấy ghế giữ tạm (trong 10 phút)
        $giuTamIds = DB::table('ghe_giu_tam')
            ->where('suat_chieu_id', $suatChieuId)
            ->where('het_han', '>', Carbon::now())
            ->pluck('ghe_id')
            ->toArray();

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
            'combos',
            'sanPhams',
            'availableVouchers'
        ));
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

            // Thêm ghế giữ tạm mới (bắt duplicate nếu có race)
            foreach ($gheIds as $gheId) {
                try {
                    DB::table('ghe_giu_tam')->insert([
                        'suat_chieu_id' => $suatChieuId,
                        'ghe_id' => $gheId,
                        'nguoi_dung_id' => auth()->id(),
                        'het_han' => Carbon::now()->addMinutes(10),
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

        // Kiểm tra ghế liên tiếp
        if (!$this->areSeatsConsecutive($gheIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Ghế phải được chọn liên tiếp nhau trong cùng một hàng.',
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

            // Xóa ghế giữ tạm sau khi tạo đơn đặt vé
            DB::table('ghe_giu_tam')
                ->where('nguoi_dung_id', auth()->id())
                ->delete();

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
            'payment_method' => 'required|in:momo,zalopay,bank',
        ]);

        $donDatVe = DonDatVe::where('id', $id)
            ->where('nguoi_dung_id', auth()->id())
            ->where('trang_thai', 'cho_thanh_toan')
            ->firstOrFail();

        $paymentMethod = $request->payment_method;

        //===== Xử lý thanh toán MoMo ======//
        if ($paymentMethod === 'momo') {
            $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

            $partnerCode = env('MOMO_PARTNER_CODE', 'MOMOBKUN20180529');
            $accessKey = env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j');
            $secretKey = env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');

            $orderId = 'BOOKING_' . $donDatVe->id . '_' . time();;
            $amount = (int) round($donDatVe->tong_tien); // MoMo yêu cầu integer
            $orderInfo = 'Thanh toán đơn ' . $donDatVe->ma_don;
            $redirectUrl = route('booking.confirm', $donDatVe->id); // GET route đã có
            $ipnUrl = route('booking.momo-callback'); // bạn đã thêm route IPN nếu cần
            $requestId = (string) time();
            $requestType = 'payWithATM'; // hoặc request type phù hợp

            $extraData = ''; // nếu cần truyền thêm

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

            try {
                $response = Http::timeout(10)->post($endpoint, $data)->json();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi kết nối MoMo: ' . $e->getMessage(),
                ], 500);
            }

            if (!empty($response) && isset($response['resultCode']) && $response['resultCode'] == 0 && !empty($response['payUrl'])) {
                // Nếu request là AJAX trả JSON để client redirect
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'payUrl' => $response['payUrl']]);
        }
    // Nếu form submit bình thường: server trực tiếp redirect (browser sẽ load trang MoMo)
    return redirect()->away($response['payUrl']);
            }

            // Trả về lỗi từ MoMo để debug
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tạo thanh toán MoMo: ' . ($response['message'] ?? 'Không xác định'),
                'response' => $response,
            ], 500);
        }
        
       // ===== ZaloPay =====
if ($paymentMethod === 'zalopay') {
    $endpoint = env('ZALOPAY_ENDPOINT', 'https://sb-openapi.zalopay.vn/v2/create');
    $appId    = env('ZALOPAY_APP_ID');
    $key1     = env('ZALOPAY_KEY1');

    if (!$appId || !$key1) {
        return response()->json(['success' => false, 'message' => 'Thiếu cấu hình ZaloPay (.env)'], 400);
    }

    $appUser    = substr((string)(auth()->user()->email ?? ('user' . $donDatVe->nguoi_dung_id)), 0, 50);
    $appTime    = (int) round(microtime(true) * 1000);
    $appTransId = date('ymd') . '_' . $donDatVe->id . '_' . time();
    $amount     = (int) round($donDatVe->tong_tien);

    $embedData = json_encode(['return_url' => route('booking.zalopay_return')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $item = json_encode([[
        'itemid' => 'VE',
        'itemname' => 'Vé xem phim',
        'itemprice' => $amount,
        'itemquantity' => 1
    ]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    // THỨ TỰ MAC: app_id|app_trans_id|app_user|amount|app_time|embed_data|item
    $rawMac = $appId . '|' . $appTransId . '|' . $appUser . '|' . $amount . '|' . $appTime . '|' . $embedData . '|' . $item;
    $mac = hash_hmac('sha256', $rawMac, $key1);

    $payload = [
        'app_id'       => (int)$appId,
        'app_user'     => $appUser,
        'app_time'     => $appTime,
        'app_trans_id' => $appTransId,
        'amount'       => $amount,
        'embed_data'   => $embedData,
        'item'         => $item,
        'description'  => 'Thanh toán đơn ' . ($donDatVe->ma_don ?? $appTransId),
        'mac'          => $mac,
    ];

    Log::info('ZaloPay rawMac', ['rawMac' => $rawMac]);
    Log::info('ZaloPay payload', $payload);

    try {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->timeout(15)->post($endpoint, $payload)->json();
        Log::info('ZaloPay response', $response);
    } catch (\Exception $e) {
        Log::error('ZaloPay request error', ['msg' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Lỗi kết nối ZaloPay: ' . $e->getMessage()], 500);
    }

    if (isset($response['return_code']) && $response['return_code'] == 1 && !empty($response['order_url'])) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'payUrl' => $response['order_url']]);
        }
        return redirect()->away($response['order_url']);
    }

    Log::warning('ZaloPay create failed', $response ?? []);
    return response()->json(['success' => false, 'message' => 'Lỗi: ' . ($response['return_message'] ?? 'Không xác định'), 'response' => $response], 400);
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
     * API hủy đơn đặt vé qua AJAX (dùng khi huỷ do thoát trang thanh toán)
     */
    public function ajaxCancel(Request $request, $id)
    {
        if (!$request->ajax()) {
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

        // Kiểm tra thời gian hủy (trước 2 giờ chiếu)
        $suatChieu = $donDatVe->suatChieu;
        if (Carbon::now()->addHours(2)->gte($suatChieu->gio_bat_dau)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy vé trong vòng 2 giờ trước khi chiếu.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái vé chi tiết thành đã hủy
            $donDatVe->chiTietVes()->update(['trang_thai' => 'da_huy']);

            // Cập nhật trạng thái đơn thành đã hủy
            $donDatVe->trang_thai = 'da_huy';
            $donDatVe->save();

            // Xóa các bản ghi giữ tạm ghế của người dùng cho suất chiếu này
            DB::table('ghe_giu_tam')
                ->where('nguoi_dung_id', auth()->id())
                ->where('suat_chieu_id', $suatChieu->id)
                ->delete();

            // Cập nhật trạng thái ghế trong GheSuatChieu về trạng thái 'hoat_dong'
            $chiTietVes = $donDatVe->chiTietVes()->get();
            foreach ($chiTietVes as $chiTietVe) {
                $gheSuatChieu = GheSuatChieu::where('suat_chieu_id', $suatChieu->id)
                    ->where('ghe_id', $chiTietVe->ghe_id)
                    ->first();
                if ($gheSuatChieu) {
                    $gheSuatChieu->trang_thai = 'hoat_dong';
                    $gheSuatChieu->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã hủy đơn và trả ghế thành công.',
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

        $orderId = $request->input('orderId'); // ví dụ: BOOKING_23
        $resultCode = $request->input('resultCode');

        if (!$orderId) {
            Log::warning('MoMo IPN missing orderId', $request->all());
            return response('Missing orderId', 400);
        }

        $id = (int) str_replace('BOOKING_', '', $orderId);
        $donDatVe = DonDatVe::find($id);
        if (!$donDatVe) {
            Log::warning('MoMo IPN order not found: ' . $orderId);
            return response('Order not found', 404);
        }

        // Nếu thanh toán thành công cập nhật trạng thái
        if ((string)$resultCode === '0') {
            $donDatVe->update([
                'trang_thai' => 'da_thanh_toan',
                'thoi_gian_thanh_toan' => Carbon::now(),
            ]);
            $donDatVe->chiTietVes()->update(['trang_thai' => 'da_thanh_toan']);
            Log::info('Order marked paid via MoMo: ' . $orderId);
            return response('OK', 200);
        }

        Log::info('MoMo IPN returned failure', $request->all());
        return response('Ignored', 200);
    }

    public function momoReturn(Request $request)
    {
        // Người dùng được redirect (GET) từ MoMo => xử lý hiển thị/redirect tới trang confirm
        $orderId = $request->query('orderId');
        $resultCode = $request->query('resultCode');

        if (!$orderId) {
            return redirect()->route('booking.index')->with('error', 'Thiếu dữ liệu trả về từ MoMo.');
        }

        $id = (int) str_replace('BOOKING_', '', $orderId);
        if ($resultCode === '0') {
            return redirect()->route('booking.confirm', $id)->with('success', 'Thanh toán MoMo thành công.');
        }

        return redirect()->route('booking.payment', $id)->with('error', 'Thanh toán MoMo không thành công.');
    }

    public function zalopayReturn(Request $request)
    {
        // Hiển thị kết quả cho user, hoặc verify lại bằng key2 nếu cần
        Log::info('ZaloPay return', $request->all());
        // redirect tới trang confirm hoặc hiển thị lỗi
        return redirect()->route('booking.confirm', $request->query('app_trans_id') ? explode('_', $request->query('app_trans_id'))[1] : null)
            ->with('success', 'Thanh toán ZaloPay trả về (kiểm tra trạng thái).');
    }

    public function zalopayCallback(Request $request)
    {
        Log::info('ZaloPay callback', $request->all());
        // TODO: verify mac using key2 and update order trạng thái nếu thanh toán thành công
        return response('OK', 200);
    }
}
