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
        // 1. Add views_count to blogs table if it doesn't exist
        if (!Schema::hasColumn('blogs', 'views_count')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->integer('views_count')->default(0)->after('media_type');
            });
        }

        // 2. Recreate blog_likes table to make user_id nullable and add session_id
        Schema::dropIfExists('blog_likes');
        Schema::create('blog_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable();
            $table->timestamps();
            
            // Note: Enforce unique pairs in PHP logic to avoid complex SQLite constraint modifications
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('blogs', 'views_count')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropColumn('views_count');
            });
        }

        Schema::dropIfExists('blog_likes');
        Schema::create('blog_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained('blogs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['blog_id', 'user_id']);
        });
    }
};
