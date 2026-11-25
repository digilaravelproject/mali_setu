<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('webinars', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('access_type');
            $table->decimal('price', 10, 2)->nullable()->after('is_paid');
        });

        Schema::table('webinar_registrations', function (Blueprint $table) {
            $table->foreignId('transaction_id')->nullable()->after('user_id')->constrained('transactions')->nullOnDelete();
            $table->timestamp('paid_at')->nullable()->after('transaction_id');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE transactions MODIFY purpose ENUM('business_registration','matrimony_profile','donation','webinar_registration') NOT NULL");
        }
    }

    public function down(): void
    {
        Schema::table('webinar_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('transaction_id');
            $table->dropColumn('paid_at');
        });
        Schema::table('webinars', fn (Blueprint $table) => $table->dropColumn(['is_paid', 'price']));
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE transactions MODIFY purpose ENUM('business_registration','matrimony_profile','donation') NOT NULL");
        }
    }
};
