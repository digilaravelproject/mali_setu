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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->enum('business_type', ['product', 'service']);
            $table->foreignId('category_id')->constrained('business_categories');
            $table->text('description');
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('website')->nullable();
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->json('photo')->nullable();
            $table->enum('subscription_status', ['active', 'expired', 'trial'])->default('trial');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->integer('job_posting_limit')->default(0);
            $table->timestamps();
            
            $table->index(['user_id']);
            $table->index(['category_id']);
            $table->index(['verification_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
