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
        Schema::create('volunteer_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_profile_id')->constrained('volunteer_profiles')->onDelete('cascade');
            $table->foreignId('volunteer_opportunity_id')->constrained('volunteer_opportunities')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'withdrawn'])->default('pending');
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            // Ensure a volunteer can only apply once per opportunity
            $table->unique(['volunteer_profile_id', 'volunteer_opportunity_id'], 'vol_app_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_applications');
    }
};
