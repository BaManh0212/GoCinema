<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NguoiDung;

class ContactController extends Controller
{
    public function create()
    {
        return view('client.contact.contact_form'); // view below
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email'=> 'required|email|max:191',
            'message' => 'required|string|min:5',
        ]);

    // Ensure we only persist a user_id that exists in the application's user table (NguoiDung)
    $userId = Auth::id();
    $data['user_id'] = NguoiDung::find($userId) ? $userId : null;
    $data['status'] = 'pending';

        Contact::create($data);

        return redirect()->route('contact.history')->with('success', 'Gửi liên hệ thành công. Chúng tôi sẽ phản hồi sớm.');
    }

    public function history()
    {
        $contacts = Contact::where('user_id', auth()->id())
            ->with('replies')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('client.contact.contact_history', compact('contacts'));
    }
}
