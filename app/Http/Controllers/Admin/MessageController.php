<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%")
                  ->orWhere('message', 'like', "%{$q}%");
            });
        }

        if ($request->filled('filter')) {
            if ($request->filter === 'unread') $query->whereNull('read_at');
            if ($request->filter === 'read')   $query->whereNotNull('read_at');
        }

        $messages = $query->latest()->paginate(20)->withQueryString();

        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        $message->markRead();
        return view('admin.messages.show', compact('message'));
    }

    public function markUnread(ContactMessage $message)
    {
        $message->update(['read_at' => null]);
        return back()->with('success', 'Oznaczono jako nieprzeczytane.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return redirect()->route('admin.messages.index')->with('success', 'Wiadomość usunięta.');
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:read,unread,delete',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:contact_messages,id',
        ]);

        $q = ContactMessage::whereIn('id', $request->ids);

        if ($request->action === 'read')   $q->update(['read_at' => now()]);
        if ($request->action === 'unread') $q->update(['read_at' => null]);
        if ($request->action === 'delete') $q->delete();

        return back()->with('success', 'Akcja wykonana na ' . count($request->ids) . ' wiadomościach.');
    }
}
