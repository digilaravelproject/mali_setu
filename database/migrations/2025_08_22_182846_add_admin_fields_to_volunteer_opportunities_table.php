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
        Schema::table('volunteer_opportunities', function (Blueprint $table) {
            $table->text('admin_notes')->nullable()->after('time_commitment');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('admin_notes');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteer_opportunities', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['admin_notes', 'reviewed_by', 'reviewed_at']);
        });
    }
};
