<?php

namespace App\Console\Commands;

use App\Services\CCAvenuePaymentReconciliationService;
use Illuminate\Console\Command;

class ReconcileCCAvenuePayments extends Command
{
    protected $signature = 'payment:reconcile-ccavenue';

    protected $description = 'Reconcile pending business and matrimony payments with CCAvenue';

    public function handle(CCAvenuePaymentReconciliationService $reconciliation): int
    {
        $stats = $reconciliation->reconcile();

        $this->info(sprintf(
            'CCAvenue reconciliation: checked %d, completed %d, unchanged %d, errors %d.',
            $stats['checked'],
            $stats['completed'],
            $stats['unchanged'],
            $stats['errors'],
        ));

        if ($stats['errors'] > 0 && $stats['last_error']) {
            $this->error('Last CCAvenue error: '.$stats['last_error']);
        }

        return self::SUCCESS;
    }
}
