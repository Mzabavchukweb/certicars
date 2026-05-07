<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('engine_video_path', 500)->nullable()->after('engine_video_url');
        });

        Schema::table('car_damages', function (Blueprint $table) {
            $table->string('image_path', 500)->nullable()->after('position_y');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('engine_video_path');
        });
        Schema::table('car_damages', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
