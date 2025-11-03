<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\NguoiDung;

class Contact extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'phone', 'subject', 'message', 'status'
    ];

    public function replies(): HasMany
    {
        return $this->hasMany(ContactReply::class);
    }

    public function user(): BelongsTo
    {
        // Explicitly reference the NguoiDung model (app's real user table)
        return $this->belongsTo(NguoiDung::class, 'user_id');
    }
}
