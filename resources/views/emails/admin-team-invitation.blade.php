<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Mali Setu Team Account</title></head>
<body style="margin:0;background:#f5f6f8;font-family:Arial,sans-serif;color:#374151">
<div style="max-width:560px;margin:32px auto;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,.08)">
    <div style="background:#84144f;color:#fff;padding:28px;text-align:center"><h1 style="margin:0;font-size:25px">Welcome to the Mali Setu Admin Team</h1></div>
    <div style="padding:30px">
        <p>Hello {{ $member->first_name }} {{ $member->last_name }},</p>
        <p>An administrator has created a team account for you. Use the credentials below on the existing admin login page.</p>
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:18px;margin:22px 0">
            <p style="margin:0 0 10px"><strong>Username:</strong> {{ $member->user->email }}</p>
            <p style="margin:0"><strong>Password:</strong> {{ $plainPassword }}</p>
        </div>
        <p style="text-align:center"><a href="{{ route('admin.login') }}" style="display:inline-block;background:#84144f;color:#fff;text-decoration:none;padding:12px 24px;border-radius:8px">Open Admin Login</a></p>
        <p style="font-size:13px;color:#6b7280;margin-top:24px">Your panel will show only the functionality assigned to you. Keep these credentials private.</p>
    </div>
</div>
</body>
</html>
