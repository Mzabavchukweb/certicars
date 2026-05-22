<?php

namespace App\Http\Controllers;

use App\Mail\InquiryConfirmation;
use App\Mail\InquiryReceived;
use App\Models\Car;
use App\Models\Inquiry;
use Illuminate\Http\Request;
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
            'website' => 'max:0', // honeypot
        ]);

        $car = Car::with('brand')->find($validated['car_id']);

        $inquiry = Inquiry::create([
            'car_id'     => $validated['car_id'],
            'type'       => $validated['type'],
            'name'       => $validated['name'],
            'phone'      => $validated['phone'],
            'email'      => $validated['email'] ?? null,
            'message'    => $validated['message'] ?? null,
            'car_title'  => $car ? ($car->brand?->name . ' ' . $car->model . ' ' . $car->year) : null,
            'ip'         => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 500),
        ]);

        // Send email notification
        try {
            Mail::to('kontakt@certicars.pl')->send(new InquiryReceived($inquiry));

            if (!empty($inquiry->email)) {
                Mail::to($inquiry->email)->send(new InquiryConfirmation($inquiry));
            }
        } catch (\Exception $e) {
            // Log but don't fail the request
            \Log::warning('Inquiry email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Dziękujemy! Odezwiemy się jak najszybciej.',
        ]);
    }
}
