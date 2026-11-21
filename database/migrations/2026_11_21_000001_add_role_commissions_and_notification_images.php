<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteer_referral_profiles', function (Blueprint $table) {
            $table->string('commission_mode', 20)->default('default')->after('commission_points');
            $table->json('role_commission_points')->nullable()->after('commission_mode');
        });

        Schema::table('notification_actions', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('external_url');
        });
    }

    public function down(): void
    {
        Schema::table('notification_actions', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });

        Schema::table('volunteer_referral_profiles', function (Blueprint $table) {
            $table->dropColumn(['commission_mode', 'role_commission_points']);
        });
    }
};
