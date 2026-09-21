<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Event;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        $stats = [
            'cars'     => \App\Models\Car::count(),
            'brands'   => \App\Models\Brand::count(),
            'featured' => \App\Models\Car::where('is_featured', true)->count(),
        ];

        return view('pages.about', compact('stats'));
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function certicheckLanding()
    {
        return view('pages.certicheck');
    }

    public function privacy()
    {
        return view('pages.privacy', ['updated' => '15 lipca 2026']);
    }

    public function terms()
    {
        return view('pages.terms', ['updated' => '15 lipca 2026']);
    }

    public function cookies()
    {
        return view('pages.cookies', ['updated' => '15 lipca 2026']);
    }


    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|min:2|max:100',
            'email'   => 'required|email|max:200',
            'phone'   => 'nullable|string|max:30',
            'message' => 'required|string|min:10|max:2000',
            'website' => 'nullable|string|max:200', // honeypot — ocenia SpamGuard, nie walidacja
        ]);

        unset($validated['website']);

        // Filtr antyspamowy — spam ląduje w kwarantannie (zakładka Spam w
        // adminie), nadawca dostaje normalne potwierdzenie, żeby bot nie
        // wiedział, że został złapany, i nie próbował dalej.
        $verdict = app(\App\Support\SpamGuard::class)->inspect(
            $request, $validated['name'], $validated['email'], $validated['phone'] ?? null, $validated['message']
        );

        ContactMessage::create($validated + [
            'ip'           => $request->ip(),
            'user_agent'   => substr((string) $request->userAgent(), 0, 500),
            'is_spam'      => $verdict['spam'],
            'spam_score'   => $verdict['score'],
            'spam_reasons' => $verdict['reasons'],
            'body_hash'    => \App\Support\SpamGuard::bodyHash($validated['message']),
        ]);

        if (!$verdict['spam']) {
            Event::record('contact_submitted', $request);
        }

        // AJAX (fetch) submit → return JSON so the page never reloads and the
        // visitor stays exactly where they are (no scroll jump). Validation
        // errors are returned as 422 JSON automatically by Laravel.
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Dziękujemy! Odezwiemy się wkrótce.']);
        }

        return back()->with('success', 'Dziękujemy! Odezwiemy się wkrótce.');
    }
}
