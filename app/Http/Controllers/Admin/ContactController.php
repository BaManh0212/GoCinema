<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactReply;
use App\Notifications\ContactRepliedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\NguoiDung;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query()->withCount('replies')->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($qr) use ($q){
                $qr->where('name','like',"%{$q}%")
                   ->orWhere('email','like',"%{$q}%")
                   ->orWhere('message','like',"%{$q}%");
            });
        }

        $contacts = $query->paginate(15)->withQueryString();
        return view('admin.contacts.index', compact('contacts'));
    }

    public function show(Contact $contact)
    {
        $contact->load('replies.admin');
        if ($contact->status === 'pending') {
            $contact->update(['status' => 'read']);
        }
        return view('admin.contacts.show', compact('contact'));
    }

    public function reply(Request $request, Contact $contact)
    {
        $data = $request->validate([
            'reply_message' => 'required|string|min:2',
        ]);

        // Ensure admin exists in NguoiDung table (app's user table)
        $adminId = Auth::id();
        if (! NguoiDung::find($adminId)) {
            return back()->with('error', 'Admin account not found. Cannot post reply.');
        }

        $reply = ContactReply::create([
            'contact_id' => $contact->id,
            'admin_id' => $adminId,
            'reply_message' => $data['reply_message'],
        ]);

        $contact->update(['status' => 'replied']);

        // Send in-app DB notification to the user if exists
        if ($contact->user) {
            $contact->user->notify(new ContactRepliedNotification($reply));
        }

        return redirect()->route('admin.contacts.show', $contact->id)->with('success', 'Đã gửi phản hồi (in-app).');
    }

    public function markRead(Contact $contact)
    {
        $contact->update(['status' => 'read']);
        return back()->with('success', 'Đã đánh dấu là đã đọc.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')->with('success', 'Đã xóa liên hệ.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) Contact::whereIn('id', $ids)->delete();
        return back()->with('success', 'Đã xóa các liên hệ chọn.');
    }
}
