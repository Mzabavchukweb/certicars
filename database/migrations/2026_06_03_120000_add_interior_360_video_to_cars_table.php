<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Path to the original uploaded interior pan-around video on the
            // public disk (e.g. `cars/{id}/interior_video/{hash}.mp4`).
            $table->string('interior_video_path')->nullable()->after('engine_video_path');

            // Lifecycle of the frame extraction pipeline. Stays null until the
            // first upload; transitions pending → processing → ready|failed.
            $table->string('interior_frames_status', 16)->nullable()->after('interior_video_path');

            // Number of JPEG frames produced by ffmpeg. Used by the frontend to
            // pre-size the scrubber and to know which file names to request
            // (frame_001.jpg … frame_NNN.jpg).
            $table->unsignedSmallInteger('interior_frames_count')->nullable()->after('interior_frames_status');

            // Directory containing the extracted frames on the public disk
            // (e.g. `cars/{id}/interior_frames`). Kept explicit instead of
            // derived from car_id so a later re-extraction can move into a
            // versioned subdir without breaking older links.
            $table->string('interior_frames_dir')->nullable()->after('interior_frames_count');

            // Human-readable failure reason — surfaced to admins so they know
            // why the job failed without digging into queue logs.
            $table->string('interior_frames_error', 500)->nullable()->after('interior_frames_dir');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn([
                'interior_video_path',
                'interior_frames_status',
                'interior_frames_count',
                'interior_frames_dir',
                'interior_frames_error',
            ]);
        });
    }
};
