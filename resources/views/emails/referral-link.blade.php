<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Mali Setu referral invitation</title></head>
<body style="font-family:Arial,sans-serif;background:#f5f5f5;padding:24px;color:#222">
<div style="max-width:560px;margin:auto;background:#fff;border-radius:12px;padding:28px">
    <h2 style="color:#84144f;margin-top:0">Join Mali Setu</h2>
    <p>Hello {{ $recipientName ?: 'there' }},</p>
    <p><strong>{{ $sender->name }}</strong> invited you to join Mali Setu.</p>
    @if($personalMessage)<div style="margin:18px 0;padding:14px;background:#faf2f7;border-left:4px solid #84144f">{{ $personalMessage }}</div>@endif
    <p style="text-align:center;margin:28px 0"><a href="{{ $referralLink }}" style="display:inline-block;background:#84144f;color:#fff;text-decoration:none;padding:12px 22px;border-radius:8px;font-weight:bold">Register with referral link</a></p>
    <p style="font-size:13px;color:#666">Referral code: <strong>{{ $sender->reffral_code }}</strong></p>
    <p style="font-size:13px;color:#666;word-break:break-all">If the button does not work, open: <a href="{{ $referralLink }}">{{ $referralLink }}</a></p>
    <p>Regards,<br><strong>Mali Setu Team</strong></p>
</div>
</body>
</html>
