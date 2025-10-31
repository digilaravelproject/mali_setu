<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Changed Successfully</title>
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
            letter-spacing: 1px;
            font-size: 26px;
            margin-bottom: 10px;
        }
        p {
            color: #555;
            font-size: 16px;
            line-height: 1.6;
            margin: 8px 0;
        }
        .password-box {
            background-color: #f3f3f3;
            text-align: center;
            padding: 15px 0;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
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
    <h2>Password Changed Successfully</h2>

    <p>Hello <strong>{{ $name }}</strong>,</p>

    <p>Your password for <strong>{{ $appName }}</strong> has been changed successfully.</p>

    <p>Please use the new password below to log in:</p>

    <div class="password-box">
        {{ $newPassword }}
    </div>

    <p>For your security, please keep this password confidential and do not share it with anyone.</p>

    <p>Thank you,<br><strong>{{ $appName }}</strong> Team</p>

    <div class="footer">
        &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
    </div>
</div>
</body>
</html>
