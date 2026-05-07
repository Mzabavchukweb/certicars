<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Old seeder used 'exterior'/'interior', but the app expects 'gallery'/'damage'.
        // Normalize so galleryImages/damageImages relations work for existing rows.
        DB::table('car_images')->whereIn('type', ['exterior', 'interior'])->update(['type' => 'gallery']);
    }

    public function down(): void
    {
        // Irreversible normalization.
    }
};
