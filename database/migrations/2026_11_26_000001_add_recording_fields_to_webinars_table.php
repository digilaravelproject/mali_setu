<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->string('recording_path')->nullable()->after('allow_recording');
            $table->timestamp('recorded_at')->nullable()->after('recording_path');
        });
    }

    public function down(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->dropColumn(['recording_path', 'recorded_at']);
        });
    }
};
