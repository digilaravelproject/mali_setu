<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'dob')) {
            Schema::table('users', function (Blueprint $table) {
                $table->date('dob')->nullable()->after('occupation');
            });
        }
    }

    public function down(): void
    {
        // This migration may be applied to an older database that already owned
        // the column, so it intentionally does not remove user profile data.
    }
};
