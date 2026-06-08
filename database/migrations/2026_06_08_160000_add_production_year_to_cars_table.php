<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Year the vehicle was actually built (distinct from
            // first_registration which is the registration date — often a
            // year+ after production for inventory cars). Surfaced on the
            // public car page as "Rok produkcji" and required by the new
            // wizard Step 1 layout.
            $table->unsignedSmallInteger('production_year')->nullable()->after('first_registration');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('production_year');
        });
    }
};
