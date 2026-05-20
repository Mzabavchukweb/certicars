<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->boolean('available_now')->default(false)->after('has_certicheck');
            $table->boolean('home_delivery')->default(false)->after('available_now');
            $table->boolean('has_gethelp')->default(false)->after('home_delivery');
            $table->string('gethelp_package')->nullable()->after('has_gethelp');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['available_now', 'home_delivery', 'has_gethelp', 'gethelp_package']);
        });
    }
};
