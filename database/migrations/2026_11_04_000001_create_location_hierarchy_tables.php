<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique('name');
        });

        Schema::create('talukas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['district_id', 'name']);
        });

        Schema::create('localities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taluka_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['city', 'village']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['taluka_id', 'name', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localities');
        Schema::dropIfExists('talukas');
        Schema::dropIfExists('districts');
    }
};
