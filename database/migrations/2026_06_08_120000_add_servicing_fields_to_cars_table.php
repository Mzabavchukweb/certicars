<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Fields the new car-detail "Serwisowanie" card needs but the
            // schema didn't have yet. All nullable so existing rows render
            // a muted dash; admin can fill them per car.
            $table->string('odometer_status', 120)->nullable()->after('mileage');
            $table->string('de_tech_valid_until', 32)->nullable()->after('next_inspection');
            $table->string('service_confirmation_type', 120)->nullable()->after('service_documentation');
            $table->string('last_service_scope', 200)->nullable()->after('last_service_mileage');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn([
                'odometer_status',
                'de_tech_valid_until',
                'service_confirmation_type',
                'last_service_scope',
            ]);
        });
    }
};
