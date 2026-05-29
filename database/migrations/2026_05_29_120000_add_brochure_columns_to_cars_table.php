<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Brochure state lives on the cars row so a public download is a single
 * indexed lookup and no work happens on the customer's click. Status
 * machine: missing → generating → ready | failed. The download endpoint
 * only serves when status='ready' AND the file exists on disk.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Storage path of the cached PDF (relative to the public disk).
            // Null when the brochure has never been generated or has been
            // explicitly invalidated.
            $table->string('brochure_path', 255)->nullable()->after('has_certicheck');

            // Status machine: missing | generating | ready | failed.
            $table->string('brochure_status', 16)->default('missing')->after('brochure_path');

            // Wall-clock of the last successful generation.
            $table->timestamp('brochure_generated_at')->nullable()->after('brochure_status');

            // Byte size of the cached PDF, for surfacing in admin and for
            // logging without re-reading the file.
            $table->unsignedInteger('brochure_size')->nullable()->after('brochure_generated_at');

            // Last failure message, truncated. Surfaced in admin so the
            // operator can see why a regeneration didn't work without
            // tailing Railway logs.
            $table->text('brochure_error')->nullable()->after('brochure_size');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn([
                'brochure_path',
                'brochure_status',
                'brochure_generated_at',
                'brochure_size',
                'brochure_error',
            ]);
        });
    }
};
