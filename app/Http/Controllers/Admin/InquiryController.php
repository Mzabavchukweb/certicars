<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inquiry::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%")
                  ->orWhere('car_title', 'like', "%{$q}%");
            });
        }

        if ($request->filled('filter')) {
            $filter = $request->filter;
            if ($filter === 'unread')         $query->whereNull('read_at');
            if ($filter === 'read')           $query->whereNotNull('read_at');
            if ($filter === 'type:general')   $query->where('type', 'general');
            if ($filter === 'type:financing') $query->where('type', 'financing');
            if (str_starts_with($filter, 'source:')) {
                $query->where('form_source', substr($filter, 7));
            }
        }

        $inquiries = $query->latest()->paginate(20)->withQueryString();
        $unreadCount = Inquiry::unread()->count();

        return view('admin.inquiries.index', compact('inquiries', 'unreadCount'));
    }

    public function show(Inquiry $inquiry)
    {
        $inquiry->markRead();
        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function markUnread(Inquiry $inquiry)
    {
        $inquiry->update(['read_at' => null]);
        return back()->with('success', 'Oznaczono jako nieprzeczytane.');
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();
        return redirect()->route('admin.inquiries.index')->with('success', 'Zapytanie usunięte.');
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:read,unread,delete',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:inquiries,id',
        ]);

        $q = Inquiry::whereIn('id', $request->ids);

        if ($request->action === 'read')   $q->update(['read_at' => now()]);
        if ($request->action === 'unread') $q->update(['read_at' => null]);
        if ($request->action === 'delete') $q->delete();

        return back()->with('success', 'Akcja wykonana na ' . count($request->ids) . ' zapytaniach.');
    }
}
