<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Support\SpamGuard;
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

        // Spam ma własną zakładkę i nie miesza się ze skrzynką.
        $spamTab = $request->filter === 'spam';
        $query->where('is_spam', $spamTab);

        if ($request->filter === 'unread') $query->whereNull('read_at');
        if ($request->filter === 'read')   $query->whereNotNull('read_at');

        $messages   = $query->latest()->paginate(20)->withQueryString();
        $spamCount  = ContactMessage::spam()->count();
        $inboxCount = ContactMessage::inbox()->count();

        return view('admin.messages.index', compact('messages', 'spamCount', 'inboxCount', 'spamTab'));
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

    /** „To nie spam” — wraca do skrzynki jako nieprzeczytana. */
    public function notSpam(ContactMessage $message)
    {
        $message->update(['is_spam' => false, 'spam_score' => 0, 'spam_reasons' => null, 'read_at' => null]);
        return back()->with('success', 'Wiadomość przywrócona do skrzynki.');
    }

    /** Ręczne oznaczenie jako spam (filtr czegoś nie złapał). */
    public function markSpam(ContactMessage $message)
    {
        $message->update(['is_spam' => true, 'read_at' => now()]);
        return back()->with('success', 'Wiadomość przeniesiona do spamu.');
    }

    public function clearSpam()
    {
        $n = ContactMessage::spam()->count();
        ContactMessage::spam()->delete();
        return back()->with('success', "Usunięto {$n} wiadomości ze spamu.");
    }

    /** Przejrzyj wiadomości, które są już w skrzynce, i przenieś spam do kwarantanny. */
    public function sweep(SpamGuard $guard)
    {
        $moved = 0;
        ContactMessage::where('is_spam', false)->orderByDesc('id')->limit(5000)->get()
            ->each(function ($m) use ($guard, &$moved) {
                $v = $guard->inspectStored($m->name, $m->email, $m->phone, $m->message);
                if (!$v['spam']) return;
                $m->forceFill([
                    'is_spam'      => true,
                    'spam_score'   => $v['score'],
                    'spam_reasons' => $v['reasons'],
                    'read_at'      => $m->read_at ?? now(),
                    'body_hash'    => $m->body_hash ?? SpamGuard::bodyHash($m->message),
                ])->save();
                $moved++;
            });

        return back()->with('success', $moved
            ? "Przeniesiono do spamu: {$moved}. Sprawdź zakładkę Spam — jeśli coś zostało tam przez pomyłkę, kliknij „To nie spam”."
            : 'Nie znaleziono spamu wśród wiadomości w skrzynce.');
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:read,unread,delete,spam,not_spam',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:contact_messages,id',
        ]);

        $q = ContactMessage::whereIn('id', $request->ids);

        if ($request->action === 'read')   $q->update(['read_at' => now()]);
        if ($request->action === 'unread') $q->update(['read_at' => null]);
        if ($request->action === 'delete') $q->delete();
        if ($request->action === 'spam')     $q->update(['is_spam' => true, 'read_at' => now()]);
        if ($request->action === 'not_spam') $q->update(['is_spam' => false, 'spam_score' => 0, 'spam_reasons' => null]);

        return back()->with('success', 'Akcja wykonana na ' . count($request->ids) . ' wiadomościach.');
    }
}
