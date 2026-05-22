<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Proper document fields (previously misusing coc_documents, vehicle_folder, hu_au_report)
            $table->string('service_book_status', 100)->nullable()->after('hu_au_report');      // Książka serwisowa: Dostępna/Niedostępna/Elektroniczna/Częściowa
            $table->string('registration_cert', 100)->nullable()->after('service_book_status');  // Dowód rejestracyjny: Dostępny/Niedostępny
            $table->string('owners_manual', 100)->nullable()->after('registration_cert');        // Instrukcja obsługi: Dostępna/Niedostępna
            $table->string('aso_serviced', 100)->nullable()->after('owners_manual');             // Serwisowany w ASO: Tak/Nie/Częściowo/Brak danych
            $table->string('service_history', 100)->nullable()->after('aso_serviced');           // Historia serwisowa: Dostępna/Częściowo/Brak
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['service_book_status', 'registration_cert', 'owners_manual', 'aso_serviced', 'service_history']);
        });
    }
};
