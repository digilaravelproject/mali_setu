<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BusinessTransactionsExport implements FromCollection, WithHeadings, WithStyles
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions->map(function ($t) {
            return [
                'id' => $t->id,
                'user_name' => $t->user->name ?? 'N/A',
                'user_email' => $t->user->email ?? 'N/A',
                'amount' => $t->amount,
                'purpose' => $t->purpose,
                'status' => ucfirst($t->status),
                'razorpay_payment_id' => $t->razorpay_payment_id ?? '',
                'razorpay_order_id' => $t->razorpay_order_id ?? '',
                'created_at' => $t->created_at->format('Y-m-d H:i:s')
            ];
        });
    }

    public function headings(): array
    {
        return ['Transaction ID', 'User Name', 'User Email', 'Amount', 'Purpose', 'Status', 'Razorpay Payment ID', 'Razorpay Order ID', 'Created At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]]
        ];
    }
}
