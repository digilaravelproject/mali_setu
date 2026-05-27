<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice_{{ $transaction->id }} — Mali Setu</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #2d3436;
            margin: 0;
            padding: 40px;
            background-color: #fff;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            padding: 30px;
            border-radius: 12px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f1f2f6;
            padding-bottom: 30px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #ff4757;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            color: #2d3436;
        }
        .invoice-title p {
            margin: 5px 0 0;
            color: #747d8c;
            font-size: 14px;
        }
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .meta-block h4 {
            margin: 0 0 8px;
            color: #747d8c;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .meta-block p {
            margin: 0;
            font-weight: 600;
            font-size: 15px;
        }
        .meta-block .val-ref {
            font-family: monospace;
            color: #57606f;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-bottom: 30px;
        }
        .details-table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 12px;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            color: #747d8c;
        }
        .details-table td {
            border-bottom: 1px solid #dee2e6;
            padding: 15px 12px;
            font-size: 15px;
        }
        .details-table .item-descr {
            font-weight: 600;
            color: #2d3436;
        }
        .details-table .item-period {
            font-size: 12px;
            color: #747d8c;
            display: block;
            margin-top: 4px;
        }
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }
        .totals-box {
            width: 300px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
            font-size: 15px;
        }
        .totals-row.grand-total {
            border-bottom: none;
            font-size: 18px;
            font-weight: 800;
            color: #ff4757;
            padding-top: 15px;
        }
        .footer {
            border-top: 2px solid #f1f2f6;
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #747d8c;
        }
        .print-btn {
            display: block;
            width: 150px;
            margin: 20px auto 0;
            padding: 10px 20px;
            background-color: #ff4757;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(255, 71, 87, 0.1);
        }
        @media print {
            body {
                padding: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
            }
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <div class="header">
        <div class="logo">Mali Setu</div>
        <div class="invoice-title">
            <h2>RECEIPT</h2>
            <p>Order Reference: #{{ $transaction->id }}</p>
        </div>
    </div>

    <div class="meta-info">
        <div class="meta-block">
            <h4>Billed To</h4>
            <p>{{ $transaction->user->name }}</p>
            <p style="font-weight: normal; color: #57606f; font-size:14px;">{{ $transaction->user->email }}</p>
            <p style="font-weight: normal; color: #57606f; font-size:14px;">{{ $transaction->user->phone }}</p>
        </div>
        <div class="meta-block" style="text-align: right;">
            <h4>Payment Details</h4>
            <p>Date: <span class="val-ref" style="font-family: inherit; font-size: inherit;">{{ $transaction->created_at->format('M d, Y') }}</span></p>
            <p>Razorpay ID: <span class="val-ref">{{ $transaction->razorpay_payment_id ?? $payment->payment_id ?? 'Direct Sync' }}</span></p>
            <p>Status: <span style="color: #2ec4b6; font-weight: 700;">SUCCESS</span></p>
        </div>
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th>Service Plan Description</th>
                <th style="text-align: right;">Base Amount</th>
                <th style="text-align: right;">Tax (0%)</th>
                <th style="text-align: right;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <span class="item-descr">
                        @if($transaction->purpose === 'business_registration')
                            Business Directory Premium Registration Plan
                        @elseif($transaction->purpose === 'matrimony_profile')
                            Matrimony Profile Gated Matchmaking Subscription
                        @else
                            Community Causes Donation Support
                        @endif
                    </span>
                    @if($transaction->subscription_period)
                        <span class="item-period">Validity Period: {{ $transaction->subscription_period }} Month(s)</span>
                    @endif
                </td>
                <td style="text-align: right;">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</td>
                <td style="text-align: right;">0.00</td>
                <td style="text-align: right; font-weight: 600;">{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="totals-section">
        <div class="totals-box">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>Tax / GST:</span>
                <span>0.00</span>
            </div>
            <div class="totals-row grand-total">
                <span>Grand Total Paid:</span>
                <span>{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer-generated transaction receipt. No signature is required.</p>
        <p>Mali Setu Community Platform • <a href="mailto:support@malisetu.org" style="color: #ff4757; text-decoration: none;">support@malisetu.org</a></p>
    </div>
</div>

<button onclick="window.print();" class="print-btn">Print Invoice</button>

<script>
    // Automatically trigger print overlay on load
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            window.print();
        }, 800);
    });
</script>

</body>
</html>
