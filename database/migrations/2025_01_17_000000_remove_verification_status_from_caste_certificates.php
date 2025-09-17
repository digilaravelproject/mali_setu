<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('caste_certificates') && Schema::hasColumn('caste_certificates', 'verification_status')) {
            Schema::table('caste_certificates', function (Blueprint $table) {
                $table->dropColumn('verification_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('caste_certificates') && !Schema::hasColumn('caste_certificates', 'verification_status')) {
            Schema::table('caste_certificates', function (Blueprint $table) {
                $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending')->after('file_path');
            });
        }
    }
};