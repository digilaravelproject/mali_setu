<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('webinars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('banner_path')->nullable();
            $table->string('speaker_name')->nullable();
            $table->text('agenda')->nullable();
            $table->dateTime('starts_at');
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->string('agora_channel')->unique();
            $table->text('agora_link');
            $table->enum('audience', ['all', 'subscribers'])->default('all');
            $table->enum('status', ['draft', 'published', 'cancelled'])->default('published');
            $table->timestamps();
            $table->index(['status', 'starts_at']);
        });

        Schema::create('webinar_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['webinar_id', 'user_id']);
        });

        Schema::create('webinar_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('comment');
            $table->timestamps();
            $table->index(['webinar_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_comments');
        Schema::dropIfExists('webinar_registrations');
        Schema::dropIfExists('webinars');
    }
};
