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
        Schema::create('matrimony_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('age');
            $table->string('height', 10)->nullable();
            $table->string('weight', 10)->nullable();
            $table->string('complexion', 50)->nullable();
            $table->string('physical_status', 50)->nullable();
            $table->json('personal_details')->nullable();
            $table->json('family_details')->nullable();
            $table->json('education_details')->nullable();
            $table->json('professional_details')->nullable();
            $table->json('lifestyle_details')->nullable();
            $table->json('location_details')->nullable();
            $table->json('partner_preferences')->nullable();
            $table->json('privacy_settings')->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('profile_expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id']);
            $table->index(['age']);
            $table->index(['approval_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matrimony_profiles');
    }
};
