<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2>Hello, {{ $member->first_name }}!</h2>
    <p>Thank you for your payment. Here are the official details of your transaction:</p>
    
    <table border="0" cellpadding="8" cellspacing="0" style="width: 100%; max-width: 450px; background: #f9f9f9; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 6px;">
        <tr>
            <td><strong>Receipt Number:</strong></td>
            <td>#INV-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td><strong>Amount Paid:</strong></td>
            <td><span style="font-size: 1.1em; color: #16a34a; font-weight: bold;">₱{{ number_format($payment->amount, 2) }}</span></td>
        </tr>
        <tr>
            <td><strong>Category:</strong></td>
            <td>{{ ucwords(str_replace('_', ' ', $payment->category)) }}</td>
        </tr>
        <tr>
            <td><strong>Date & Time:</strong></td>
            <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('F d, Y h:i A') }}</td>
        </tr>
    </table>

    <p>If you have any questions regarding your account balance or membership status, please reach out to the front desk.</p>
    
    <p style="margin-top: 30px; font-size: 0.85em; color: #6b7280;">
        Automated receipt generated from your Gym Management System via Gmail SMTP.
    </p>
</body>
</html>