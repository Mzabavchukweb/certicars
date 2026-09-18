<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rejestr błędów widoczny w panelu admina (Admin → Rejestr błędów).
 *
 * Trafia tu każdy wyjątek zgłoszony przez aplikację (bootstrap/app.php),
 * każde nieudane zapisanie auta (walidacja, baza, pliki), nieudane
 * wgrywanie plików oraz błędy zgłoszone z przeglądarki przez kreator.
 * Do tej pory te informacje lądowały tylko w logach Railway, do których
 * operator nie ma wygodnego dostępu — „ogłoszenie zniknęło i nie wiadomo czemu”.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('level', 12)->default('error');      // error | warning | info
            $table->string('source', 64);                       // car.store, upload.chunk, exception, client…
            $table->string('message', 1000);
            $table->json('context')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('car_id')->nullable();   // bez FK: auto mogło się nie zapisać
            $table->string('url', 500)->nullable();
            $table->string('method', 8)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 300)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['created_at']);
            $table->index(['source', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
