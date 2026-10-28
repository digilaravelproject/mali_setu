<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_path', 500)->nullable()->change();
            $table->json('image_paths')->nullable()->after('image_path');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('image_path', 500)->nullable()->change();
            $table->json('image_paths')->nullable()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('image_paths'));
        Schema::table('services', fn (Blueprint $table) => $table->dropColumn('image_paths'));
    }
};
