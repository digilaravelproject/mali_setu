<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Transaction;
use App\Services\CCAvenue;
use App\Services\CCAvenuePaymentReconciliationService;
use App\Services\CCAvenuePaymentService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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
        config()->set('services.ccavenue.api_access_code', 'test-api-access-code');
        config()->set('services.ccavenue.api_working_key', 'test-api-working-key');

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
        Schema::create('matrimony_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name')->nullable();
            $table->integer('duration_years');
            $table->decimal('price', 10, 2);
            $table->boolean('active')->default(true);
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

    public function test_reconciliation_completes_shipped_order_using_plan_duration(): void
    {
        DB::table('matrimony_plans')->insert([
            'id' => 11,
            'plan_name' => 'Three years',
            'duration_years' => 3,
            'price' => 11,
            'active' => true,
        ]);
        DB::table('matrimony_profiles')->insert([
            'user_id' => 1,
            'approval_status' => 'pending',
        ]);

        $transaction = $this->transaction('MAT-test-1003', ['plan_id' => 11]);
        app(CCAvenuePaymentService::class)->createPendingPayment($transaction);

        $gateway = \Mockery::mock(CCAvenue::class);
        $gateway->shouldReceive('getOrderStatus')
            ->once()
            ->with('MAT-test-1003')
            ->andReturn([
                'order_status' => 'Shipped',
                'reference_no' => '114762776059',
                'order_amt' => '11.00',
                'order_currncy' => 'INR',
                'order_option_type' => 'Unified Payments - UPI',
            ]);

        $service = new CCAvenuePaymentReconciliationService(
            $gateway,
            app(CCAvenuePaymentService::class),
        );
        $stats = $service->reconcile();

        $this->assertSame(1, $stats['checked']);
        $this->assertSame(1, $stats['completed']);
        $this->assertSame('completed', $transaction->fresh()->status);
        $this->assertDatabaseHas('payments', [
            'transaction_id' => (string) $transaction->id,
            'payment_id' => '114762776059',
            'status' => 'completed',
        ]);

        $profile = DB::table('matrimony_profiles')->where('user_id', 1)->first();
        $this->assertSame('pending', $profile->approval_status);
        $this->assertEqualsWithDelta(now()->addYears(3)->timestamp, strtotime($profile->profile_expires_at), 5);
    }

    public function test_reconciliation_leaves_awaited_order_pending(): void
    {
        $transaction = $this->transaction('MAT-test-1004');

        $gateway = \Mockery::mock(CCAvenue::class);
        $gateway->shouldReceive('getOrderStatus')->once()->andReturn([
            'order_status' => 'Awaited',
        ]);

        $service = new CCAvenuePaymentReconciliationService(
            $gateway,
            app(CCAvenuePaymentService::class),
        );
        $stats = $service->reconcile();

        $this->assertSame(1, $stats['unchanged']);
        $this->assertSame('pending', $transaction->fresh()->status);
    }

    public function test_ccavenue_status_client_accepts_form_encoded_envelope(): void
    {
        config()->set('services.ccavenue.working_key', 'test-working-key');
        config()->set('services.ccavenue.access_code', 'test-access-code');
        config()->set('services.ccavenue.api_working_key', 'test-working-key');
        config()->set('services.ccavenue.api_access_code', 'test-access-code');
        config()->set('services.ccavenue.status_url', 'https://ccavenue.test/status');

        $gatewayResponse = json_encode([
            'order_status' => 'Shipped',
            'reference_no' => '114762776059',
            'order_amt' => '11.00',
            'order_currncy' => 'INR',
        ]);
        $encryptedResponse = $this->encryptCCAvenueResponse($gatewayResponse, 'test-working-key');

        Http::fake([
            'https://ccavenue.test/status' => Http::response(
                'status=0&enc_response='.$encryptedResponse,
                200,
                ['Content-Type' => 'text/plain'],
            ),
        ]);

        $status = app(CCAvenue::class)->getOrderStatus('BIZ-test-1005');

        $this->assertSame('Shipped', $status['order_status']);
        $this->assertSame('114762776059', $status['reference_no']);
        Http::assertSent(fn ($request) => $request['command'] === 'orderStatusTracker'
            && $request['access_code'] === 'test-access-code'
            && json_decode(
                $this->decryptCCAvenueRequest($request['enc_request'], 'test-working-key'),
                true,
            ) === ['order_no' => 'BIZ-test-1005']);
    }

    public function test_ccavenue_status_client_reports_gateway_error_details(): void
    {
        config()->set('services.ccavenue.working_key', 'test-working-key');
        config()->set('services.ccavenue.access_code', 'invalid-access-code');
        config()->set('services.ccavenue.api_working_key', 'test-working-key');
        config()->set('services.ccavenue.api_access_code', 'invalid-access-code');
        config()->set('services.ccavenue.status_url', 'https://ccavenue.test/status');

        Http::fake([
            'https://ccavenue.test/status' => Http::response(
                'status=1&enc_response=Access_code%3A+Invalid+Parameter&enc_error_code=51407',
                200,
                ['Content-Type' => 'text/plain'],
            ),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'CCAvenue rejected the order-status request (code 51407): Access_code: Invalid Parameter'
        );

        app(CCAvenue::class)->getOrderStatus('BIZ-test-1006');
    }

    public function test_ccavenue_status_client_uses_separate_api_credentials(): void
    {
        config()->set('services.ccavenue.working_key', 'checkout-working-key');
        config()->set('services.ccavenue.access_code', 'checkout-access-code');
        config()->set('services.ccavenue.api_working_key', 'api-working-key');
        config()->set('services.ccavenue.api_access_code', 'api-access-code');
        config()->set('services.ccavenue.status_url', 'https://ccavenue.test/status');

        $encryptedResponse = $this->encryptCCAvenueResponse(
            json_encode(['order_status' => 'Shipped']),
            'api-working-key',
        );
        Http::fake([
            'https://ccavenue.test/status' => Http::response(
                'status=0&enc_response='.$encryptedResponse,
                200,
            ),
        ]);

        app(CCAvenue::class)->getOrderStatus('BIZ-test-1007');

        Http::assertSent(fn ($request) => $request['access_code'] === 'api-access-code'
            && json_decode(
                $this->decryptCCAvenueRequest($request['enc_request'], 'api-working-key'),
                true,
            ) === ['order_no' => 'BIZ-test-1007']);
    }

    public function test_reconciliation_stops_before_polling_without_status_api_credentials(): void
    {
        config()->set('services.ccavenue.api_access_code');
        config()->set('services.ccavenue.api_working_key');
        $gateway = \Mockery::mock(CCAvenue::class);
        $gateway->shouldNotReceive('getOrderStatus');

        $service = new CCAvenuePaymentReconciliationService(
            $gateway,
            app(CCAvenuePaymentService::class),
        );
        $stats = $service->reconcile();

        $this->assertSame(0, $stats['checked']);
        $this->assertSame(1, $stats['errors']);
        $this->assertStringContainsString('CCAVENUE_API_ACCESS_CODE', $stats['last_error']);
    }

    private function encryptCCAvenueResponse(string $plainText, string $workingKey): string
    {
        $secretKey = hex2bin(md5($workingKey));
        $initialVector = pack('C*', ...range(0, 15));
        $encrypted = openssl_encrypt(
            $plainText,
            'AES-128-CBC',
            $secretKey,
            OPENSSL_RAW_DATA,
            $initialVector,
        );

        return bin2hex($encrypted);
    }

    private function decryptCCAvenueRequest(string $encryptedText, string $workingKey): string
    {
        $secretKey = hex2bin(md5($workingKey));
        $initialVector = pack('C*', ...range(0, 15));

        return openssl_decrypt(
            hex2bin($encryptedText),
            'AES-128-CBC',
            $secretKey,
            OPENSSL_RAW_DATA,
            $initialVector,
        );
    }

    private function transaction(string $orderId, array $metadata = []): Transaction
    {
        return Transaction::create([
            'user_id' => 1,
            'amount' => 11,
            'currency' => 'INR',
            'purpose' => 'matrimony_profile',
            'razorpay_order_id' => $orderId,
            'status' => 'pending',
            'subscription_period' => 12,
            'metadata' => $metadata,
        ]);
    }
}
