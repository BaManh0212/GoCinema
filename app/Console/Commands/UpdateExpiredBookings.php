<?php

namespace App\Console\Commands;

use App\Models\DonDatVe;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật trạng thái đơn đặt vé đã qua thời gian bắt đầu suất chiếu thành "qua_han"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu cập nhật đơn đặt vé đã quá hạn...');

        // Lấy các đơn đặt vé có trạng thái 'da_thanh_toan' và suất chiếu đã bắt đầu
        $expiredBookings = DonDatVe::where('trang_thai', 'da_thanh_toan')
            ->whereHas('suatChieu', function ($query) {
                $query->where('gio_bat_dau', '<', Carbon::now());
            })
            ->get();

        $count = 0;
        foreach ($expiredBookings as $booking) {
            $booking->update(['trang_thai' => 'qua_han']);
            $count++;
        }

        $this->info("Đã cập nhật {$count} đơn đặt vé thành trạng thái 'qua_han'.");

        return Command::SUCCESS;
    }
}
