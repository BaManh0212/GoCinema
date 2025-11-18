<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<class-string>
     */
    protected $commands = [
        \App\Console\Commands\CleanupPendingBookings::class,
        \App\Console\Commands\UpdateBookingStatus::class,
        \App\Console\Commands\UpdateExpiredBookings::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Ch dọn dẹp đơn chờ thanh toán quá hạn và ghế giữ tạm hết hạn
        $schedule->command('booking:cleanup')->everyMinute();

        // Cập nhật trạng thái đơn đặt vé đã quá giờ chiếu
        $schedule->command('bookings:update-status')->everyMinute();

        // Cập nhật đơn đặt vé đã qua thời gian bắt đầu suất chiếu thành "qua_han"
        $schedule->command('bookings:update-expired')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
