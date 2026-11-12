<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessPlan;
use App\Models\MatrimonyPlan;
use App\Models\MatrimonyProfile;
use App\Models\Transaction;
use App\Services\AdminApprovalPaymentService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminApprovalPaymentServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'admin_payment_testing');
        config()->set('database.connections.admin_payment_testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => false,
        ]);
        DB::purge('admin_payment_testing');
        DB::setDefaultConnection('admin_payment_testing');

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('purpose');
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_order_id')->nullable();
            $table->string('status');
            $table->integer('subscription_period')->nullable();
            $table->string('receipt_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('payment_id')->unique();
            $table->string('order_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_type');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->string('status');
            $table->string('payment_method');
            $table->json('razorpay_response')->nullable();
            $table->json('metadata')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_reason')->nullable();
            $table->string('receipt_number')->nullable();
            $table->timestamps();
        });
        Schema::create('matrimony_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('approval_status')->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('profile_expires_at')->nullable();
            $table->timestamps();
        });
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('business_name');
            $table->string('verification_status')->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->string('subscription_status')->default('trial');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->timestamps();
        });
        Schema::create('matrimony_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name')->nullable();
            $table->integer('duration_years');
            $table->decimal('price', 10, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('business_plans', function (Blueprint $table) {
            $table->id();
            $table->string('company_type');
            $table->integer('duration_years');
            $table->decimal('price', 10, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_completed_matrimony_attempt_wins_over_a_newer_pending_attempt(): void
    {
        $profile = MatrimonyProfile::create(['user_id' => 10]);
        Transaction::create($this->transactionData(10, 'completed', 1));
        Transaction::create($this->transactionData(10, 'pending', 2));

        $profile->load(['matrimonyTransactions', 'matrimonyPayments']);

        $this->assertSame('paid', $profile->matrimony_payment_status);
        $this->assertSame('completed', $profile->matrimony_payment_record->status);
    }

    public function test_admin_can_add_matrimony_payment_and_fully_approve_profile(): void
    {
        Carbon::setTestNow('2026-11-12 10:00:00');
        $profile = MatrimonyProfile::create(['user_id' => 20]);
        $plan = MatrimonyPlan::create([
            'plan_name' => 'Standard', 'duration_years' => 2, 'price' => 1999, 'active' => true,
        ]);

        app(AdminApprovalPaymentService::class)->approveMatrimony($profile, $plan, 99);

        $profile->refresh();
        $this->assertSame('approved', $profile->approval_status);
        $this->assertSame(99, $profile->approved_by);
        $this->assertTrue($profile->profile_expires_at->equalTo(Carbon::parse('2028-11-12 10:00:00')));
        $this->assertDatabaseHas('payments', [
            'user_id' => 20,
            'payment_type' => 'matrimony_subscription',
            'amount' => 1999,
            'status' => 'completed',
            'payment_method' => 'other',
        ]);
    }

    public function test_admin_can_add_business_payment_and_fully_approve_business(): void
    {
        Carbon::setTestNow('2026-11-12 10:00:00');
        $business = Business::create(['user_id' => 30, 'business_name' => 'Example']);
        $plan = BusinessPlan::create([
            'company_type' => 'Private Ltd', 'duration_years' => 3, 'price' => 2499, 'active' => true,
        ]);

        app(AdminApprovalPaymentService::class)->approveBusiness($business, $plan, 99);

        $business->refresh();
        $this->assertSame('approved', $business->verification_status);
        $this->assertSame('active', $business->subscription_status);
        $this->assertSame(99, $business->verified_by);
        $this->assertTrue($business->subscription_expires_at->equalTo(Carbon::parse('2029-11-12 10:00:00')));
        $this->assertDatabaseHas('payments', [
            'user_id' => 30,
            'payment_type' => 'business_registration',
            'amount' => 2499,
            'status' => 'completed',
        ]);
    }

    private function transactionData(int $userId, string $status, int $sequence): array
    {
        return [
            'user_id' => $userId,
            'amount' => 249,
            'currency' => 'INR',
            'purpose' => 'matrimony_profile',
            'razorpay_order_id' => 'MAT-'.$sequence,
            'status' => $status,
            'subscription_period' => 12,
            'metadata' => [],
        ];
    }
}
