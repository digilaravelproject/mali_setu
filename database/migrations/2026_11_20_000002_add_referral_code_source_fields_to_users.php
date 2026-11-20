<?php

use App\Services\ReferralCodeService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name', 100)->nullable()->after('name');
            }
            if (! Schema::hasColumn('users', 'taluka')) {
                $table->string('taluka', 100)->nullable()->after('district');
            }
        });

        app(ReferralCodeService::class)->refreshAll();
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'first_name')) {
                $table->dropColumn('first_name');
            }
            if (Schema::hasColumn('users', 'taluka')) {
                $table->dropColumn('taluka');
            }
        });
    }
};
