<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_damages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->string('area');
            $table->string('severity')->default('warning');
            $table->string('type')->default('damage');
            $table->json('tags')->nullable();
            $table->text('description')->nullable();
            $table->decimal('position_x', 5, 2)->nullable();
            $table->decimal('position_y', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_damages');
    }
};
