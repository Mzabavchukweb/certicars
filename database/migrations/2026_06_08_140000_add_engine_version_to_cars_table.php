<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Engine variant string surfaced as "Wersja / silnik" on the
            // public car page (e.g. "1.6 dCi 160 KM EDC"). Distinct from
            // transmission_detail (gearbox tag) and equipment_version
            // (trim level), which were getting mixed into one cell.
            $table->string('engine_version', 120)->nullable()->after('engine_capacity');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('engine_version');
        });
    }
};
