<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonDatVe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QRController extends Controller
{
    public function scanPage()
    {
        return view('admin.donve.scan_qr');
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

            // Kiểm tra suất chiếu & khoảng thời gian cho phép (45p → 10p trước giờ chiếu)
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

            // Cập nhật chi tiết vé sang 'da_su_dung' (nếu cần)
            foreach ($don->chiTietVes as $ct) {
                if ($ct->trang_thai !== 'da_su_dung') {
                    $ct->trang_thai = 'da_su_dung';
                    $ct->save();
                }
            }

            // Cập nhật trạng thái đơn thành 'da_checkin'
            $don->trang_thai = 'da_checkin';
            $don->save();

            // Ghi log checkin
            \App\Models\CheckinPrintLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'don_dat_ve_id' => $don->id,
                'action_type' => 'checkin',
            ]);

            DB::commit();

            // Trả về redirect tới trang chi tiết đơn (dùng url trực tiếp để tránh trường hợp route name không tồn tại)
            return response()->json([
                'status' => true,
                'message' => 'Check-in thành công.',
                'redirect' => route('admin.admin.orders.showQR', $don->ma_don)
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('QR checkin error: '.$e->getMessage());

            return response()->json(['status' => false, 'message' => 'Có lỗi khi check-in.']);
        }
    }

}
