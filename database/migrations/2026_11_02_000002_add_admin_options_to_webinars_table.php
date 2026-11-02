<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->enum('category', ['business', 'matrimony', 'other'])->default('other')->after('title');
            $table->boolean('require_registration')->default(true)->after('audience');
            $table->boolean('allow_qa')->default(true)->after('require_registration');
            $table->boolean('allow_recording')->default(false)->after('allow_qa');
            $table->enum('access_type', ['free', 'specific_plans'])->default('free')->after('allow_recording');
            $table->json('plan_ids')->nullable()->after('access_type');
        });
    }

    public function down(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->dropColumn(['category', 'require_registration', 'allow_qa', 'allow_recording', 'access_type', 'plan_ids']);
        });
    }
};
