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
        // Change column to text to support JSON arrays comfortably
        Schema::table('blogs', function (Blueprint $table) {
            $table->text('media_path')->nullable()->change();
        });

        // Convert existing records' media_path to JSON arrays if they are single strings
        $blogs = DB::table('blogs')->get();
        foreach ($blogs as $blog) {
            $path = $blog->media_path;
            if ($path && !str_starts_with($path, '[')) {
                DB::table('blogs')
                    ->where('id', $blog->id)
                    ->update(['media_path' => json_encode([$path])]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('media_path', 255)->nullable()->change();
        });
    }
};
