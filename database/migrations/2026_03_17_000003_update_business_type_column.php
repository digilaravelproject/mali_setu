<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert the existing column to a string column so we can store arbitrary business types.
        // This avoids issues when adding new values like "Public Ltd", "Private Ltd", and "Proprietary /Partnership - LLP".
        $column = DB::selectOne("SHOW COLUMNS FROM `businesses` WHERE Field = 'business_type'");
        if ($column && !str_contains(strtolower($column->Type), 'varchar')) {
            DB::statement('ALTER TABLE `businesses` MODIFY `business_type` VARCHAR(255) NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to enum with the original set of values.
        // Note: existing values not in this list will be converted to empty string (MySQL behavior).
        $column = DB::selectOne("SHOW COLUMNS FROM `businesses` WHERE Field = 'business_type'");
        if ($column && !str_contains(strtolower($column->Type), 'enum')) {
            DB::statement("ALTER TABLE `businesses` MODIFY `business_type` ENUM('product','service') NOT NULL");
        }
    }
};
