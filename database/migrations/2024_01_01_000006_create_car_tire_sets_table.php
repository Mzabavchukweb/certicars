<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_tire_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->integer('set_number')->default(1);
            $table->boolean('is_mounted')->default(true);
            $table->string('tire_type')->nullable();
            $table->string('rim')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('car_tires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_tire_set_id')->constrained()->cascadeOnDelete();
            $table->string('position');
            $table->string('tread_depth')->nullable();
            $table->json('condition')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_tires');
        Schema::dropIfExists('car_tire_sets');
    }
};
