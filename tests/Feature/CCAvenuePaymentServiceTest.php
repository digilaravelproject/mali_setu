<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Transaction;
use App\Services\CCAvenuePaymentService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CCAvenuePaymentServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'payment_testing');
        config()->set('database.connections.payment_testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        DB::purge('payment_testing');
        DB::setDefaultConnection('payment_testing');

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('purpose');
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_order_id')->nullable();
            $table->json('metadata');
            $table->string('status')->default('pending');
            $table->integer('subscription_period')->nullable();
            $table->string('receipt_url', 500)->nullable();
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
            $table->string('currency', 3)->default('INR');
            $table->string('status')->default('pending');
            $table->string('payment_method')->default('other');
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
            $table->timestamp('profile_expires_at')->nullable();
            $table->string('approval_status')->nullable();
            $table->timestamps();
        });
    }

    public function test_pending_order_has_a_payment_audit_row_and_can_complete_later(): void
    {
        $transaction = $this->transaction('MAT-test-1001');
        $service = app(CCAvenuePaymentService::class);

        $pendingPayment = $service->createPendingPayment($transaction);
        $this->assertSame('pending', $pendingPayment->status);
        $this->assertSame('pending:MAT-test-1001', $pendingPayment->payment_id);

        $pendingResult = $service->processCallback([
            'order_id' => 'MAT-test-1001',
            'order_status' => 'Awaited',
            'amount' => '11.00',
            'currency' => 'INR',
            'payment_mode' => 'Unified Payments - UPI',
        ]);
        $this->assertSame('pending', $pendingResult['status']);
        $this->assertSame('pending', $transaction->fresh()->status);

        $completedResult = $service->processCallback([
            'order_id' => 'MAT-test-1001',
            'tracking_id' => '114748828474',
            'order_status' => 'Success',
            'amount' => '11.00',
            'currency' => 'INR',
            'payment_mode' => 'Unified Payments - UPI',
        ]);

        $this->assertSame('completed', $completedResult['status']);
        $this->assertSame('completed', $transaction->fresh()->status);
        $this->assertDatabaseHas('payments', [
            'transaction_id' => (string) $transaction->id,
            'payment_id' => '114748828474',
            'status' => 'completed',
            'payment_method' => 'upi',
        ]);
        $this->assertSame(1, Payment::where('transaction_id', (string) $transaction->id)->count());
    }

    public function test_duplicate_or_late_callback_cannot_downgrade_completed_payment(): void
    {
        $transaction = $this->transaction('MAT-test-1002');
        $service = app(CCAvenuePaymentService::class);
        $service->createPendingPayment($transaction);

        $success = [
            'order_id' => 'MAT-test-1002',
            'tracking_id' => 'tracking-1002',
            'order_status' => 'Success',
            'amount' => '11.00',
            'currency' => 'INR',
            'payment_mode' => 'UPI',
        ];
        $service->processCallback($success);
        $service->processCallback($success);
        $lateFailure = $service->processCallback([
            'order_id' => 'MAT-test-1002',
            'order_status' => 'Failure',
        ]);

        $this->assertSame('completed', $lateFailure['status']);
        $this->assertSame('completed', $transaction->fresh()->status);
        $this->assertSame(1, Payment::where('transaction_id', (string) $transaction->id)->count());
    }

    private function transaction(string $orderId): Transaction
    {
        return Transaction::create([
            'user_id' => 1,
            'amount' => 11,
            'currency' => 'INR',
            'purpose' => 'matrimony_profile',
            'razorpay_order_id' => $orderId,
            'status' => 'pending',
            'subscription_period' => 12,
            'metadata' => [],
        ]);
    }
}
