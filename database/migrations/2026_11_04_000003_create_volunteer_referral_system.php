<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'referred_by_user_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('referred_by_user_id')->nullable()->after('reffral_code')->constrained('users')->nullOnDelete();
            });
        }

        // The legacy field previously accepted a sponsor's code. From this
        // migration onward it is the account owner's permanent unique code.
        foreach (DB::table('users')->select('id')->orderBy('id')->cursor() as $user) {
            $code = 'MSU'.str_pad((string) $user->id, 7, '0', STR_PAD_LEFT);
            DB::table('users')->where('id', $user->id)->update(['reffral_code' => $code]);
        }

        try {
            Schema::table('users', fn (Blueprint $table) => $table->unique('reffral_code', 'users_reffral_code_unique'));
        } catch (Throwable) {
            // The unique index may already exist on installations updated manually.
        }

        Schema::create('volunteer_referral_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('commission_points')->default(2);
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('taluka_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('locality_id')->nullable()->constrained('localities')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('referral_code_used', 50);
            $table->unsignedTinyInteger('commission_points')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->index(['referrer_user_id', 'status']);
            $table->index('created_at');
        });

        $volunteerUserIds = DB::table('users')->where('user_type', 'volunteer')->pluck('id')
            ->merge(Schema::hasTable('volunteer_profiles') ? DB::table('volunteer_profiles')->pluck('user_id') : collect())
            ->unique();
        $now = now();
        foreach ($volunteerUserIds as $userId) {
            DB::table('volunteer_referral_profiles')->insertOrIgnore([
                'user_id' => $userId,
                'commission_points' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('volunteer_referral_profiles');
        try {
            Schema::table('users', fn (Blueprint $table) => $table->dropUnique('users_reffral_code_unique'));
        } catch (Throwable) {
        }
        if (Schema::hasColumn('users', 'referred_by_user_id')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('referred_by_user_id'));
        }
    }
};
