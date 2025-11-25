<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\DonDatVe;

class CheckinPrintLog extends Model
{
    use HasFactory;

    protected $table = 'checkin_print_logs';

    protected $fillable = [
        'user_id',
        'don_dat_ve_id',
        'action_type',
    ];

    /**
     * The user who performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The booking order related to this log.
     */
    public function donDatVe()
    {
        return $this->belongsTo(DonDatVe::class, 'don_dat_ve_id');
    }
}
