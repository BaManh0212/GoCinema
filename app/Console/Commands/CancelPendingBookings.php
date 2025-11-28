<?php

namespace App\Console\Commands;

use App\Models\DonDatVe;
use App\Models\GheSuatChieu;
use App\Models\DonDatVeCombo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelPendingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hủy tự động các đơn đặt vé ở trạng thái "cho_thanh_toan" và quá 10 phút, trả lại ghế và combo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu hủy đơn đặt vé chờ thanh toán quá hạn...');

        // Lấy các đơn đặt vé chờ thanh toán và quá 10 phút
        $expiredPendingBookings = DonDatVe::where('trang_thai', 'cho_thanh_toan')
            ->where('created_at', '<=', Carbon::now()->subMinutes(10))
            ->with(['chiTietVes', 'combos'])
            ->get();

        $count = 0;
        foreach ($expiredPendingBookings as $booking) {
            DB::beginTransaction();
            try {
                // Cập nhật trạng thái chi tiết vé thành 'da_huy'
                $booking->chiTietVes()->update(['trang_thai' => 'da_huy']);

                // Trả lại ghế: cập nhật trạng thái ghế trong GheSuatChieu về 'hoat_dong'
                foreach ($booking->chiTietVes as $chiTietVe) {
                    GheSuatChieu::where('suat_chieu_id', $booking->suat_chieu_id)
                        ->where('ghe_id', $chiTietVe->ghe_id)
                        ->update(['trang_thai' => 'hoat_dong']);
                }

                // Trả lại combo: tăng lại số lượng combo
                $donDatVeCombos = DonDatVeCombo::where('don_dat_ve_id', $booking->id)->get();
                foreach ($donDatVeCombos as $donDatVeCombo) {
                    $combo = $donDatVeCombo->combo;
                    if ($combo) {
                        $combo->increment('so_luong', $donDatVeCombo->so_luong);
                    }
                }

                // Cập nhật trạng thái đơn đặt vé thành 'da_huy'
                $booking->update(['trang_thai' => 'da_huy']);

                DB::commit();
                $count++;
            } catch (\Exception $e) {
                DB::rollback();
                $this->error("Lỗi khi hủy đơn {$booking->id}: " . $e->getMessage());
            }
        }

        $this->info("Đã hủy {$count} đơn đặt vé chờ thanh toán quá hạn và trả lại ghế + combo.");

        return Command::SUCCESS;
    }
}
