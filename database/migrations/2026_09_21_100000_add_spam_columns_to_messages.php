<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kwarantanna spamu. Wiadomości oznaczone jako spam nie trafiają do skrzynki
 * ani do licznika nieprzeczytanych — siedzą w zakładce „Spam”, skąd można je
 * skasować hurtem albo przywrócić (gdyby filtr się pomylił).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->boolean('is_spam')->default(false)->index();
            $table->unsignedSmallInteger('spam_score')->default(0);
            $table->json('spam_reasons')->nullable();
            $table->string('body_hash', 32)->nullable()->index();
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->boolean('is_spam')->default(false)->index();
            $table->unsignedSmallInteger('spam_score')->default(0);
            $table->json('spam_reasons')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['is_spam', 'spam_score', 'spam_reasons', 'body_hash']);
        });
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['is_spam', 'spam_score', 'spam_reasons']);
        });
    }
};
