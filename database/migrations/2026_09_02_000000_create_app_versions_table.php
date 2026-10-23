<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 20)->unique();
            $table->string('version', 50);
            $table->unsignedBigInteger('build_code');
            $table->string('min_version', 50);
            $table->unsignedBigInteger('min_build');
            $table->text('store_url');
            $table->text('update_notes')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('app_versions')->insert([
            [
                'platform' => 'android',
                'version' => '1.0.0',
                'build_code' => 1,
                'min_version' => '1.0.0',
                'min_build' => 1,
                'store_url' => 'https://play.google.com/store/apps/details?id=com.malisetu.app',
                'update_notes' => 'Initial Mali Setu Android release.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'platform' => 'ios',
                'version' => '1.0.0',
                'build_code' => 1,
                'min_version' => '1.0.0',
                'min_build' => 1,
                'store_url' => 'https://apps.apple.com/in/app/mali-setu/id6766891247',
                'update_notes' => 'Initial Mali Setu iOS release.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
