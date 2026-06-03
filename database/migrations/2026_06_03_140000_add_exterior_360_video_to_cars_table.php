<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Mirror of the interior_video_* columns added one migration earlier
            // (see 2026_06_03_120000_add_interior_360_video_to_cars_table).
            // Same lifecycle, same shape — admin uploads a walk-around video,
            // ExtractExteriorFramesJob slices it into a JPEG sequence, and the
            // catalog page drag-scrubs through it (Copart-style exterior view).
            $table->string('exterior_video_path')->nullable()->after('interior_frames_error');
            $table->string('exterior_frames_status', 16)->nullable()->after('exterior_video_path');
            $table->unsignedSmallInteger('exterior_frames_count')->nullable()->after('exterior_frames_status');
            $table->string('exterior_frames_dir')->nullable()->after('exterior_frames_count');
            $table->string('exterior_frames_error', 500)->nullable()->after('exterior_frames_dir');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn([
                'exterior_video_path',
                'exterior_frames_status',
                'exterior_frames_count',
                'exterior_frames_dir',
                'exterior_frames_error',
            ]);
        });
    }
};
