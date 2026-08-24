<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f5; padding: 20px; }
        .card { background: #ffffff; max-width: 450px; margin: 0 auto; padding: 24px; border-radius: 8px; text-align: center; }
        .qr-container { margin: 20px 0; display: inline-block; }
        .code { font-family: monospace; font-size: 18px; font-weight: bold; color: #374151; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Hello, {{ $member->first_name }}!</h2>
        <p>Here is your digital access badge for entry at the gym front desk scanner.</p>
        
        <div class="qr-container" style="display: inline-block; width: 220px; height: 220px; margin: 20px auto;">
             {!! $qrSvg !!}
        </div>

        <p class="code">Badge ID: {{ $member->qr_token ?? $member->member_code }}</p>
        <p><small>Show this QR code on your mobile phone upon entry.</small></p>
    </div>
</body>
</html>