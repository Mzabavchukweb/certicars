<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('previous_owners', 50)->nullable()->change();
            $table->string('number_of_keys', 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->integer('previous_owners')->nullable()->change();
            $table->integer('number_of_keys')->nullable()->change();
        });
    }
};
