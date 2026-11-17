<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteer_referral_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('commission_points')->default(2)->change();
        });

        Schema::table('referrals', function (Blueprint $table) {
            $table->unsignedBigInteger('commission_points')->default(0)->change();
        });

        Schema::table('notification_actions', function (Blueprint $table) {
            $table->string('external_url', 2048)->nullable()->after('action_url');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->string('external_url', 2048)->nullable()->after('action_url');
            $table->timestamp('push_attempted_at')->nullable()->after('push_sent_at');
            $table->text('push_error')->nullable()->after('push_attempted_at');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['external_url', 'push_attempted_at', 'push_error']);
        });

        Schema::table('notification_actions', function (Blueprint $table) {
            $table->dropColumn('external_url');
        });

        Schema::table('volunteer_referral_profiles', function (Blueprint $table) {
            $table->unsignedTinyInteger('commission_points')->default(2)->change();
        });

        Schema::table('referrals', function (Blueprint $table) {
            $table->unsignedTinyInteger('commission_points')->default(0)->change();
        });
    }
};
