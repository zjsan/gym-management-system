<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f5; padding: 20px; }
        .card { background: #ffffff; max-width: 450px; margin: 0 auto; padding: 24px; border-radius: 8px; text-align: center; }
        .qr-container { margin: 20px 0; }
        .code { font-family: monospace; font-size: 18px; font-weight: bold; color: #374151; }
        .qr-image { width: 200px; height: 200px; display: block; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Welcome to the Gym, {{ $member->first_name }}!</h2>
        <p>Here is your digital access badge for entry at the gym front desk scanner.</p>
        
        <div class="qr-container">
            <!-- Rendered securely as a base64 PNG image -->
            <img src="{{ $qrCodeDataUri }}" alt="Member QR Code" class="qr-image">
        </div>

        <p class="code">Badge ID: {{ $member->membership_no }}</p>
        <p><small>Present this QR code on your phone at the front desk scanner whenever you arrive for fast, automated attendance check-in.</small></p>
    </div>
</body>
</html>