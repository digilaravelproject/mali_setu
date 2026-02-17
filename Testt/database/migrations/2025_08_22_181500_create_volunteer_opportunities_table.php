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
        Schema::create('volunteer_opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('organization');
            $table->string('location');
            $table->json('required_skills')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('volunteers_needed');
            $table->integer('volunteers_registered')->default(0);
            $table->enum('status', ['active', 'inactive', 'completed', 'cancelled'])->default('active');
            $table->string('contact_person');
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->text('requirements')->nullable();
            $table->string('time_commitment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_opportunities');
    }
};
