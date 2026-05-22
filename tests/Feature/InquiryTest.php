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
}
