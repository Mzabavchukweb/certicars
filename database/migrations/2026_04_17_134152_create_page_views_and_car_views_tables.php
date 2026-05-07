<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('path', 500);
            $table->string('route_name', 100)->nullable();
            $table->string('session_id', 64)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('referer', 500)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('created_at');
            $table->index('route_name');
            $table->index(['session_id', 'path']);
        });

        Schema::create('car_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->string('session_id', 64)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('referer', 500)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('created_at');
            $table->index(['car_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_views');
        Schema::dropIfExists('page_views');
    }
};
