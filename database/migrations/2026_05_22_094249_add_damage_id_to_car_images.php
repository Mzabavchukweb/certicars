<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_images', function (Blueprint $table) {
            $table->unsignedBigInteger('damage_id')->nullable()->after('car_id');
            $table->index('damage_id');
        });
    }

    public function down(): void
    {
        Schema::table('car_images', function (Blueprint $table) {
            $table->dropIndex(['damage_id']);
            $table->dropColumn('damage_id');
        });
    }
};
