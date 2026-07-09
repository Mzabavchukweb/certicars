<?php

namespace App\Http\Controllers;

use App\Mail\InquiryConfirmation;
use App\Mail\InquiryReceived;
use App\Models\Car;
use App\Models\Event;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id'  => 'required|exists:cars,id',
            'type'    => 'required|in:general,financing',
            'name'    => 'required|string|max:100',
            'phone'   => 'required|string|max:30',
            'email'   => 'nullable|email|max:200',
            'message' => 'nullable|string|max:2000',
            'consent' => 'required|accepted',
            'website' => 'max:0', // honeypot
        ], [
            'consent.required' => 'Aby wysłać zapytanie, musisz wyrazić zgodę na przetwarzanie danych osobowych.',
            'consent.accepted' => 'Aby wysłać zapytanie, musisz wyrazić zgodę na przetwarzanie danych osobowych.',
        ]);

        $car = Car::with('brand')->find($validated['car_id']);

        $inquiry = Inquiry::create([
            'car_id'      => $validated['car_id'],
            'type'        => $validated['type'],
            'name'        => $validated['name'],
            'phone'       => $validated['phone'],
            'email'       => $validated['email'] ?? null,
            'message'     => $validated['message'] ?? null,
            'car_title'   => $car ? ($car->brand?->name . ' ' . $car->model . ' ' . $car->year) : null,
            'ip'          => $request->ip(),
            'user_agent'  => substr($request->userAgent() ?? '', 0, 500),
            'form_source' => $this->sanitizeShort($request->input('form_source')),
            'source_page' => $this->sanitizeLong($request->input('source_page')),
            'referrer'    => $this->sanitizeLong($request->input('referrer')),
            'utm_source'  => $this->sanitizeShort($request->input('utm_source')),
            'utm_medium'  => $this->sanitizeShort($request->input('utm_medium')),
            'utm_campaign'=> $this->sanitizeShort($request->input('utm_campaign')),
            'utm_content' => $this->sanitizeShort($request->input('utm_content')),
            'utm_term'    => $this->sanitizeShort($request->input('utm_term')),
        ]);

        Event::record('inquiry_submitted', $request, $inquiry->car_id, [
            'type' => $inquiry->type,
        ]);

        // Admin notification — track delivery status
        try {
            Mail::to('kontakt@certicars.pl')->send(new InquiryReceived($inquiry));
            $inquiry->update(['admin_email_sent_at' => now()]);
        } catch (\Throwable $e) {
            Log::warning('Inquiry admin email failed #'.$inquiry->id.': '.$e->getMessage());
            $inquiry->update([
                'admin_email_failed_at' => now(),
                'admin_email_error'     => substr($e->getMessage(), 0, 500),
            ]);
        }

        // Buyer confirmation — separate try so admin failure doesn't block it
        if (!empty($inquiry->email)) {
            try {
                Mail::to($inquiry->email)->send(new InquiryConfirmation($inquiry));
                $inquiry->update(['buyer_confirmation_sent_at' => now()]);
            } catch (\Throwable $e) {
                Log::warning('Inquiry buyer confirmation failed #'.$inquiry->id.': '.$e->getMessage());
                $inquiry->update([
                    'buyer_confirmation_failed_at' => now(),
                    'buyer_confirmation_error'     => substr($e->getMessage(), 0, 500),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Dziękujemy! Odezwiemy się jak najszybciej.',
        ]);
    }

    private function sanitizeShort(?string $value): ?string
    {
        if ($value === null || $value === '') return null;
        return substr(strip_tags($value), 0, 100);
    }

    private function sanitizeLong(?string $value): ?string
    {
        if ($value === null || $value === '') return null;
        return substr(strip_tags($value), 0, 500);
    }
}
