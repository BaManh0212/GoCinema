<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DonDatVe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QRController extends Controller
{
    public function scanPage()
    {
        return view('staff.donve.scan_qr');
    }

    public function check(Request $request)
    {
        $maDon = trim($request->ma_don ?? '');

        if (!$maDon) {
            return response()->json(['status' => false, 'message' => 'Mã đơn trống.']);
        }

        try {
            DB::beginTransaction();

            $don = DonDatVe::with(['chiTietVes.ghe', 'suatChieu'])
                ->where('ma_don', $maDon)
                ->lockForUpdate()
                ->first();

            if (! $don) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Không tìm thấy đơn.']);
            }

            if ($don->trang_thai === 'da_checkin') {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Đơn đã được check-in.']);
            }

            if ($don->trang_thai !== 'da_thanh_toan') {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Đơn chưa thanh toán.']);
            }

            $suatChieu = $don->suatChieu;
            if (! $suatChieu) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Không tìm thấy thông tin suất chiếu.']);
            }

            $now = now();
            $thoiGianBatDau = \Carbon\Carbon::parse($suatChieu->gio_bat_dau);
            $startAllowed = $thoiGianBatDau->copy()->subMinutes(45);
            $endAllowed = $thoiGianBatDau->copy()->subMinutes(10);

            if ($now->lt($startAllowed)) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Chỉ được phép check-in từ 45 phút đến 10 phút trước khi phim bắt đầu.']);
            }

            if ($now->gt($endAllowed) || $now->gt($thoiGianBatDau)) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Đã quá thời gian cho phép check-in.']);
            }

            foreach ($don->chiTietVes as $ct) {
                if ($ct->trang_thai !== 'da_su_dung') {
                    $ct->trang_thai = 'da_su_dung';
                    $ct->save();
                }
            }

            $don->trang_thai = 'da_checkin';
            $don->save();

            \App\Models\CheckinPrintLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'don_dat_ve_id' => $don->id,
                'action_type' => 'checkin',
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Check-in thành công.',
                'redirect' => route('staff.donve.show', $don->id)
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Staff QR checkin error: '.$e->getMessage());

            return response()->json(['status' => false, 'message' => 'Có lỗi khi check-in.']);
        }
    }
}