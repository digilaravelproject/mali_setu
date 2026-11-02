<!doctype html><html><body style="font-family:Arial,sans-serif;color:#222">
<div style="max-width:620px;margin:auto;padding:28px;border:1px solid #eee;border-radius:14px">
    <h2 style="color:#9b0056">You are registered!</h2>
    <h3>{{ $webinar->title }}</h3>
    <p>{{ $webinar->description }}</p>
    <p><strong>Date &amp; time:</strong> {{ $webinar->starts_at->format('d M Y, h:i A') }}</p>
    <p><strong>Duration:</strong> {{ $webinar->duration_minutes }} minutes</p>
    <p><a href="{{ $webinar->agora_link }}" style="display:inline-block;padding:12px 22px;background:#b00062;color:#fff;text-decoration:none;border-radius:8px">Join Webinar</a></p>
    <p style="color:#666;font-size:13px">The join link becomes useful at the scheduled webinar time.</p>
</div></body></html>
