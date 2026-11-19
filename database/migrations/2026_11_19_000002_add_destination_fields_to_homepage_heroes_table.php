<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_heroes', function (Blueprint $table) {
            $table->string('functionality_name', 120)->nullable()->after('title');
            $table->string('event_name', 120)->nullable()->after('functionality_name');
            $table->string('external_url', 512)->nullable()->after('url');
        });

        DB::table('homepage_heroes')
            ->where(function ($query) {
                $query->where('url', 'like', 'http://%')
                    ->orWhere('url', 'like', 'https://%');
            })
            ->update([
                'external_url' => DB::raw('url'),
                'url' => null,
            ]);
    }

    public function down(): void
    {
        DB::table('homepage_heroes')
            ->whereNull('url')
            ->whereNotNull('external_url')
            ->update(['url' => DB::raw('external_url')]);

        Schema::table('homepage_heroes', function (Blueprint $table) {
            $table->dropColumn(['functionality_name', 'event_name', 'external_url']);
        });
    }
};
