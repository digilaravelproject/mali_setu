<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Summary - Mali Setu</title>
    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #84144f;
            --primary-dark: #630837;
            --accent: #aa1262;
            --success-color: #10b981;
            --bg-gradient: linear-gradient(135deg, #fdfbf7 0%, #f4f3f0 100%);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #2d3748;
        }

        .summary-container {
            width: 100%;
            max-width: 580px;
            perspective: 1000px;
        }

        .summary-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(132, 20, 79, 0.1);
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(132, 20, 79, 0.05), 0 1px 3px rgba(0, 0, 0, 0.02);
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: cardAppear 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        @keyframes cardAppear {
            0% {
                opacity: 0;
                transform: translateY(30px) rotateX(-5deg);
            }
            100% {
                opacity: 1;
                transform: translateY(0) rotateX(0deg);
            }
        }

        /* Success Icon Animation */
        .success-checkmark {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 50%;
            color: var(--success-color);
            font-size: 2.5rem;
            animation: pulseSuccess 2s infinite ease-in-out;
        }

        @keyframes pulseSuccess {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }
            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 15px rgba(16, 185, 129, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .title {
            font-weight: 800;
            font-size: 1.75rem;
            color: #1a202c;
            margin-bottom: 8px;
        }

        .amount-display {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 24px;
            font-family: monospace;
        }

        /* Details list styling */
        .details-box {
            background: #faf9f6;
            border: 1px solid rgba(0, 0, 0, 0.04);
            border-radius: 18px;
            padding: 20px;
            text-align: left;
            margin-bottom: 28px;
        }

        .details-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            border-bottom: 1.5px solid rgba(0, 0, 0, 0.04);
            padding-bottom: 8px;
        }

        .details-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-size: 0.92rem;
        }

        .details-row:last-child {
            margin-bottom: 0;
        }

        .details-label {
            color: #718096;
            font-weight: 500;
        }

        .details-value {
            color: #2d3748;
            font-weight: 700;
            text-align: right;
        }

        /* Purpose section styling */
        .purpose-box {
            border: 1.5px dashed rgba(132, 20, 79, 0.2);
            background: rgba(132, 20, 79, 0.02);
            border-radius: 18px;
            padding: 20px;
            text-align: left;
            margin-bottom: 32px;
        }

        .purpose-title {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--primary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back {
            background: linear-gradient(135deg, #84144f 0%, #aa1262 100%);
            border: none;
            color: #fff;
            padding: 14px 32px;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 50px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 20px rgba(132, 20, 79, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            width: 100%;
        }

        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(132, 20, 79, 0.3);
            color: #fff;
        }

        .btn-back:active {
            transform: translateY(-1px);
        }

        .footer-note {
            font-size: 0.8rem;
            color: #a0aec0;
            margin-top: 24px;
        }

        /* Hide on mobile */
        .desktop-only {
            display: none;
        }

        /* Show on desktop */
        @media (min-width: 768px) {
            .desktop-only {
                display: inline-block;
            }
        }
    </style>
</head>
<body>

<div class="summary-container">
    <div class="summary-card">
        <!-- Success Icon -->
        <div class="success-checkmark">
            <i class="fa-solid fa-check"></i>
        </div>

        <h1 class="title">Payment Successful</h1>
        <p class="text-secondary small mb-3">Your transaction has been completed successfully.</p>
        
        <div class="amount-display">
            {{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}
        </div>

        <!-- Transaction Details -->
        <div class="details-box">
            <div class="details-title">Transaction Details</div>
            
            <div class="details-row">
                <span class="details-label">Order ID</span>
                <span class="details-value">{{ $transaction->razorpay_order_id }}</span>
            </div>

            @if($transaction->razorpay_payment_id)
            <div class="details-row">
                <span class="details-label">Payment ID</span>
                <span class="details-value">{{ $transaction->razorpay_payment_id }}</span>
            </div>
            @endif

            <div class="details-row">
                <span class="details-label">Payment Method</span>
                <span class="details-value text-uppercase">{{ $payment_method }}</span>
            </div>

            <div class="details-row">
                <span class="details-label">Date & Time</span>
                <span class="details-value">{{ $transaction->updated_at->format('d M Y, h:i A') }}</span>
            </div>
        </div>

        <!-- Purpose Specific details -->
        @if(!empty($details))
            <div class="purpose-box">
                @if($details['type'] === 'business')
                    <div class="purpose-title">
                        <i class="fa-solid fa-briefcase"></i> {{ $details['title'] }}
                    </div>
                    <div class="details-row">
                        <span class="details-label">Business Name</span>
                        <span class="details-value">{{ $details['name'] }}</span>
                    </div>
                    <div class="details-row">
                        <span class="details-label">Duration</span>
                        <span class="details-value">{{ $details['period'] }}</span>
                    </div>
                    <div class="details-row">
                        <span class="details-label">Expires On</span>
                        <span class="details-value text-primary fw-bold">{{ $details['expires_at'] }}</span>
                    </div>

                @elseif($details['type'] === 'matrimony')
                    <div class="purpose-title">
                        <i class="fa-solid fa-heart"></i> {{ $details['title'] }}
                    </div>
                    <div class="details-row">
                        <span class="details-label">Profile Name</span>
                        <span class="details-value">{{ $details['name'] }}</span>
                    </div>
                    <div class="details-row">
                        <span class="details-label">Duration</span>
                        <span class="details-value">{{ $details['period'] }}</span>
                    </div>
                    <div class="details-row">
                        <span class="details-label">Expires On</span>
                        <span class="details-value text-primary fw-bold">{{ $details['expires_at'] }}</span>
                    </div>

                @elseif($details['type'] === 'donation')
                    <div class="purpose-title">
                        <i class="fa-solid fa-hand-holding-heart"></i> {{ $details['title'] }}
                    </div>
                    <div class="details-row">
                        <span class="details-label">Cause</span>
                        <span class="details-value">{{ $details['cause_title'] }}</span>
                    </div>
                    <div class="details-row">
                        <span class="details-label">Organization</span>
                        <span class="details-value">{{ $details['organization'] }}</span>
                    </div>
                    <div class="details-row">
                        <span class="details-label">Donor Privacy</span>
                        <span class="details-value">
                            @if($details['is_anonymous'])
                                <span class="badge bg-secondary">Anonymous</span>
                            @else
                                <span class="badge bg-info text-dark">Public</span>
                            @endif
                        </span>
                    </div>
                    @if($details['message'])
                        <div class="mt-3 pt-3 border-top" style="border-top: 1px solid rgba(0,0,0,0.05) !important;">
                            <span class="details-label d-block mb-1">Donor Message:</span>
                            <span class="text-secondary small italic">"{{ $details['message'] }}"</span>
                        </div>
                    @endif
                @endif
            </div>
        @endif

        <!-- Action Button -->
        <a href="{{ route('payment.summary.back', ['order_id' => $order_id]) }}"
        class="btn-back desktop-only">
            <i class="fa-solid fa-arrow-left"></i> Back to Platform
        </a>

        <div class="footer-note">
            If you have any questions or did not receive the subscription access, please contact Support.
        </div>
    </div>
</div>

</body>
</html>
