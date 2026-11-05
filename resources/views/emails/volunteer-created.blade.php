<!doctype html>
<html lang="en"><head><meta charset="utf-8"><title>Volunteer account</title></head>
<body style="font-family:Arial,sans-serif;background:#f5f5f5;padding:24px;color:#222">
<div style="max-width:560px;margin:auto;background:#fff;border-radius:12px;padding:28px">
    <h2 style="color:#84144f">Welcome to Mali Setu</h2>
    <p>Hello {{ $user->name }}, your volunteer account was created by the administrator.</p>
    <table style="width:100%;border-collapse:collapse">
        <tr><td style="padding:8px;border-bottom:1px solid #eee">Name</td><td style="padding:8px;border-bottom:1px solid #eee"><strong>{{ $user->name }}</strong></td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee">Email</td><td style="padding:8px;border-bottom:1px solid #eee">{{ $user->email }}</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee">Mobile</td><td style="padding:8px;border-bottom:1px solid #eee">{{ $user->phone }}</td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee">Temporary password</td><td style="padding:8px;border-bottom:1px solid #eee"><strong>{{ $temporaryPassword }}</strong></td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee">Referral code</td><td style="padding:8px;border-bottom:1px solid #eee"><strong>{{ $user->reffral_code }}</strong></td></tr>
        <tr><td style="padding:8px;border-bottom:1px solid #eee">Commission</td><td style="padding:8px;border-bottom:1px solid #eee">{{ $commissionPoints }} points per referral</td></tr>
        <tr><td style="padding:8px">Status</td><td style="padding:8px">{{ ucfirst($user->status) }}</td></tr>
    </table>
    <p style="margin-top:22px">Please sign in and change your password after your first login.</p>
    <p>Regards,<br><strong>Mali Setu Team</strong></p>
</div></body></html>
