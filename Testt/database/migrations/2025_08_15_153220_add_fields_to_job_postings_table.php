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
        Schema::table('job_postings', function (Blueprint $table) {
            $table->string('experience_level')->nullable()->after('location');
            $table->string('employment_type')->nullable()->after('experience_level');
            $table->string('category')->nullable()->after('employment_type');
            $table->json('skills_required')->nullable()->after('category');
            $table->json('benefits')->nullable()->after('skills_required');
            $table->timestamp('application_deadline')->nullable()->after('benefits');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('application_deadline');
            
            // Add indexes for better performance
            $table->index('category');
            $table->index('experience_level');
            $table->index('employment_type');
            $table->index('status');
            $table->index('application_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['experience_level']);
            $table->dropIndex(['employment_type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['application_deadline']);
            
            $table->dropColumn([
                'experience_level',
                'employment_type',
                'category',
                'skills_required',
                'benefits',
                'application_deadline',
                'status'
            ]);
        });
    }
};