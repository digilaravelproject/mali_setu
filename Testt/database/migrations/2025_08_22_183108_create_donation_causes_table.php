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
        Schema::create('donation_causes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->decimal('target_amount', 10, 2);
            $table->decimal('raised_amount', 10, 2)->default(0);
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->string('location')->nullable();
            $table->string('organization');
            $table->json('contact_info'); // {email, phone, address}
            $table->string('image_url')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'active', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_causes');
    }
};
