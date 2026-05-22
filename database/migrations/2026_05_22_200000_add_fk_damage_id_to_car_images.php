<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_images', function (Blueprint $table) {
            $table->dropIndex(['damage_id']);
            $table->foreign('damage_id')
                ->references('id')->on('car_damages')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('car_images', function (Blueprint $table) {
            $table->dropForeign(['damage_id']);
            $table->index('damage_id');
        });
    }
};
