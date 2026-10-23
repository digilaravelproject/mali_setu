<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromCollection, WithHeadings, WithStyles
{
    protected $payments;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        return $this->payments->map(function ($p) {
            return [
                'id' => $p->id,
                'user_name' => $p->user->name ?? 'N/A',
                'user_email' => $p->user->email ?? 'N/A',
                'amount' => $p->amount,
                'payment_type' => $p->payment_type ?? 'N/A',
                'status' => ucfirst($p->status),
                'razorpay_payment_id' => $p->razorpay_payment_id ?? '',
                'razorpay_order_id' => $p->razorpay_order_id ?? '',
                'created_at' => $p->created_at->format('Y-m-d H:i:s')
            ];
        });
    }

    public function headings(): array
    {
        return ['Payment ID', 'User Name', 'User Email', 'Amount', 'Payment Type', 'Status', 'Razorpay Payment ID', 'Razorpay Order ID', 'Created At'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]]
        ];
    }
}
