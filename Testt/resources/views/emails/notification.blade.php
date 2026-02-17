<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? ($notification->title ?? config('app.name').' Notification') }}</title>
    <style>
        /* Base Reset */
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* Layout */
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
            padding-bottom: 40px;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            padding: 40px 20px;
            text-align: center;
        }

        .logo {
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            margin: 0;
            text-transform: capitalize;
        }

        .tagline {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-top: 8px;
        }

        /* Content Body */
        .content {
            padding: 40px 35px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 12px;
        }

        .meta-badge {
            display: inline-block;
            background-color: #f1f5f9;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 15px;
        }

        h2 {
            color: #0f172a;
            font-size: 24px;
            line-height: 1.3;
            margin: 0 0 15px 0;
            font-weight: 700;
        }

        p {
            color: #475569;
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 20px 0;
        }

        /* The "Activity" Card */
        .highlight-box {
            background-color: #fcfaff;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #6366f1;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }

        .highlight-title {
            font-weight: 700;
            font-size: 13px;
            color: #6366f1;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }

        .highlight-text {
            font-size: 15px;
            color: #334155;
            line-height: 1.5;
        }

        /* Action Button */
        .btn-wrapper {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            background: #6366f1;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            display: inline-block;
            box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.39);
        }

        /* Footer */
        .footer {
            padding: 0 20px;
            text-align: center;
        }

        .support-text {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 20px;
        }

        .footer-links {
            font-size: 12px;
            color: #cbd5e1;
            border-top: 1px solid #e2e8f0;
            padding-top: 25px;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
        }

        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; margin: 0 !important; border-radius: 0 !important; }
            .content { padding: 30px 20px !important; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="logo">{{ config('app.name') }}</div>
                <div class="tagline">Businesses • Jobs • Matrimony • Community</div>
            </div>

            <div class="content">
                @if(!empty($greeting))
                    <div class="greeting">{{ $greeting }}</div>
                @else
                    <div class="greeting">Hi {{ $userName ?? ($notification->user->name ?? 'there') }},</div>
                @endif
                
                <h2>{{ $notification->title ?? ($subject ?? 'New Update Found') }}</h2>

                <div class="highlight-box">
                    <span class="highlight-title">Summary</span>
                    <div class="highlight-text">
                        {{ $notification->message ?? $message ?? 'There is a new activity waiting for your review on your account.' }}
                    </div>
                </div>

                @if(!empty($extraLines) && is_array($extraLines))
                    @foreach($extraLines as $line)
                        <p>{{ $line }}</p>
                    @endforeach
                @endif

                <p class="support-text">
                    Need help? <a href="#" style="color: #6366f1; text-decoration: none;">Contact Support</a> or visit our help center.
                </p>
            </div>
        </div>

        <div class="footer">
            <div class="footer-links">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                <p style="margin-top: 10px; font-size: 11px;">
                    Sent to {{ $notification->user->email ?? 'your email' }}. 
                    <a href="#">Unsubscribe</a> from these alerts.
                </p>
            </div>
        </div>
    </div>
</body>
</html>