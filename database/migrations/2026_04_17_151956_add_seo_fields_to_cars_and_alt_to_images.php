<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('meta_title', 180)->nullable()->after('status');
            $table->string('meta_description', 320)->nullable()->after('meta_title');
            $table->string('focus_keyword', 120)->nullable()->after('meta_description');
            $table->boolean('noindex')->default(false)->after('focus_keyword');
        });

        Schema::table('car_images', function (Blueprint $table) {
            $table->string('alt_text', 255)->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'focus_keyword', 'noindex']);
        });
        Schema::table('car_images', function (Blueprint $table) {
            $table->dropColumn('alt_text');
        });
    }
};
