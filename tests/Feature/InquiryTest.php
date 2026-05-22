<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\Inquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    private function carWithBrand(): Car
    {
        $brand = Brand::create(['name' => 'Audi', 'slug' => 'audi']);
        return Car::create(['brand_id' => $brand->id, 'model' => 'A4', 'status' => 'active']);
    }

    public function test_inquiry_stored_and_admin_notified(): void
    {
        Mail::fake();
        $car = $this->carWithBrand();

        $this->post('/zapytanie', [
            'car_id'  => $car->id,
            'type'    => 'general',
            'name'    => 'Jan Kowalski',
            'phone'   => '+48 600 100 200',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('inquiries', ['phone' => '+48 600 100 200']);
        Mail::assertSent(\App\Mail\InquiryReceived::class);
    }

    public function test_confirmation_sent_when_email_provided(): void
    {
        Mail::fake();
        $car = $this->carWithBrand();

        $this->post('/zapytanie', [
            'car_id'  => $car->id,
            'type'    => 'general',
            'name'    => 'Jan Kowalski',
            'phone'   => '+48 600 100 200',
            'email'   => 'jan@example.pl',
        ])->assertOk();

        Mail::assertSent(\App\Mail\InquiryConfirmation::class, fn ($m) => $m->hasTo('jan@example.pl'));
    }

    public function test_no_confirmation_when_email_absent(): void
    {
        Mail::fake();
        $car = $this->carWithBrand();

        $this->post('/zapytanie', [
            'car_id'  => $car->id,
            'type'    => 'general',
            'name'    => 'Jan Kowalski',
            'phone'   => '+48 600 100 200',
        ])->assertOk();

        Mail::assertNotSent(\App\Mail\InquiryConfirmation::class);
    }

    public function test_honeypot_rejects_bot(): void
    {
        Mail::fake();
        $car = $this->carWithBrand();

        $this->post('/zapytanie', [
            'car_id'  => $car->id,
            'type'    => 'general',
            'name'    => 'Bot',
            'phone'   => '123',
            'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('inquiries', 0);
        Mail::assertNothingSent();
    }

    public function test_invalid_car_id_rejected(): void
    {
        $this->post('/zapytanie', [
            'car_id'  => 9999,
            'type'    => 'general',
            'name'    => 'Jan',
            'phone'   => '123456789',
        ])->assertSessionHasErrors('car_id');
    }

    public function test_inquiry_stores_utm_parameters(): void
    {
        Mail::fake();
        $car = $this->carWithBrand();

        $this->post('/zapytanie', [
            'car_id'       => $car->id,
            'type'         => 'general',
            'name'         => 'Jan Kowalski',
            'phone'        => '+48 600 100 200',
            'utm_source'   => 'facebook',
            'utm_medium'   => 'cpc',
            'utm_campaign' => 'summer_sale',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('inquiries', [
            'utm_source'   => 'facebook',
            'utm_medium'   => 'cpc',
            'utm_campaign' => 'summer_sale',
        ]);
    }

    public function test_inquiry_stores_referrer_and_source(): void
    {
        Mail::fake();
        $car = $this->carWithBrand();

        $this->post('/zapytanie', [
            'car_id'      => $car->id,
            'type'        => 'general',
            'name'        => 'Anna Nowak',
            'phone'       => '+48 700 200 300',
            'form_source' => 'main_car_cta',
            'source_page' => 'https://certicars.pl/samochody/audi-a4',
            'referrer'    => 'https://www.google.com/',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('inquiries', [
            'form_source' => 'main_car_cta',
            'referrer'    => 'https://www.google.com/',
        ]);
    }

    public function test_inquiry_records_admin_email_sent_at(): void
    {
        Mail::fake();
        $car = $this->carWithBrand();

        $this->post('/zapytanie', [
            'car_id' => $car->id,
            'type'   => 'general',
            'name'   => 'Jan Kowalski',
            'phone'  => '+48 600 100 200',
        ])->assertOk();

        $inquiry = Inquiry::latest()->first();
        $this->assertNotNull($inquiry->admin_email_sent_at);
        $this->assertNull($inquiry->admin_email_failed_at);
    }

    public function test_inquiry_records_buyer_confirmation_sent_at(): void
    {
        Mail::fake();
        $car = $this->carWithBrand();

        $this->post('/zapytanie', [
            'car_id' => $car->id,
            'type'   => 'general',
            'name'   => 'Jan Kowalski',
            'phone'  => '+48 600 100 200',
            'email'  => 'jan@example.pl',
        ])->assertOk();

        $inquiry = Inquiry::latest()->first();
        $this->assertNotNull($inquiry->buyer_confirmation_sent_at);
        $this->assertNull($inquiry->buyer_confirmation_failed_at);
    }

    public function test_inquiry_succeeds_when_buyer_confirmation_email_fails(): void
    {
        Mail::fake();
        Mail::shouldReceive('to->send')
            ->andReturnUsing(function () {
                static $call = 0;
                if (++$call >= 2) throw new \RuntimeException('SMTP error');
            });

        $car = $this->carWithBrand();

        $this->post('/zapytanie', [
            'car_id' => $car->id,
            'type'   => 'general',
            'name'   => 'Jan Kowalski',
            'phone'  => '+48 600 100 200',
            'email'  => 'jan@example.pl',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('inquiries', ['phone' => '+48 600 100 200']);
    }

    public function test_inquiry_records_admin_email_failed_at_on_error(): void
    {
        Mail::shouldReceive('to->send')->andThrow(new \RuntimeException('SMTP unavailable'));

        $car = $this->carWithBrand();

        $this->post('/zapytanie', [
            'car_id' => $car->id,
            'type'   => 'general',
            'name'   => 'Jan Kowalski',
            'phone'  => '+48 600 100 200',
        ])->assertOk()->assertJson(['success' => true]);

        $inquiry = Inquiry::latest()->first();
        $this->assertNotNull($inquiry->admin_email_failed_at);
        $this->assertStringContainsString('SMTP unavailable', $inquiry->admin_email_error);
    }
}
