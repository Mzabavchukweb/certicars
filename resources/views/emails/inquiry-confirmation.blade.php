<!DOCTYPE html>
<html lang="pl">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width"></head>
<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#f5f5f7;padding:32px 16px">
<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.08)">
    <div style="background:#1a1a1a;padding:24px 28px">
        <h1 style="margin:0;color:#fff;font-size:18px;font-weight:700">Dziękujemy za wiadomość!</h1>
        @if($inquiry->car_title)
        <p style="margin:8px 0 0;color:rgba(255,255,255,.8);font-size:13px">{{ $inquiry->car_title }}</p>
        @endif
    </div>
    <div style="padding:28px">
        <p style="margin:0 0 20px;font-size:15px;color:#1a1a1a;line-height:1.6">
            Cześć <strong>{{ $inquiry->name }}</strong>, otrzymaliśmy Twoją wiadomość i odezwiemy się jak najszybciej.
        </p>
        <table style="width:100%;border-collapse:collapse;font-size:14px">
            <tr>
                <td style="padding:10px 0;color:#6b7280;width:120px;vertical-align:top">Telefon:</td>
                <td style="padding:10px 0;font-weight:600;color:#1a1a1a">{{ $inquiry->phone }}</td>
            </tr>
            @if($inquiry->message)
            <tr>
                <td style="padding:10px 0;color:#6b7280;border-top:1px solid #f0f0f2;vertical-align:top">Twoja wiadomość:</td>
                <td style="padding:10px 0;color:#1a1a1a;border-top:1px solid #f0f0f2;line-height:1.6">{!! nl2br(e($inquiry->message)) !!}</td>
            </tr>
            @endif
        </table>
        <p style="margin:24px 0 0;font-size:13px;color:#6b7280;line-height:1.6">
            W razie pytań zadzwoń do nas lub napisz na <a href="mailto:kontakt@certicars.pl" style="color:#0066ff;text-decoration:none">kontakt@certicars.pl</a>.
        </p>
    </div>
    <div style="padding:16px 28px;background:#f9fafb;border-top:1px solid #f0f0f2;font-size:11px;color:#9ca3af;text-align:center">
        CertiCars.pl · {{ now()->format('d.m.Y H:i') }}
    </div>
</div>
</body>
</html>
