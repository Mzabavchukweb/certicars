<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    public function up(): void
    {
        if (app()->environment('testing')) {
            return;
        }
        Artisan::call('db:seed', ['--class' => 'AudiA4Seeder', '--force' => true]);
    }

    public function down(): void
    {
        //
    }
};
