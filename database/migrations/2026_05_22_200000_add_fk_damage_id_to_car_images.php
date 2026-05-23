<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Null out orphaned damage_id references before adding FK constraint.
        // Prevents MySQL from rejecting the constraint when production has rows
        // whose damage_id no longer exists in car_damages.
        DB::statement('
            UPDATE car_images
            SET damage_id = NULL
            WHERE damage_id IS NOT NULL
              AND damage_id NOT IN (SELECT id FROM car_damages)
        ');

        Schema::table('car_images', function (Blueprint $table) {
            $table->dropIndex(['damage_id']);
            $table->foreign('damage_id')
                ->references('id')->on('car_damages')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('car_images', function (Blueprint $table) {
            $table->dropForeign(['damage_id']);
            $table->index('damage_id');
        });
    }
};
