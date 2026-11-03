<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->json('categories')->nullable()->after('category');
        });

        DB::table('webinars')->select('id', 'category')->orderBy('id')->chunkById(100, function ($webinars) {
            foreach ($webinars as $webinar) {
                DB::table('webinars')->where('id', $webinar->id)->update([
                    'categories' => json_encode([$webinar->category ?: 'other']),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->dropColumn('categories');
        });
    }
};
