<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->cascadeOnDelete();
            $table->string('model');
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->string('identifier')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency', 5)->default('PLN');
            $table->string('price_type')->nullable();

            $table->string('seller_name')->nullable();
            $table->string('seller_phone')->nullable();
            $table->string('seller_email')->nullable();
            $table->text('commission_note')->nullable();
            $table->date('reception_date')->nullable();

            $table->string('color')->nullable();
            $table->string('color_code')->nullable();
            $table->string('doors')->nullable();
            $table->integer('seats')->nullable();
            $table->integer('weight')->nullable();
            $table->string('upholstery')->nullable();
            $table->string('vin')->nullable();
            $table->string('body_type')->nullable();

            $table->string('first_registration')->nullable();
            $table->integer('mileage')->nullable();
            $table->integer('previous_owners')->nullable();
            $table->string('business_use')->nullable();
            $table->integer('number_of_keys')->nullable();

            $table->string('fuel_type')->nullable();
            $table->integer('power_hp')->nullable();
            $table->integer('power_kw')->nullable();
            $table->integer('engine_capacity')->nullable();
            $table->string('transmission')->nullable();
            $table->string('transmission_detail')->nullable();

            $table->string('location')->nullable();
            $table->string('location_distance')->nullable();
            $table->string('source')->nullable();
            $table->boolean('is_imported')->default(false);
            $table->string('country_registration')->nullable();
            $table->string('taxation')->nullable();

            $table->string('last_service')->nullable();
            $table->string('last_service_mileage')->nullable();
            $table->string('next_inspection')->nullable();
            $table->string('service_documentation')->nullable();

            $table->string('fuel_consumption')->nullable();
            $table->string('fuel_procedure')->nullable();
            $table->string('co2_emission')->nullable();
            $table->string('emission_class')->nullable();

            $table->string('service_book')->nullable();
            $table->string('coc_documents')->nullable();
            $table->string('vehicle_folder')->nullable();
            $table->string('hu_au_report')->nullable();

            $table->json('paint_measurements')->nullable();
            $table->json('technical_conditions')->nullable();
            $table->json('equipment')->nullable();
            $table->string('engine_video_url')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_sold')->default(false);
            $table->string('status')->default('draft');

            $table->timestamps();

            $table->index('slug');
            $table->index('status');
            $table->index(['is_sold', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
