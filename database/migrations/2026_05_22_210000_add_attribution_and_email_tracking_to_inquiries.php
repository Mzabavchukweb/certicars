<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            // Attribution
            $table->string('form_source', 50)->nullable()->after('user_agent');
            $table->string('source_page', 500)->nullable()->after('form_source');
            $table->string('referrer', 500)->nullable()->after('source_page');
            $table->string('utm_source', 100)->nullable()->after('referrer');
            $table->string('utm_medium', 100)->nullable()->after('utm_source');
            $table->string('utm_campaign', 100)->nullable()->after('utm_medium');
            $table->string('utm_content', 100)->nullable()->after('utm_campaign');
            $table->string('utm_term', 100)->nullable()->after('utm_content');

            // Email delivery tracking
            $table->timestamp('admin_email_sent_at')->nullable()->after('utm_term');
            $table->timestamp('admin_email_failed_at')->nullable()->after('admin_email_sent_at');
            $table->string('admin_email_error', 500)->nullable()->after('admin_email_failed_at');
            $table->timestamp('buyer_confirmation_sent_at')->nullable()->after('admin_email_error');
            $table->timestamp('buyer_confirmation_failed_at')->nullable()->after('buyer_confirmation_sent_at');
            $table->string('buyer_confirmation_error', 500)->nullable()->after('buyer_confirmation_failed_at');
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'form_source', 'source_page', 'referrer',
                'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term',
                'admin_email_sent_at', 'admin_email_failed_at', 'admin_email_error',
                'buyer_confirmation_sent_at', 'buyer_confirmation_failed_at', 'buyer_confirmation_error',
            ]);
        });
    }
};
