<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Throwable;

class CCAvenuePaymentReconciliationService
{
    public function __construct(
        private readonly CCAvenue $ccavenue,
        private readonly CCAvenuePaymentService $payments,
    ) {}

    /**
     * @return array{checked: int, completed: int, failed: int, unchanged: int, errors: int, last_error: ?string}
     */
    public function reconcile(): array
    {
        $stats = ['checked' => 0, 'completed' => 0, 'failed' => 0, 'unchanged' => 0, 'errors' => 0, 'last_error' => null];

        $statusAccessCode = config('services.ccavenue.api_access_code')
            ?: config('services.ccavenue.access_code');
        $statusWorkingKey = config('services.ccavenue.api_working_key')
            ?: config('services.ccavenue.working_key');

        if (blank($statusAccessCode) || blank($statusWorkingKey)) {
            $stats['errors'] = 1;
            $stats['last_error'] = 'CCAvenue Status API credentials are not configured. '
                .'Set CCAVENUE_ACCESS_CODE and CCAVENUE_WORKING_KEY, or the dedicated '
                .'CCAVENUE_API_ACCESS_CODE and CCAVENUE_API_WORKING_KEY values.';

            return $stats;
        }

        $lookbackDays = max(1, (int) config('services.ccavenue.reconcile_lookback_days', 7));
        $batchSize = max(1, min(500, (int) config('services.ccavenue.reconcile_batch_size', 100)));

        $transactions = Transaction::query()
            ->where('status', 'pending')
            ->whereIn('purpose', ['business_registration', 'matrimony_profile', 'donation'])
            ->whereNotNull('razorpay_order_id')
            ->where('created_at', '>=', now()->subDays($lookbackDays))
            ->oldest('id')
            ->limit($batchSize)
            ->get();

        foreach ($transactions as $transaction) {
            $stats['checked']++;

            try {
                $gatewayStatus = $this->ccavenue->getOrderStatus($transaction->razorpay_order_id);

                $status = $this->payments->normalizeGatewayStatus($gatewayStatus['order_status'] ?? null);

                if ($status === 'pending') {
                    $stats['unchanged']++;

                    continue;
                }

                $this->payments->processCallback($this->callbackParameters($transaction, $gatewayStatus));
                $stats[$status]++;
            } catch (Throwable $exception) {
                $stats['errors']++;
                $stats['last_error'] = $exception->getMessage();
                Log::warning('CCAvenue payment reconciliation failed', [
                    'transaction_id' => $transaction->id,
                    'order_id' => $transaction->razorpay_order_id,
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $stats;
    }

    /**
     * @param  array<string, mixed>  $status
     * @return array<string, mixed>
     */
    private function callbackParameters(Transaction $transaction, array $status): array
    {
        return [
            'order_id' => $transaction->razorpay_order_id,
            'tracking_id' => $status['reference_no'] ?? $status['tracking_id'] ?? null,
            // Preserve the authoritative gateway state. The payment service
            // normalizes Shipped/Successful and Unsuccessful/Aborted safely.
            'order_status' => $status['order_status'] ?? null,
            'amount' => $status['order_amt'] ?? $status['amount'] ?? null,
            'currency' => $status['order_currncy'] ?? $status['currency'] ?? null,
            'payment_mode' => $status['order_option_type'] ?? $status['order_card_name'] ?? '',
            'ccavenue_order_status_response' => $status,
        ];
    }
}