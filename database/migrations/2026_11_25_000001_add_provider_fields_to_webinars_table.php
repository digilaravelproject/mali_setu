<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->enum('meeting_provider', ['agora', 'daily'])->default('agora')->after('duration_minutes');
            $table->string('daily_room_name')->nullable()->unique()->after('agora_link');
            $table->text('daily_room_url')->nullable()->after('daily_room_name');
        });
    }

    public function down(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->dropUnique(['daily_room_name']);
            $table->dropColumn(['meeting_provider', 'daily_room_name', 'daily_room_url']);
        });
    }
};
