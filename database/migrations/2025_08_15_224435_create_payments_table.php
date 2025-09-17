<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payment_id')->unique(); // Razorpay payment ID
            $table->string('order_id')->nullable(); // Razorpay order ID
            $table->string('transaction_id')->nullable(); // Internal transaction ID
            $table->enum('payment_type', ['business_registration', 'matrimony_subscription', 'donation', 'other']);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['razorpay', 'upi', 'card', 'netbanking', 'wallet', 'other'])->default('razorpay');
            $table->json('razorpay_response')->nullable(); // Store Razorpay response
            $table->json('metadata')->nullable(); // Additional payment metadata
            $table->string('description')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_reason')->nullable();
            $table->string('receipt_number')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['payment_type', 'status']);
            $table->index('paid_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
