<?php

namespace App\Console\Commands;

use App\Models\DonDatVe;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateBookingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update booking statuses that have passed their showtime';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        try {
            // Update bookings that are within 10 minutes of showtime or have passed showtime
            $updated = DonDatVe::whereIn('trang_thai', ['dang_cho_thanh_toan', 'da_thanh_toan'])
                ->whereHas('suatChieu', function($query) use ($now) {
                    // Mark as expired if current time is within 10 minutes of showtime or after showtime
                    $query->where('gio_bat_dau', '<=', $now->copy()->addMinutes(10));
                })
                ->update(['trang_thai' => 'qua_han']);

            $this->info("Successfully updated {$updated} booking(s) to 'qua_han' status.");
            Log::info("Updated {$updated} booking(s) to 'qua_han' status.");
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error updating booking statuses: ' . $e->getMessage());
            Log::error('Error in UpdateBookingStatus command: ' . $e->getMessage());
            return 1;
        }
    }
}
