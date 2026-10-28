<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_actions', function (Blueprint $table) {
            $table->json('payload_template')->nullable()->after('push_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('notification_actions', function (Blueprint $table) {
            $table->dropColumn('payload_template');
        });
    }
};
