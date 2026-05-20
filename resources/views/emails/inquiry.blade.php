<!DOCTYPE html>
<html lang="pl">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width"></head>
<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f5f5f7;padding:32px 16px">
<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)">
    <div style="background:{{ $inquiry->type === 'financing' ? '#0066ff' : '#1a1a1a' }};padding:24px 28px">
        <h1 style="margin:0;color:#fff;font-size:18px;font-weight:700">
            {{ $inquiry->type === 'financing' ? '💰 Zapytanie o finansowanie' : '✉️ Nowa wiadomość' }}
        </h1>
        @if($inquiry->car_title)
        <p style="margin:8px 0 0;color:rgba(255,255,255,.8);font-size:13px">{{ $inquiry->car_title }}</p>
        @endif
    </div>
    <div style="padding:28px">
        <table style="width:100%;border-collapse:collapse;font-size:14px">
            <tr>
                <td style="padding:10px 0;color:#6b7280;width:120px;vertical-align:top">Imię:</td>
                <td style="padding:10px 0;font-weight:600;color:#1a1a1a">{{ $inquiry->name }}</td>
            </tr>
            <tr>
                <td style="padding:10px 0;color:#6b7280;border-top:1px solid #f0f0f2;vertical-align:top">Telefon:</td>
                <td style="padding:10px 0;font-weight:600;color:#1a1a1a;border-top:1px solid #f0f0f2">
                    <a href="tel:{{ $inquiry->phone }}" style="color:#0066ff;text-decoration:none">{{ $inquiry->phone }}</a>
                </td>
            </tr>
            @if($inquiry->email)
            <tr>
                <td style="padding:10px 0;color:#6b7280;border-top:1px solid #f0f0f2;vertical-align:top">E-mail:</td>
                <td style="padding:10px 0;font-weight:600;color:#1a1a1a;border-top:1px solid #f0f0f2">
                    <a href="mailto:{{ $inquiry->email }}" style="color:#0066ff;text-decoration:none">{{ $inquiry->email }}</a>
                </td>
            </tr>
            @endif
            @if($inquiry->message)
            <tr>
                <td style="padding:10px 0;color:#6b7280;border-top:1px solid #f0f0f2;vertical-align:top">Wiadomość:</td>
                <td style="padding:10px 0;color:#1a1a1a;border-top:1px solid #f0f0f2;line-height:1.6">{!! nl2br(e($inquiry->message)) !!}</td>
            </tr>
            @endif
        </table>
    </div>
    <div style="padding:16px 28px;background:#f9fafb;border-top:1px solid #f0f0f2;font-size:11px;color:#9ca3af;text-align:center">
        CertiCars.pl · {{ now()->format('d.m.Y H:i') }}
    </div>
</div>
</body>
</html>
