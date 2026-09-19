<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Arkusze klatek 360° (sprite sheets).
 *
 * Widok 360 ładował 60 osobnych JPEG-ów z r2.dev (HTTP/1.1, ~1 s na plik,
 * max 6 naraz) — ok. 11 s zanim klient mógł obrócić auto. Teraz klatki są
 * sklejane w kilka arkuszy + jeden mały podgląd całego obrotu. Tu trzymamy
 * układ arkuszy (rozmiary, liczba klatek na arkusz, nazwy plików, wersja
 * do omijania cache). null = arkuszy brak, strona używa pojedynczych klatek.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->json('interior_frames_meta')->nullable()->after('interior_frames_error');
            $table->json('exterior_frames_meta')->nullable()->after('exterior_frames_error');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['interior_frames_meta', 'exterior_frames_meta']);
        });
    }
};
