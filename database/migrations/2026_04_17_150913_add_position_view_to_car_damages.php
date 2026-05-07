<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_damages', function (Blueprint $table) {
            $table->string('position_view', 20)->default('top')->after('position_y');
        });
    }

    public function down(): void
    {
        Schema::table('car_damages', function (Blueprint $table) {
            $table->dropColumn('position_view');
        });
    }
};
