<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to {{ config('app.name') }}</title>
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
            color: #0d6efd;
            text-align: center;
            letter-spacing: 1px;
            font-size: 28px;
            margin-bottom: 10px;
        }
        h4 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        p {
            color: #555;
            font-size: 16px;
            line-height: 1.6;
        }
        .welcome-box {
            background-color: #f3f3f3;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 18px;
            font-weight: 500;
            color: #333;
        }
        .button {
            display: inline-block;
            background-color: #0d6efd;
            color: #fff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px auto;
            text-align: center;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0b5ed7;
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
    <h2>Welcome!</h2>
    <h4>Hi {{ $name }}, we're glad to have you 🎉</h4>

    <p>Thank you for joining <strong>{{ config('app.name') }}</strong>! Your account has been successfully created.</p>

    <div class="welcome-box">
        Start exploring your dashboard and enjoy all the features we’ve built for you.
    </div>

    <p style="text-align: center;">
        <a href="{{ config('app.url') }}" class="button">Go to {{ config('app.name') }}</a>
    </p>

    <p>If you have any questions, feel free to reply to this email. Our team is always ready to help!</p>

    <p>Warm regards,<br>
    <strong>{{ config('app.name') }} Team</strong></p>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>
</body>
</html>
