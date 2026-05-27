<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a JSON column for the 8 "highlighted equipment" tiles shown at the top
 * of the public Wyposażenie section. Stores up to 8 string keys referencing
 * entries in App\Helpers\EquipmentCatalog::OPTIONS. Existing cars get NULL —
 * the frontend handles missing data by hiding the highlighted row and showing
 * only the category cards built from $car->equipment.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->json('highlighted_equipment')->nullable()->after('equipment');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('highlighted_equipment');
        });
    }
};
