<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mali Setu — Blogger Account Created</title>
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
            color: #b61315;
            text-align: center;
            letter-spacing: 1px;
            font-size: 24px;
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
        .credentials-box {
            background-color: #f3f3f3;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 16px;
            color: #333;
            border-left: 4px solid #b61315;
        }
        .credentials-row {
            margin-bottom: 10px;
        }
        .credentials-row strong {
            display: inline-block;
            width: 100px;
        }
        .button {
            display: block;
            background-color: #b61315;
            color: #fff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            margin: 25px auto;
            text-align: center;
            width: 200px;
        }
        .button:hover {
            background-color: #8b0000;
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
    <h2>Welcome to Mali Setu!</h2>
    <h4>Hi {{ $name }}, your Blogger account is ready 🎉</h4>

    <p>An administrator has created a Blogger account for you on <strong>Mali Setu</strong>. You now have complete access to write, manage, and publish articles on our blog portal.</p>

    <div class="credentials-box">
        <h5 style="margin-top:0; margin-bottom:15px; font-size:16px; color:#b61315; font-weight:bold;">Your Login Credentials</h5>
        <div class="credentials-row">
            <strong>Email:</strong> {{ $email }}
        </div>
        <div class="credentials-row">
            <strong>Password:</strong> <span style="font-family: monospace; background:#fff; padding:2px 6px; border-radius:4px; font-weight:bold;">{{ $password }}</span>
        </div>
        <div class="credentials-row">
            <strong>Category:</strong> {{ $category }}
        </div>
    </div>

    <p style="text-align: center;">
        <a href="{{ url('/login') }}" class="button">Log In to Your Account</a>
    </p>

    <p>Please log in using these credentials. Once logged in, you will be directed straight to the blog portal where you can write articles and view other publications.</p>

    <p>If you have any questions, feel free to reply to this email.</p>

    <p>Warm regards,<br>
    <strong>Mali Setu Team</strong></p>

    <div class="footer">
        &copy; {{ date('Y') }} Mali Setu. All rights reserved.
    </div>
</div>
</body>
</html>
