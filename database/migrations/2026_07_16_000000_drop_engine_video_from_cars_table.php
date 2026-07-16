<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            foreach (['engine_video_path', 'engine_video_url'] as $col) {
                if (Schema::hasColumn('cars', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            if (!Schema::hasColumn('cars', 'engine_video_url')) {
                $table->string('engine_video_url')->nullable();
            }
            if (!Schema::hasColumn('cars', 'engine_video_path')) {
                $table->string('engine_video_path', 500)->nullable()->after('engine_video_url');
            }
        });
    }
};
