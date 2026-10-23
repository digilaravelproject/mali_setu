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
        Schema::create('matrimony_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name')->nullable();
            $table->integer('duration_years');
            $table->decimal('price', 10, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Seed default matrimony plans (placeholders; admin can edit)
        DB::table('matrimony_plans')->insert([
            ['plan_name' => 'Basic', 'duration_years' => 1, 'price' => 1499.00, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['plan_name' => 'Standard', 'duration_years' => 2, 'price' => 1999.00, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['plan_name' => 'Premium', 'duration_years' => 3, 'price' => 2499.00, 'active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matrimony_plans');
    }
};
