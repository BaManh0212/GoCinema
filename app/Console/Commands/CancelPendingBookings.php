<?php

namespace App\Console\Commands;

use App\Models\DonDatVe;
use Illuminate\Console\Command;
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
    protected $description = 'Hủy tự động các đơn đặt vé ở trạng thái "cho_thanh_toan" và quá 10 phút';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu hủy đơn đặt vé chờ thanh toán quá hạn...');

        // Lấy các đơn đặt vé chờ thanh toán và quá 10 phút
        $expiredPendingBookings = DonDatVe::where('trang_thai', 'cho_thanh_toan')
            ->where('created_at', '<=', Carbon::now()->subMinutes(10))
            ->get();

        $count = 0;
        foreach ($expiredPendingBookings as $booking) {
            // Cập nhật trạng thái chi tiết vé thành 'da_huy'
            $booking->chiTietVes()->update(['trang_thai' => 'da_huy']);

            // Cập nhật trạng thái đơn đặt vé thành 'da_huy'
            $booking->update(['trang_thai' => 'da_huy']);

            $count++;
        }

        $this->info("Đã hủy {$count} đơn đặt vé chờ thanh toán quá hạn.");

        return Command::SUCCESS;
    }
}
