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
        // 1. Get all unique blog categories from existing blogs
        $existingBlogs = DB::table('blogs')->get();
        
        // 2. Insert any missing categories into blog_categories
        foreach ($existingBlogs as $blog) {
            $typeName = isset($blog->blog_type) ? trim($blog->blog_type) : null;
            if (!empty($typeName) && !is_numeric($typeName)) {
                // Check if category exists
                $exists = DB::table('blog_categories')->where('name', $typeName)->exists();
                if (!$exists) {
                    DB::table('blog_categories')->insert([
                        'name' => $typeName,
                        'description' => $typeName . ' Blog Category',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 3. Update existing blogs blog_type with the category ID
        $categories = DB::table('blog_categories')->get()->pluck('id', 'name');
        foreach ($existingBlogs as $blog) {
            $typeName = isset($blog->blog_type) ? trim($blog->blog_type) : null;
            if (!empty($typeName) && !is_numeric($typeName) && isset($categories[$typeName])) {
                DB::table('blogs')->where('id', $blog->id)->update([
                    'blog_type' => $categories[$typeName]
                ]);
            }
        }

        // 4. Change blog_type column to integer (unsigned big int, nullable)
        Schema::table('blogs', function (Blueprint $table) {
            $table->unsignedBigInteger('blog_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('blog_type')->nullable()->change();
        });
    }
};
