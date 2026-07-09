<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Analityka v2.
 *
 * 1. `events` — jedna tabela na wszystkie zdarzenia interakcji (klik w
 *    telefon, pobranie PDF, otwarcie CertiCheck, ulubione, 360, lead).
 *    Zdarzenia lecą albo z serwera (kontrolery), albo z przeglądarki
 *    przez POST /zdarzenie z listą dozwolonych nazw.
 *
 * 2. `visitor_id` na page_views/car_views/events — trwałe ciasteczko
 *    (1 rok). Bez tego nie da się policzyć unikalnych odwiedzających
 *    ani nowych vs powracających; `session_id` ginie po zamknięciu
 *    przeglądarki.
 *
 * 3. `utm_*` na page_views — atrybucja liczona z PIERWSZEJ odsłony w
 *    sesji. Wcześniej UTM-y istniały tylko na `inquiries`, czyli znaliśmy
 *    źródło leada, ale nie źródło ruchu, który leada nie zostawił.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64);
            $table->foreignId('car_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 100)->nullable();
            $table->string('visitor_id', 36)->nullable();
            $table->string('path', 500)->nullable();
            $table->string('referer', 500)->nullable();
            $table->string('utm_source', 120)->nullable();
            $table->string('utm_medium', 120)->nullable();
            $table->string('utm_campaign', 120)->nullable();
            $table->string('device', 10)->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('name');
            $table->index('created_at');
            $table->index('visitor_id');
            $table->index(['name', 'created_at']);
        });

        Schema::table('page_views', function (Blueprint $table) {
            $table->string('visitor_id', 36)->nullable()->after('session_id');
            $table->string('device', 10)->nullable()->after('visitor_id');
            $table->string('utm_source', 120)->nullable()->after('referer');
            $table->string('utm_medium', 120)->nullable()->after('utm_source');
            $table->string('utm_campaign', 120)->nullable()->after('utm_medium');

            $table->index('visitor_id');
        });

        Schema::table('car_views', function (Blueprint $table) {
            $table->string('visitor_id', 36)->nullable()->after('session_id');
            $table->string('device', 10)->nullable()->after('visitor_id');

            $table->index('visitor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');

        Schema::table('page_views', function (Blueprint $table) {
            $table->dropIndex(['visitor_id']);
            $table->dropColumn(['visitor_id', 'device', 'utm_source', 'utm_medium', 'utm_campaign']);
        });

        Schema::table('car_views', function (Blueprint $table) {
            $table->dropIndex(['visitor_id']);
            $table->dropColumn(['visitor_id', 'device']);
        });
    }
};
