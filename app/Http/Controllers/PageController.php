<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
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

    /**
     * Marketing landing dla feature CertiCheck — dostępny z menu głównego
     * jako `/certicheck`. Oddzielna od `catalog.certicheck` (raport per-car
     * na URL `/samochody/{slug}/certicheck`), która pokazuje broszurę
     * konkretnego pojazdu. Ta landing wyjaśnia co to jest CertiCheck i
     * linkuje do katalogu z filtrem CertiCheck.
     */
    public function certicheckLanding()
    {
        return view('pages.certicheck');
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|min:2|max:100',
            'email'   => 'required|email|max:200',
            'phone'   => 'nullable|string|max:30',
            'message' => 'required|string|min:10|max:2000',
            'website' => 'nullable|size:0',
        ]);

        unset($validated['website']);

        ContactMessage::create($validated + [
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        return back()->with('success', 'Dziękujemy! Odezwiemy się wkrótce.');
    }
}
