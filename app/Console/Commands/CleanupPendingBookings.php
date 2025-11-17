<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DonDatVe;
use Carbon\Carbon;

class CleanupPendingBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:cleanup {--minutes=10 : Số phút cho phép chờ thanh toán}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hủy đơn chờ thanh toán quá hạn và trả ghế; xóa giữ tạm hết hạn';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minutes = (int) $this->option('minutes');
        $deadline = Carbon::now()->subMinutes($minutes);

        $this->info("[Cleanup] Bắt đầu dọn dẹp. Hạn: {$deadline->toDateTimeString()}");

        // 1) Xóa giữ tạm đã hết hạn
        $expiredHolds = DB::table('ghe_giu_tam')
            ->where('het_han', '<=', Carbon::now())
            ->delete();
        $this->info("[Cleanup] Đã xóa {$expiredHolds} giữ tạm đã hết hạn.");

        // 2) Hủy đơn chờ thanh toán quá hạn
        DonDatVe::where('trang_thai', 'cho_thanh_toan')
            // Quá hạn tính theo thoi_gian_dat; nếu null thì dùng created_at
            ->where(function ($q) use ($deadline) {
                $q->whereNotNull('thoi_gian_dat')
                  ->where('thoi_gian_dat', '<=', $deadline)
                  ->orWhere(function ($qq) use ($deadline) {
                      $qq->whereNull('thoi_gian_dat')
                         ->where('created_at', '<=', $deadline);
                  });
            })
            ->orderBy('id')
            ->chunkById(200, function ($orders) {
                foreach ($orders as $order) {
                    DB::beginTransaction();
                    try {
                        // Cập nhật chi tiết vé thành đã hủy để trả ghế nhưng giữ lịch sử
                        DB::table('chi_tiet_ve')
                            ->where('don_dat_ve_id', $order->id)
                            ->update(['trang_thai' => 'da_huy', 'updated_at' => Carbon::now()]);

                        // Cập nhật trạng thái đơn thành đã hủy (giữ log đơn)
                        DB::table('don_dat_ve')->where('id', $order->id)->update([
                            'trang_thai' => 'da_huy',
                            'updated_at' => Carbon::now(),
                        ]);
                        DB::commit();
                        $this->info("[Cleanup] Hủy đơn #{$order->id} (quá hạn)");
                    } catch (\Throwable $e) {
                        DB::rollBack();
                        Log::error('Cleanup booking failed', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage(),
                        ]);
                        $this->error("[Cleanup] Lỗi khi xử lý đơn #{$order->id}: {$e->getMessage()}");
                    }
                }
            });

        $this->info('[Cleanup] Hoàn tất.');
        return self::SUCCESS;
    }
}
