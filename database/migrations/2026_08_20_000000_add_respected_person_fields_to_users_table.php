<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('respected_person_name')->nullable()->after('cast_certificate');
            $table->string('respected_person_mobile_number', 20)->nullable()->after('respected_person_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'respected_person_name',
                'respected_person_mobile_number',
            ]);
        });
    }
};
