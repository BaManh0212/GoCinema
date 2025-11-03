<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\NguoiDung;

class ContactReply extends Model
{
    protected $fillable = ['contact_id', 'admin_id', 'reply_message'];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function admin()
    {
        // Reference the application's real user model (NguoiDung) which stores admins
        return $this->belongsTo(NguoiDung::class, 'admin_id');
    }
}
