<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Password Reset OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #ffffff;
            max-width: 480px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        h2 {
            color: #333;
            text-align: center;
            letter-spacing: 2px;
            font-size: 28px;
        }
        p {
            color: #555;
            font-size: 16px;
            line-height: 1.6;
        }
        .otp-box {
            background-color: #f3f3f3;
            text-align: center;
            padding: 15px 0;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 4px;
            color: #0d6efd;
        }
        .footer {
            font-size: 13px;
            color: #888;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Password Reset OTP</h2>
    <p>Hello,</p>
    <p>We received a request to reset your password. Use the OTP below to continue:</p>

    <div class="otp-box">
        {{ $otp }}
    </div>

    <p>This OTP is valid for <strong>10 minutes</strong>. If you didn’t request this, please ignore this email.</p>

    <p>Thank you,<br>{{ config('app.name') }}</p>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>
</body>
</html>
