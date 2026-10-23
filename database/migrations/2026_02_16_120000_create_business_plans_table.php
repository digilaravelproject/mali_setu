<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_plans', function (Blueprint $table) {
            $table->id();
            $table->string('company_type');
            $table->integer('duration_years');
            $table->decimal('price', 10, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Seed default plans
        DB::table('business_plans')->insert([
            // Proprietary / Partnership / LLP
            ['company_type' => 'Proprietary/Partnership - LLP', 'duration_years' => 1, 'price' => 999.00, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_type' => 'Proprietary/Partnership - LLP', 'duration_years' => 2, 'price' => 1499.00, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_type' => 'Proprietary/Partnership - LLP', 'duration_years' => 3, 'price' => 1999.00, 'active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Private Ltd
            ['company_type' => 'Private Ltd', 'duration_years' => 1, 'price' => 1499.00, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_type' => 'Private Ltd', 'duration_years' => 2, 'price' => 1999.00, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_type' => 'Private Ltd', 'duration_years' => 3, 'price' => 2499.00, 'active' => true, 'created_at' => now(), 'updated_at' => now()],

            // Public Ltd
            ['company_type' => 'Public Ltd', 'duration_years' => 1, 'price' => 3999.00, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_type' => 'Public Ltd', 'duration_years' => 2, 'price' => 4999.00, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['company_type' => 'Public Ltd', 'duration_years' => 3, 'price' => 5999.00, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_plans');
    }
};
