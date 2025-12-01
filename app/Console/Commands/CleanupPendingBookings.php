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

        // 1) Xóa giữ tạm đã hết hạn (nhưng KHÔNG xóa ghế của đơn đang chờ thanh toán)
        // Lấy danh sách ghế đang trong đơn chờ thanh toán
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

        // Lấy tất cả ghế giữ tạm đã hết hạn
        $expiredHolds = DB::table('ghe_giu_tam')
            ->where('het_han', '<=', Carbon::now())
            ->get();

        // Lọc ra các ghế giữ tạm KHÔNG thuộc đơn chờ thanh toán
        $holdsToDelete = $expiredHolds->filter(function($hold) use ($gheIdsInPendingOrders) {
            $key = $hold->suat_chieu_id . '_' . $hold->ghe_id;
            return !in_array($key, $gheIdsInPendingOrders);
        });

        if ($holdsToDelete->isNotEmpty()) {
            // Xóa các ghế giữ tạm đã hết hạn và không thuộc đơn chờ thanh toán
            $gheIdsToDelete = $holdsToDelete->pluck('ghe_id')->toArray();
            $suatChieuIdsToDelete = $holdsToDelete->pluck('suat_chieu_id')->toArray();
            
            $deletedCount = DB::table('ghe_giu_tam')
                ->where('het_han', '<=', Carbon::now())
                ->whereIn('ghe_id', $gheIdsToDelete)
                ->whereIn('suat_chieu_id', $suatChieuIdsToDelete)
                ->delete();
            
            $this->info("[Cleanup] Đã xóa {$deletedCount} giữ tạm đã hết hạn (bỏ qua ghế của đơn chờ thanh toán).");
        } else {
            $this->info("[Cleanup] Không có giữ tạm nào cần xóa.");
        }

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
