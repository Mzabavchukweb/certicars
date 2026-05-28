<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds two free-text columns surfaced in the public single-car "Dane pojazdu"
 * section so the layout matches the reference 4-column grid:
 *   - equipment_version → "Wersja wyposażenia" (e.g. "Initiale Paris")
 *   - drivetrain        → "Napęd"               (e.g. "Na przednie koła (FWD)")
 *
 * Both are nullable strings — existing cars stay unchanged; admins add values
 * via the wizard form when they want them surfaced.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('equipment_version', 120)->nullable()->after('transmission_detail');
            $table->string('drivetrain', 80)->nullable()->after('equipment_version');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['equipment_version', 'drivetrain']);
        });
    }
};
