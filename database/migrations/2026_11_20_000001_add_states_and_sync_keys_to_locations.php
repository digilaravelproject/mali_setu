<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('external_code', 30)->unique();
            $table->string('name', 120);
            $table->boolean('is_active')->default(true);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->foreignId('state_id')->nullable()->after('id')->constrained('states')->restrictOnDelete();
            $table->string('external_code', 30)->nullable()->after('state_id')->unique();
            $table->timestamp('synced_at')->nullable();
            $table->dropUnique('districts_name_unique');
            $table->unique(['state_id', 'name']);
        });
        Schema::table('talukas', function (Blueprint $table) {
            $table->string('external_code', 30)->nullable()->after('district_id')->unique();
            $table->timestamp('synced_at')->nullable();
        });
        Schema::table('localities', function (Blueprint $table) {
            $table->string('external_code', 30)->nullable()->after('taluka_id')->unique();
            $table->timestamp('synced_at')->nullable();
        });

        $legacyState = DB::table('states')->insertGetId([
            'external_code' => 'LEGACY', 'name' => 'Unassigned / Legacy', 'is_active' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('districts')->whereNull('state_id')->update(['state_id' => $legacyState]);
    }

    public function down(): void
    {
        Schema::table('localities', function (Blueprint $table) {
            $table->dropUnique(['external_code']);
            $table->dropColumn(['external_code', 'synced_at']);
        });
        Schema::table('talukas', function (Blueprint $table) {
            $table->dropUnique(['external_code']);
            $table->dropColumn(['external_code', 'synced_at']);
        });
        Schema::table('districts', function (Blueprint $table) {
            $table->dropUnique(['state_id', 'name']);
            $table->dropUnique(['external_code']);
            $table->dropConstrainedForeignId('state_id');
            $table->dropColumn(['external_code', 'synced_at']);
            $table->unique('name');
        });
        Schema::dropIfExists('states');
    }
};
